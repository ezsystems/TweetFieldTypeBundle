<?php
/**
 * File containing the Tweet FieldType Type class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet;

use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\SPI\Persistence\Content\FieldValue as PersistenceValue;
use eZ\Publish\Core\FieldType\Value as CoreValue;
use eZ\Publish\SPI\FieldType\Value as SPIValue;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use EzSystems\TweetFieldTypeBundle\Twitter\TwitterClientInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;

class Type extends FieldType implements FieldValueFormMapperInterface
{
    /** @var TwitterClientInterface */
    protected $twitterClient;

    /**
     * @var \eZ\Publish\API\Repository\FieldTypeService
     */
    private $fieldTypeService;

    protected $validatorConfigurationSchema = array(
        'TweetUrlValidator' => array(),
        'TweetAuthorValidator' => array(
            'AuthorList' => array(
                'type' => 'array',
                'default' => array()
            )
        )
    );
    public function __construct( TwitterClientInterface $twitterClient, FieldTypeService $fieldTypeService )
    {
        $this->twitterClient = $twitterClient;
        $this->fieldTypeService = $fieldTypeService;
    }

    public function getFieldTypeIdentifier()
    {
        return 'eztweet';
    }

    public function getName( SPIValue $value )
    {
        if ( !preg_match( '#^https?://twitter\.com/([^/]+/status/[0-9]+)$#', (string)$value, $matches ) )
            return '';

        return str_replace( '/', '-', $matches[1] );
    }

    protected function getSortInfo( CoreValue $value )
    {
        return (string)$value->url;
    }

    protected function createValueFromInput( $inputValue )
    {
        if ( is_string( $inputValue ) )
        {
            $inputValue = new Value( array( 'url' => $inputValue ) );
        }

        return $inputValue;
    }

    protected function checkValueStructure( CoreValue $value )
    {
        if ( !is_string( $value->url ) )
        {
            throw new InvalidArgumentType(
                '$value->text',
                'string',
                $value->text
            );
        }
    }

    public function getEmptyValue()
    {
        return new Value;
    }

    public function fromHash( $hash )
    {
        if ( $hash === null )
        {
            return $this->getEmptyValue();
        }
        return new Value( $hash );
    }

    /**
     * @param $value Value
     */
    public function toHash( SPIValue $value )
    {
        if ( $this->isEmptyValue( $value ) )
        {
            return null;
        }
        return array(
            'url' => $value->url,
            'authorUrl' => $value->authorUrl,
            'contents' => $value->contents
        );
    }

    /**
     * @param Value $value
     *
     * @return PersistenceValue
     */
    public function toPersistenceValue( SPIValue $value )
    {
        if ( $value === null )
        {
            return new PersistenceValue(
                array(
                    "data" => null,
                    "externalData" => null,
                    "sortKey" => null,
                )
            );
        }

        if ( $value->url )
        {
            $value->contents = $this->twitterClient->getEmbed( $value->url );
        }

        return new PersistenceValue(
            array(
                "data" => $this->toHash( $value ),
                "sortKey" => $this->getSortInfo( $value ),
            )
        );
    }

    /**
     * Converts a persistence $fieldValue to a Value
     *
     * This method builds a field type value from the $data and $externalData properties.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\FieldValue $fieldValue
     *
     * @return \EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet\Value
     */
    public function fromPersistenceValue( PersistenceValue $fieldValue )
    {
        if ( $fieldValue->data === null )
        {
            return $this->getEmptyValue();
        }

        return new Value( $fieldValue->data );
    }

    public function validateValidatorConfiguration( $validatorConfiguration )
    {
        $validationErrors = array();

        foreach ( $validatorConfiguration as $validatorIdentifier => $constraints )
        {
            // Report unknown validators
            if ( $validatorIdentifier == 'TweetAuthorValidator' )
            {
                // Validate arguments from TweetAuthorValidator
                if ( !isset( $constraints['AuthorList'] ) || !is_array( $constraints['AuthorList'] ) )
                {
                    $validationErrors[] = new ValidationError( "Missing or invalid AuthorList argument" );
                    continue;
                }

                foreach ( $constraints['AuthorList'] as $authorName )
                {
                    if ( !preg_match( '/^[a-z0-9_]{1,15}$/i', $authorName ) )
                    {
                        $validationErrors[] = new ValidationError( "Invalid twitter username " );
                    }
                }
            } elseif ( $validatorIdentifier == 'TweetUrlValidator' )
            {

            }
            else
            {
                $validationErrors[] = new ValidationError( "Validator '$validatorIdentifier' is unknown" );
            }
        }

        return $validationErrors;
    }

    public function validate( FieldDefinition $fieldDefinition, SPIValue $fieldValue )
    {
        $errors = array();

        if ( $this->isEmptyValue( $fieldValue ) )
        {
            return $errors;
        }

        // Tweet Url validation
        if ( !preg_match( '#^https?://twitter.com/([^/]+)/status/[0-9]+$#', $fieldValue->url, $m ) )
            $errors[] = new ValidationError( "Invalid twitter status url %url%", null, array( $fieldValue->url ) );

        $validatorConfiguration = $fieldDefinition->getValidatorConfiguration();
        if ( isset( $validatorConfiguration['TweetAuthorValidator'] ) && !empty( $validatorConfiguration['TweetAuthorValidator'] ) )
        {
            if ( !in_array( $m[1], $validatorConfiguration['TweetAuthorValidator']['AuthorList'] ) )
            {
                $errors[] = new ValidationError(
                    "Twitter user %user% is not in the approved author list",
                    null,
                    array( $m[1] )
                );
            }
        }

        return $errors;
    }

    /**
     * Maps Field form to current FieldType.
     * Allows to add form fields for content edition.
     *
     * @param FormInterface $form Form for the current Field.
     * @param FieldData $data Underlying data for current Field form.
     */
    public function mapFieldValueForm(FormInterface $form, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $form->getConfig();
        $names = $fieldDefinition->getNames();
        $label = $fieldDefinition->getName($formConfig->getOption('languageCode')) ?: reset($names);

        $form->add(
            'value',
            TextType::class,
            [
                'required' => $data->fieldDefinition->isRequired,
                'label' => $label,
                'property_path' => 'value.url',
            ]
        );
    }
}
