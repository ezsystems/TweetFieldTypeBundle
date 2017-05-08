<?php
/**
 * File containing the Twitter Client.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\TweetFieldTypeBundle\Twitter;

class TwitterClient implements TwitterClientInterface
{
    public function getEmbed($statusUrl)
    {
        $parts = explode('/', $statusUrl);

        if (isset($parts[5])) {
            $response = file_get_contents(
                sprintf(
                    'https://api.twitter.com/1/statuses/oembed.json?id=%s&align=center',
                    $parts[5]
                )
            );


            $data = json_decode($response, true);
            return $data['html'];
        }

        return '';
    }

    public function getAuthor($statusUrl)
    {
        return substr(
            $statusUrl,
            0,
            strpos($statusUrl, '/status/')
        );
    }
}
