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
     * @link http://cubiq.org/the-perfect-php-clean-url-generator
     * @param         $string
     * @param  array  $replace
     * @param  string $delimiter
     * @return mixed
     */
    public static function generate($string, $replace = array(), $delimiter = '-')
    {

        if (!extension_loaded('iconv')) {
            throw new \Phalcon\Exception('iconv module not loaded');
        }

        // Save the old locale and set the new locale to UTF-8
        $oldLocale = setlocale(LC_ALL, 'en_US.UTF-8');

        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);

        if (!empty($replace)) {
            $clean = str_replace((array) $replace, ' ', $clean);
        }

        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower($clean);
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
        $clean = trim($clean, $delimiter);

        // Revert back to the old locale
        setlocale(LC_ALL, $oldLocale);

        return $clean;
    }
}
