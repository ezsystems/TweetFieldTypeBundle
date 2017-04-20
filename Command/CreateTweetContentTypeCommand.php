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

class CreateTweetContentTypeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ezsystems:tweet-fieldtype:create-contenttype')
            ->setDescription("Creates a new Content Type with a Tweet field");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->getContainer()->get('ezpublish.api.repository');
        $userService = $repository->getUserService();
        $permissionResolver = $repository->getPermissionResolver();
        $user = $userService->loadUserByLogin('admin');
        $permissionResolver->setCurrentUserReference($user);

        $contentTypeService = $repository->getContentTypeService();

        $contentTypeGroup = $contentTypeService->loadContentTypeGroupByIdentifier('Content');

        // Content type create struct
        $createStruct = $contentTypeService->newContentTypeCreateStruct('tweet');
        $createStruct->mainLanguageCode = 'eng-GB';
        $createStruct->nameSchema = '<tweet>';
        $createStruct->names = array(
            'eng-GB' => 'Tweet'
        );
        $createStruct->descriptions = array(
            'eng-GB' => 'Reference to a twitter post',
        );

        // Tweet FieldDefinition
        $tweetFieldDefinitionCreateStruct = $contentTypeService->newFieldDefinitionCreateStruct('tweet', 'eztweet');
        $tweetFieldDefinitionCreateStruct->names = array('eng-GB' => 'Tweet');
        $tweetFieldDefinitionCreateStruct->descriptions = array('eng-GB' => 'The tweet');
        $tweetFieldDefinitionCreateStruct->fieldGroup = 'content';
        $tweetFieldDefinitionCreateStruct->position = 10;
        $tweetFieldDefinitionCreateStruct->isTranslatable = true;
        $tweetFieldDefinitionCreateStruct->isRequired = true;
        $tweetFieldDefinitionCreateStruct->isSearchable = false;

        // Add the field definition to the type create struct
        $createStruct->addFieldDefinition($tweetFieldDefinitionCreateStruct);

        try {
            $contentTypeDraft = $contentTypeService->createContentType($createStruct, array($contentTypeGroup));
            $contentTypeService->publishContentTypeDraft($contentTypeDraft);
            $contentType = $contentTypeService->loadContentTypeByIdentifier('tweet');
            $output->writeln("Created ContentType 'tweet' with ID {$contentType->id}");
        } catch (\eZ\Publish\API\Repository\Exceptions\InvalidArgumentException $e) {
            $output->writeln("An error occured creating the content type: " . $e->getMessage());
        }
    }
}
