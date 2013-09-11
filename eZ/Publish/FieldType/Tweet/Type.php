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
    protected $validatorConfigurationSchema = array();

    public function getFieldTypeIdentifier()
    {
    }

    public function getName( SPIValue $value )
    {
    }

    protected function getSortInfo( CoreValue $value )
    {
    }

    protected function createValueFromInput( $inputValue )
    {
    }

    protected function checkValueStructure( CoreValue $value )
    {
    }

    public function getEmptyValue()
    {
    }

    public function fromHash( $hash )
    {
    }

    /**
     * @param $value Value
     */
    public function toHash( SPIValue $value )
    {
    }

    public function validateValidatorConfiguration( $validatorConfiguration )
    {
    }

    public function validate( FieldDefinition $fieldDefinition, SPIValue $fieldValue )
    {
    }
}
