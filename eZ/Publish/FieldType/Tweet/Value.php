<?php
/**
 * File containing the Tweet FieldType Value class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet;

use eZ\Publish\Core\FieldType\Value as BaseValue;

class Value extends BaseValue
{
    /**
     * Tweet URL on twitter.com.
     *
     * @var string
     */
    public $url;

    /**
     * Author's Twitter URL (https://twitter.com/UserName)
     *
     * @var string
     */
    public $authorUrl;

    /**
     * The tweet's embed HTML
     *
     * @var string
     */
    public $contents;

    public function __construct($arg = [])
    {
        if (!is_array($arg)) {
            $arg = ['url' => $arg];
        }

        parent::__construct($arg);
    }

    public function __toString()
    {
        return (string)$this->url;
    }
}
