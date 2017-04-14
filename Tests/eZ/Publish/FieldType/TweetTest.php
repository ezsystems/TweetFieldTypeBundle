<?php
/**
 * File containing the Tweet FieldType Test class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\TweetFieldTypeBundle\Tests\eZ\Publish\FieldType;

use eZ\Publish\Core\FieldType\Tests\FieldTypeTest;
use EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet\Type as TweetType;
use EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet\Value as TweetValue;

class TweetTest extends FieldTypeTest
{
    /**
     * @var \EzSystems\TweetFieldTypeBundle\Twitter\TwitterClientInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $twitterClientMock;

    protected function createFieldTypeUnderTest()
    {
        return new TweetType($this->getTwitterClientMock());
    }

    protected function getValidatorConfigurationSchemaExpectation()
    {
        return array(
            'TweetUrlValidator' => array(),
            'TweetAuthorValidator' => array(
                'AuthorList' => array(
                    'type' => 'array',
                    'default' => array()
                )
            )
        );
    }

    protected function getSettingsSchemaExpectation()
    {
        return array();
    }

    protected function getEmptyValueExpectation()
    {
        return new TweetValue;
    }

    public function provideInvalidInputForAcceptValue()
    {
        return array(
            array(
                1,
                'eZ\\Publish\\Core\\Base\\Exceptions\\InvalidArgumentException',
            ),
            array(
                new \stdClass,
                'eZ\\Publish\\Core\\Base\\Exceptions\\InvalidArgumentException'
            ),
        );
    }

    public function provideValidInputForAcceptValue()
    {
        return array(
            array(
                'https://twitter.com/user/status/123456789',
                new TweetValue( array( 'url' => 'https://twitter.com/user/status/123456789' ) ),
            ),
            array(
                new TweetValue(
                    array(
                        'url' => 'https://twitter.com/user/status/123456789'
                    )
                ),
                new TweetValue(
                    array(
                        'url' => 'https://twitter.com/user/status/123456789'
                    )
                ),
            ),
            array(
                new TweetValue(
                    array(
                        'url' => 'https://twitter.com/user/status/123456789',
                        'authorUrl' => 'https://twitter.com/user',
                        'contents' => '<blockquote />'
                    )
                ),
                new TweetValue(
                    array(
                        'url' => 'https://twitter.com/user/status/123456789',
                        'authorUrl' => 'https://twitter.com/user',
                        'contents' => '<blockquote />'
                    )
                )
            )
        );
    }

    public function provideInputForToHash()
    {
        return array(
            array(
                new TweetValue,
                null
            ),
            array(
                new TweetValue( array( 'url' => 'https://twitter.com/user/status/123456789' ) ),
                array(
                    'url' => 'https://twitter.com/user/status/123456789',
                    'authorUrl' => '',
                    'contents' => ''
                )
            ),
            array(
                new TweetValue(
                    array(
                        'url' => 'https://twitter.com/user/status/123456789',
                        'authorUrl' => 'https://twitter.com/user',
                        'contents' => '<blockquote />'
                    )
                ),
                array(
                    'url' => 'https://twitter.com/user/status/123456789',
                    'authorUrl' => 'https://twitter.com/user',
                    'contents' => '<blockquote />'
                )
            )
        );
    }

    public function provideInputForFromHash()
    {
        return array(
            array(
                array(), new TweetValue
            ),
            array(
                array( 'url' => 'https://twitter.com/user/status/123456789' ),
                new TweetValue( array( 'url' => 'https://twitter.com/user/status/123456789' ) ),
            ),
            array(
                array(
                    'url' => 'https://twitter.com/user/status/123456789',
                    'authorUrl' => 'https://twitter.com/user',
                    'contents' => '<blockquote />'
                ),
                new TweetValue(
                    array(
                        'url' => 'https://twitter.com/user/status/123456789',
                        'authorUrl' => 'https://twitter.com/user',
                        'contents' => '<blockquote />'
                    )
                ),
            )
        );
    }

    protected function provideFieldTypeIdentifier()
    {
        return 'eztweet';
    }

    public function provideDataForGetName()
    {
        return array(
            array($this->getEmptyValueExpectation(), ''),
            array(new TweetValue('https://twitter.com/user/status/123456789'), 'user-123456789'),
        );
    }

    /**
     * @return \EzSystems\TweetFieldTypeBundle\Twitter\TwitterClientInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getTwitterClientMock()
    {
        if (!isset($this->twitterClientMock)) {
            $this->twitterClientMock = $this->getMock('EzSystems\\TweetFieldTypeBundle\\Twitter\\TwitterClientInterface');
        }
        return $this->twitterClientMock;
    }

    public function provideValidDataForValidate()
    {
        // @todo implement me
        return array();
    }

    public function provideInvalidDataForValidate()
    {
        // @todo implement me
        return array();
    }
}
