<?php
/**
 * File containing the CreateTweetContentType class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\TweetFieldTypeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateTweetContentCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName( 'ezsystems:tweet-fieldtype:create-content' )
            ->setDescription( "Creates a new Content of the tweet type" );
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $repository = $this->getContainer()->get( 'ezpublish.api.repository' );
        $repository->setCurrentUser(
            $repository->getUserService()->loadUserByLogin( 'admin' )
        );

        $contentService = $repository->getContentService();

        // Content create struct
        $createStruct = $contentService->newContentCreateStruct(
            $repository->getContentTypeService()->loadContentTypeByIdentifier( 'tweet' ),
            'eng-GB'
        );
        $createStruct->setField( 'tweet', 'https://twitter.com/bdunogier/status/435763219555037184', 'eng-GB' );

        try
        {
            $contentDraft = $contentService->createContent(
                $createStruct,
                array( $repository->getLocationService()->newLocationCreateStruct( 2 ) )
            );
            $content = $contentService->publishVersion( $contentDraft->versionInfo );
            $output->writeln( "Created Content 'tweet' with ID {$content->id}" );
        }
        catch ( \Exception $e )
        {
            $output->writeln( "An error occurred creating the content: " . $e->getMessage() );
            $output->writeln( $e->getTraceAsString() );
        }
    }
}
