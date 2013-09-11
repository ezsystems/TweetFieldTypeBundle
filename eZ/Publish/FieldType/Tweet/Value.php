<?php
/**
 * File containing the Tweet FieldType Value class.
 *
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet;

use eZ\Publish\Core\FieldType\Value as BaseValue;

class Value extends BaseValue
{
    /**
     * Tweet URL on twitter.com.
     * @var string
     */
    public $url;

    /**
     * Author's tweet URL (http://twitter.com/UserName)
     * @var string
     */
    public $authorUrl;

    /**
     * The tweet's embed HTML
     * @var string
     */
    public $contents;

    public function __construct( $arg = array() )
    {
        if ( !is_array( $arg ) )
            $arg = array( 'url' => $arg );

        parent::__construct( $arg );
    }

    public function __toString()
    {
        return (string)$this->url;
    }
}
