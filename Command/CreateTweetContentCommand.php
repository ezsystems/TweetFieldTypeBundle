<?php
/**
 * File containing the CreateTweetContentType class.
 *
 * @copyright Copyright (C) 2014 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
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
            $repository->getContentTypeService()->loadContentTypeByIdentifier( 'folder' ),
            'eng-GB'
        );
        $createStruct->setField( 'name', 'A folder', 'eng-GB' );

        try
        {
            $contentDraft = $contentService->createContent(
                $createStruct,
                array( $repository->getLocationService()->newLocationCreateStruct( 2 ) )
            );
            $content = $contentService->publishVersion( $contentDraft->versionInfo );
            $output->writeln( "Created Content 'folder' with ID {$content->id}" );
        }
        catch ( \Exception $e )
        {
            $output->writeln( "An error occured creating the content: " . $e->getMessage() );
            $output->writeln( $e->getTraceAsString() );
        }
    }
}
