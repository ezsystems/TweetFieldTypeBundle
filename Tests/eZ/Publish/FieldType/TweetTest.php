<?php
/**
 * File containing the Tweet FieldType Test class.
 *
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */
namespace EzSystems\TweetFieldTypeBundle\Tests\eZ\Publish\FieldType;

use eZ\Publish\Core\FieldType\Tests\FieldTypeTest;
use EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet\Type as TweetType;
use EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet\Value as TweetValue;

class TweetTest extends FieldTypeTest
{
    protected function createFieldTypeUnderTest()
    {
        return new TweetType;
    }

    protected function getValidatorConfigurationSchemaExpectation()
    {
        return array();
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
}
