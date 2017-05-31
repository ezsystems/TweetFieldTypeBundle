<?php
/**
 * File containing the CreateTweetContentType class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
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
            ->setName('ezsystems:tweet-fieldtype:create-content')
            ->setDescription('Creates a new Content of the tweet type');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->getContainer()->get('ezpublish.api.repository');
        $userService = $repository->getUserService();
        $permissionResolver = $repository->getPermissionResolver();
        $user = $userService->loadUserByLogin('admin');
        $permissionResolver->setCurrentUserReference($user);

        $contentService = $repository->getContentService();

        // Content create struct
        $createStruct = $contentService->newContentCreateStruct(
            $repository->getContentTypeService()->loadContentTypeByIdentifier('tweet'),
            'eng-GB'
        );
        /* Thanks to:
         * https://twitter.com/Flutchman_Fride
         * https://twitter.com/comtocode
         * https://twitter.com/eZSystemsFR
         */
        $createStruct->setField('tweet', 'https://twitter.com/Flutchman_Fride/status/847423373940473856', 'eng-GB');

        try {
            $contentDraft = $contentService->createContent(
                $createStruct,
                [$repository->getLocationService()->newLocationCreateStruct(2)]
            );
            $content = $contentService->publishVersion($contentDraft->versionInfo);
            $output->writeln("Created Content 'tweet' with ID {$content->id}");
        } catch (\Exception $e) {
            $output->writeln('An error occurred creating the content: ' . $e->getMessage());
            $output->writeln($e->getTraceAsString());
        }
    }
}
