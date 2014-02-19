<?php
/**
 * File containing the Twitter Client interface.
 *
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */
namespace EzSystems\TweetFieldTypeBundle\Twitter;

interface TwitterClientInterface
{
    /**
     * Returns the embed version of a tweet from its $url
     * @param string $statusUrl
     * @return string
     */
    public function getEmbed( $statusUrl );
}
