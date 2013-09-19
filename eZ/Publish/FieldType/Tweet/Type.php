<?php
/**
 * File containing the Tweet FieldType Type class.
 *
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet;

use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\SPI\Persistence\Content\FieldValue as PersistenceValue;
use eZ\Publish\Core\FieldType\Value as CoreValue;
use eZ\Publish\SPI\FieldType\Value as SPIValue;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Validator\TweetUrl as TweetUrlValidator;

class Type extends FieldType
{
    protected $validatorConfigurationSchema = array(
        'TweetUrlValidator' => array(),
        'TweetAuthorValidator' => array(
            'AuthorList' => array(
                'type' => 'array',
                'default' => array()
            )
        )
    );

    public function getFieldTypeIdentifier()
    {
        return 'eztweet';
    }

    public function getName( SPIValue $value )
    {
        if ( !preg_match( '#^https?://twitter\.com/([^/]+/status/[0-9]+)$#', (string)$value->url, $matches ) )
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

        return new PersistenceValue(
            array(
                "data" => $value->url,
                "externalData" => array(
                    'authorUrl' => $value->authorUrl,
                    'contents' => $value->contents,
                ),
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
     * @return \eZ\Publish\Core\FieldType\Url\Value
     */
    public function fromPersistenceValue( PersistenceValue $fieldValue )
    {
        if ( $fieldValue->data === null )
        {
            return $this->getEmptyValue();
        }

        return new Value(
            array(
                'url' => $fieldValue->data,
                'authorUrl' => $fieldValue->externalData['authorUrl'],
                'contents' => $fieldValue->externalData['contents'],
            )
        );
    }

    public function validateValidatorConfiguration( $validatorConfiguration )
    {
        $validationErrors = array();

        foreach ( $validatorConfiguration as $validatorIdentifier => $constraints )
        {
            // Report unknown validators
            if ( !$validatorIdentifier != 'TweetAuthorValidator' )
            {
                $validationErrors[] = new ValidationError( "Validator '$validatorIdentifier' is unknown" );
                continue;
            }

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
}
