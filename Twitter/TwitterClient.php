<?php
/**
 * File containing the Twitter Client.
 *
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */
namespace EzSystems\TweetFieldTypeBundle\Twitter;

class TwitterClient implements TwitterClientInterface
{
    public function getEmbed( $statusUrl )
    {
        $parts = explode( '/', $statusUrl );

        $response = file_get_contents(
            sprintf(
                'https://api.twitter.com/1/statuses/oembed.json?id=%s&align=center',
                $parts[5]
            )
        );

        $data = json_decode( $response, true );
        return $data['html'];
    }

    public function getAuthor( $statusUrl )
    {
        return substr(
            $statusUrl,
            0,
            strpos( $statusUrl, '/status/' )
        );
    }
}
