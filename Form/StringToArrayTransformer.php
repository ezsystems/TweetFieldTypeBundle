<?php
/**
 * File containing the StringToArrayTransformer class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\TweetFieldTypeBundle\Form;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * DataTransformer that transforms array into comma-separated string and vice versa
 */
class StringToArrayTransformer implements DataTransformerInterface
{
    public function transform($array)
    {
        if ($array === null) {
            return '';
        }

        return implode(',', $array);
    }

    public function reverseTransform($string)
    {
        if (empty($string)) {
            return [];
        }

        $array = explode(',', $string);

        return array_map('trim', $array);
    }
}
