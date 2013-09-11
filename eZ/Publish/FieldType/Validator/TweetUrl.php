<?php

namespace EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Validator;

use eZ\Publish\Core\FieldType\Validator;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\Core\FieldType\Value;

class TweetUrl extends Validator
{
    public function validateConstraints( $constraints )
    {
        return array();
    }

    /**
     * Perform validation on $value.
     *
     * Will return true when all constraints are matched. If one or more
     * constraints fail, the method will return false.
     *
     * When a check against a constraint has failed, an entry will be added to the
     * $errors array.
     *
     * @param \eZ\Publish\Core\FieldType\Value|\Netgen\EzscIpAddressBundle\Core\FieldType\IpAddress\Value $value
     *
     * @return boolean
     */
    public function validate( Value $value )
    {
        $pattern = '#^https?://twitter.com/[^/]+/status/[0-9]+$#';

        if ( preg_match( $pattern, $value->url ) )
        {
            return true;
        }

        $this->errors[] = new ValidationError(
            "The value must be a valid twitter status url.",
            null,
            array()
        );

        return false;
    }
}
