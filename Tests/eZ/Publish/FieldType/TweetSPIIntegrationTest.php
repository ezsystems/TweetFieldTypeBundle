<?php
/**
 * File contains: EzSystems\TweetFieldTypeBundle\Tests\eZ\Publish\FieldType\TweetSPIIntegrationTest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\TweetFieldTypeBundle\Tests\eZ\Publish\FieldType;

use eZ\Publish\Core\FieldType;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Handler;
use eZ\Publish\SPI\Tests\FieldType\BaseIntegrationTest;
use EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet;
use EzSystems\TweetFieldTypeBundle\Twitter\TwitterClientInterface;

/**
 * SPI Integration test for legacy storage field types
 *
 * This abstract base test case is supposed to be the base for field type
 * integration tests. It basically calls all involved methods in the field type
 * ``Converter`` and ``Storage`` implementations. Fo get it working implement
 * the abstract methods in a sensible way.
 *
 * The following actions are performed by this test using the custom field
 * type:
 *
 * - Create a new content type with the given field type
 * - Load create content type
 * - Create content object of new content type
 * - Load created content
 * - Copy created content
 * - Remove copied content
 *
 * @group integration
 */
class TweetSPIIntegrationTest extends BaseIntegrationTest
{
    /**
     * @var \EzSystems\TweetFieldTypeBundle\Twitter\TwitterClientInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $twitterClientMock;

    /**
     * Get name of tested field type
     *
     * @return string
     */
    public function getTypeName()
    {
        return 'eztweet';
    }

    /**
     * Get handler with required custom field types registered
     *
     * @return Handler
     */
    public function getCustomHandler()
    {
        $fieldType = new Tweet\Type($this->getTwitterClientMock());
        $fieldType->setTransformationProcessor($this->getTransformationProcessor());

        return $this->getHandler(
            'eztweet',
            $fieldType,
            new Tweet\LegacyConverter(),
            new FieldType\NullStorage()
        );
    }

    /**
     * Returns the FieldTypeConstraints to be used to create a field definition
     * of the FieldType under test.
     *
     * @return \eZ\Publish\SPI\Persistence\Content\FieldTypeConstraints
     */
    public function getTypeConstraints()
    {
        return new Content\FieldTypeConstraints();
    }

    /**
     * Get field definition data values
     *
     * This is a PHPUnit data provider
     *
     * @return array
     */
    public function getFieldDefinitionData()
    {
        return array(
            array('fieldType', 'eztweet'),
            array('fieldTypeConstraints', new Content\FieldTypeConstraints()),
        );
    }

    /**
     * Get initial field value
     *
     * @return \eZ\Publish\SPI\Persistence\Content\FieldValue
     */
    public function getInitialValue()
    {
        return new Content\FieldValue(
            array(
                'data' => 'http://twitter.com/xxx/status/123545',
                'externalData' => null,
                'sortKey' => 'http://twitter.com/xxx/status/123545',
            )
        );
    }

    /**
     * Get update field value.
     *
     * Use to update the field
     *
     * @return \eZ\Publish\SPI\Persistence\Content\FieldValue
     */
    public function getUpdatedValue()
    {
        return new Content\FieldValue(
            array(
                'data' => 'http://twitter.com/yyyyy/status/54321',
                'externalData' => null,
                'sortKey' => 'http://twitter.com/yyyyy/status/54321',
            )
        );
    }

    /**
     * @return \EzSystems\TweetFieldTypeBundle\Twitter\TwitterClientInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getTwitterClientMock()
    {
        if (!isset($this->twitterClientMock)) {
            $this->twitterClientMock = $this->getMock(TwitterClientInterface::class);
        }

        return $this->twitterClientMock;
    }
}
