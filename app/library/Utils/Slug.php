<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2014 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file docs/LICENSE.txt.                        |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Utils;

/**
 * Transforms a string or part thereof using an ICU transliterator.
 */
class Slug
{
    /**
     * Creates a slug to be used for pretty URLs
     *
     * @param         $string
     * @param  string $delimiter
     * @return mixed
     */
    public static function generate($string, $delimiter = '-')
    {
        $string = transliterator_transliterate('Any-Latin; Latin-ASCII; [:Punctuation:] Remove; Lower()', $string);
        $string = preg_replace('/[-\s]+/', $delimiter, $string);
        return trim($string, $delimiter);
    }
}
