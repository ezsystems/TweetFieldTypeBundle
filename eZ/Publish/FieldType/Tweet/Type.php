<?php
/**
 * File containing the Tweet FieldType Type class.
 *
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet;

use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\SPI\Persistence\Content\FieldValue as PersistenceValue;
use eZ\Publish\Core\FieldType\Value as CoreValue;
use eZ\Publish\SPI\FieldType\Value as SPIValue;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Validator\TweetUrl as TweetUrlValidator;

class Type extends FieldType
{
    protected $validatorConfigurationSchema = array(
        'TweetUrlValidator' => array()
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
        return $this->getName( $value );
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
        );
    }

    public function toPersistenceValue( SPIValue $value )
    {
        if ( $value === null )
        {
            return new PersistenceValue(
                array(
                    "data" => null,
                    "sortKey" => null,
                )
            );
        }

        return new PersistenceValue(
            array(
                "data" => $value->url,
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
            )
        );
    }

    public function validateValidatorConfiguration( $validatorConfiguration )
    {
        $validationErrors = array();

        foreach ( $validatorConfiguration as $validatorIdentifier => $constraints )
        {
            if ( $validatorIdentifier !== 'TweetUrlValidator' )
            {
                $validationErrors[] = new ValidationError(
                    "Validator '%validator%' is unknown",
                    null,
                    array(
                        "validator" => $validatorIdentifier
                    )
                );

                continue;
            }

            $validator = new TweetUrlValidator();
            $validationErrors += $validator->validateConstraints( $constraints );
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

        $validatorConfiguration = $fieldDefinition->getValidatorConfiguration();
        $constraints = isset( $validatorConfiguration['IpAddressValidator'] ) ?
            $validatorConfiguration['IpAddressValidator'] :
            array();

        $validator = new TweetUrlValidator();
        $validator->initializeWithConstraints( $constraints );

        if ( !$validator->validate( $fieldValue ) )
            return $validator->getMessage();

        return array();
    }
}
