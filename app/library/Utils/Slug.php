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
 * Slug
 *
 * Transforms a string or part thereof using an ICU transliterator.
 */
class Slug
{
    /**
     * Creates a slug to be used for pretty URLs
     *
     * @param         $string
     * @param  string $delimiter
     * @return string
     */
    public static function generate($string, $delimiter = '-')
    {
        if (function_exists('transliterator_transliterate')) {
            $string = transliterator_transliterate('Any-Latin; Latin-ASCII; [:Punctuation:] Remove; Lower()', $string);
        } else {
            $string = mb_strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $string));
        }
        $string = preg_replace('/[-\s]+/', $delimiter, $string);
        $string = preg_replace('/^[-\s]+/', '', $string);
        $string = preg_replace('/[-\s]+$/', '', $string);
        return trim($string, $delimiter);
    }
}
