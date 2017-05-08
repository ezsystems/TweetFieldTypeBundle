<?php
/**
 * File containing the Twitter Client interface.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\TweetFieldTypeBundle\Twitter;

interface TwitterClientInterface
{
    /**
     * Returns the embed version of a tweet from its $url
     *
     * @param string $statusUrl
     *
     * @return string
     */
    public function getEmbed($statusUrl);
}
