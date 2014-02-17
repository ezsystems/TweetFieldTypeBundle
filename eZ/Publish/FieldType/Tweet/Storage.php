<?php
/**
 * File containing the Tweet FieldType Storage class.
 *
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */
namespace EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet;

use eZ\Publish\Core\FieldType\GatewayBasedStorage;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use EzSystems\TweetFieldTypeBundle\Twitter\ClientInterface as TwitterClientInterface;

class Storage extends GatewayBasedStorage
{
    /**
     * @var TwitterClient
     */
    private $twitterClient;

    public function __construct( array $gateways = array(), TwitterClientInterface $twitterClient )
    {
        $this->twitterClient = $twitterClient;
        parent::__construct( $gateways );
    }

    public function getFieldData( VersionInfo $versionInfo, Field $field, array $context )
    {
        if ( $field->value->data === null )
            return;

        $gateway = $this->getGateway( $context );
        $field->value->externalData = $gateway->getTweet( $field->value->data );
    }

    /**
     * Stores external data for a tweet.
     *
     * If the stored tweet doesn't exist in our external storage, we fetch the missing info
     * using the twitter client API.
     */
    public function storeFieldData( VersionInfo $versionInfo, Field $field, array $context )
    {
        if ( $field->value->data == null )
            return;

        $gateway = $this->getGateway( $context );

        // get embed data from twitter service here
        if ( !$field->value->externalData['contents'] )
        {
            // This tweet is already in our external storage,  we can reuse the data and return
            if ( ( $tweetData = $gateway->getTweet( $field->value->data ) ) != false )
            {
                $field->value->externalData = $tweetData;
                return;
            }

            $field->value->externalData['authorUrl'] = $this->twitterClient->getAuthor( $field->value->data );
            $field->value->externalData['contents'] = $this->twitterClient->getEmbed( $field->value->data );
        }

        $gateway->storeTweet(
            $field->value->data,
            $field->value->externalData['authorUrl'],
            $field->value->externalData['contents']
        );
    }

    public function deleteFieldData( VersionInfo $versionInfo, array $fieldIds, array $context )
    {
        $gateway = $this->getGateway( $context );
        foreach ( $fieldIds as $fieldId )
        {
            $gateway->deleteTweet( $fieldId, $versionInfo->versionNo );
        }
    }

    public function hasFieldData()
    {
        return true;
    }

    public function getIndexData( VersionInfo $versionInfo, Field $field, array $context )
    {

    }
}
