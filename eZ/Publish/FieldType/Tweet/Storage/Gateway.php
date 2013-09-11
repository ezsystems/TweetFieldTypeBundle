<?php
namespace EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet\Storage;

use eZ\Publish\Core\FieldType\StorageGateway;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use eZ\Publish\SPI\Persistence\Content\Field;

abstract class Gateway extends StorageGateway
{
    /**
     * Fetches a tweet from the database based on its URL
     *
     * @param string $url
     * @return array associative array with the tweet's properties (url, authorUrl, contents)
     */
    abstract public function getTweet( $url );

    /**
     * Stores a tweet in the database
     * @param string $url
     * @param string $authorUrl
     * @param string $contents
     *
     * @return void
     */
    abstract public function storeTweet( $url, $authorUrl, $contents );

    /**
     * Deletes the tweet referenced by $fieldId in $versionNo
     *
     * Will only delete if this field was the only one referencing the tweet.
     *
     * @param mixed $fieldId
     * @param int $versionNumber
     *
     * @return void
     */
    abstract public function deleteTweet( $fieldId, $versionNumber );
}

