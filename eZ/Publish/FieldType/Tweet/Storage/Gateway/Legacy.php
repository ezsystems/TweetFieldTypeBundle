<?php
/**
 * File containing the Legacy implementation of the Tweet FieldType storage gateway
 *
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */
namespace EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet\Storage\Gateway;

use EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet\Storage\Gateway;

class Legacy extends Gateway
{
    const TABLE = 'eztweet';

    public function getTweet( $url )
    {
        $dbHandler = $this->getConnection();

        $q = $dbHandler->createSelectQuery();
        $e = $q->expr;
        $q->select( "*" )
            ->from( $dbHandler->quoteTable( self::TABLE ) )
            ->where(
                $e->eq( "url", $q->bindValue( $url ) )
            );

        $statement = $q->prepare();
        $statement->execute();

        $rows = $statement->fetchAll( \PDO::FETCH_ASSOC );
        if ( count( $rows ) )
        {
            return array(
                'authorUrl' => $rows[0]['author_url'],
                'contents' => $rows[0]['contents']
            );
        }

        return false;
    }

    /**
     * Stores a tweet in the database
     *
     * @param string $url
     * @param string $authorUrl
     * @param string $contents
     *
     * @return void
     */
    public function storeTweet( $url, $authorUrl, $contents )
    {
        // we don't add the tweet if it already exists
        if ( $this->getTweet( $url ) !== false )
            return;

        $dbHandler = $this->getConnection();

        $q = $dbHandler->createInsertQuery();
        $q->insertInto(
            $dbHandler->quoteTable( self::TABLE )
        )->set(
            $dbHandler->quoteColumn( "url" ),
            $q->bindValue( $url )
        )->set(
            $dbHandler->quoteColumn( "author_url" ),
            $q->bindValue( $authorUrl )
        )->set(
            $dbHandler->quoteColumn( "contents" ),
            $q->bindValue( $contents )
        );

        $stmt = $q->prepare();
        $stmt->execute();
    }

    /**
     * Deletes the tweet referenced by $fieldId in $versionNo
     *
     * The method will first query the database for other ezcontentobject_attribute records that reference
     * the same tweet as $fieldId, and will only delete the entry if there are none.
     *
     * @param mixed $fieldId
     * @param int   $versionNumber
     *
     * @return void
     */
    public function deleteTweet( $fieldId, $versionNumber )
    {
        $dbHandler = $this->getConnection();


        // First, we get the URL
        $q = $dbHandler->createSelectQuery();
        $e = $q->expr;
        $q->select( 'data_text' )
            ->from( $dbHandler->quoteTable( 'ezcontentobject_attribute' ) )
            ->where(
                $e->eq( "id", $q->bindValue( $fieldId ) )
            );
        $statement = $q->prepare();
        $statement->execute();
        $url = $statement->fetchColumn();

        // We count how many rows other than $fieldId reference this tweet
        $q = $dbHandler->createSelectQuery();
        $q->select( 'COUNT(*)')
            ->from( $dbHandler->quoteTable( 'ezcontentobject_attribute' ) )
            ->where(
                $e->eq( 'sort_key_string', $q->bindValue( $url ) ),
                $e->neq( 'id', $q->bindValue( $fieldId ) )
            );

        $statement = $q->prepare();
        $statement->execute();

        // If other ezcontentobject_attribute row references this tweet, we can stop right here
        if ( (int)$statement->fetchColumn() > 0 )
            return;

        $q = $dbHandler->createDeleteQuery();
        $q->deleteFrom( self::TABLE )
            ->where(
                $q->expr->eq( 'url', $q->bindValue( $url ) )
            );
        $statement = $q->prepare();
        $statement->execute();
    }

    /**
     * Set database handler for this gateway
     *
     * @param mixed $dbHandler
     *
     * @return void
     * @throws \RuntimeException if $dbHandler is not an instance of
     *         {@link \eZ\Publish\Core\Persistence\Legacy\EzcDbHandler}
     */
    public function setConnection( $dbHandler )
    {
        // This obviously violates the Liskov substitution Principle, but with
        // the given class design there is no sane other option. Actually the
        // dbHandler *should* be passed to the constructor, and there should
        // not be the need to post-inject it.
        if ( ! ( $dbHandler instanceof \eZ\Publish\Core\Persistence\Legacy\EzcDbHandler ) )
        {
            throw new \RuntimeException( "Invalid dbHandler passed" );
        }

        $this->dbHandler = $dbHandler;
    }

    /**
     * Returns the active connection
     *
     * @throws \RuntimeException if no connection has been set, yet.
     *
     * @return \eZ\Publish\Core\Persistence\Legacy\EzcDbHandler
     */
    protected function getConnection()
    {
        if ( $this->dbHandler === null )
        {
            throw new \RuntimeException( "Missing database connection." );
        }
        return $this->dbHandler;
    }
}
