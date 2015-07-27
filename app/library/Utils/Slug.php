<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2015 Phalcon Team and contributors                  |
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
    protected $charMap = [
        'à' => 'a',  'ô' => 'o',  'ď' => 'd',  'ḟ' => 'f',  'ë' => 'e',  'š' => 's',  'ơ' => 'o',
        'ß' => 'ss', 'ă' => 'a',  'ř' => 'r',  'ț' => 't',  'ň' => 'n',  'ā' => 'a',  'ķ' => 'k',
        'ŝ' => 's',  'ỳ' => 'y',  'ņ' => 'n',  'ĺ' => 'l',  'ħ' => 'h',  'ṗ' => 'p',  'ó' => 'o',
        'ú' => 'u',  'ě' => 'e',  'é' => 'e',  'ç' => 'c',  'ẁ' => 'w',  'ċ' => 'c',  'õ' => 'o',
        'ṡ' => 's',  'ø' => 'o',  'ģ' => 'g',  'ŧ' => 't',  'ș' => 's',  'ė' => 'e',  'ĉ' => 'c',
        'ś' => 's',  'î' => 'i',  'ű' => 'u',  'ć' => 'c',  'ę' => 'e',  'ŵ' => 'w',  'ṫ' => 't',
        'ū' => 'u',  'č' => 'c',  'ö' => 'o',  'è' => 'e',  'ŷ' => 'y',  'ą' => 'a',  'ł' => 'l',
        'ų' => 'u',  'ů' => 'u',  'ş' => 's',  'ğ' => 'g',  'ļ' => 'l',  'ƒ' => 'f',  'ž' => 'z',
        'ẃ' => 'w',  'ḃ' => 'b',  'å' => 'a',  'ì' => 'i',  'ï' => 'i',  'ḋ' => 'd',  'ť' => 't',
        'ŗ' => 'r',  'ä' => 'a',  'í' => 'i',  'ŕ' => 'r',  'ê' => 'e',  'ü' => 'u',  'ò' => 'o',
        'ē' => 'e',  'ñ' => 'n',  'ń' => 'n',  'ĥ' => 'h',  'ĝ' => 'g',  'đ' => 'd',  'ĵ' => 'j',
        'ÿ' => 'y',  'ũ' => 'u',  'ŭ' => 'u',  'ư' => 'u',  'ţ' => 't',  'ý' => 'y',  'ő' => 'o',
        'â' => 'a',  'ľ' => 'l',  'ẅ' => 'w',  'ż' => 'z',  'ī' => 'i',  'ã' => 'a',  'ġ' => 'g',
        'ṁ' => 'm',  'ō' => 'o',  'ĩ' => 'i',  'ù' => 'u',  'į' => 'i',  'ź' => 'z',  'á' => 'a',
        'û' => 'u',  'þ' => 'th', 'ð' => 'dh', 'æ' => 'ae', 'µ' => 'u',  'ĕ' => 'e',  'ı' => 'i',
        'а' => 'a',  'б' => 'b',  'в' => 'v',  'г' => 'g',  'д' => 'd',  'е' => 'e',  'ё' => 'io',
        'ж' => 'zh', 'з' => 'z',  'и' => 'i',  'й' => 'i',  'к' => 'k',  'л' => 'l',  'м' => 'm',
        'н' => 'n',  'о' => 'o',  'п' => 'p',  'р' => 'r',  'с' => 's',  'т' => 't',  'у' => 'u',
        'ф' => 'f',  'х' => 'h',  'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch','ъ' => '',
        'ь' => "ʹ",  'ы' => 'y',  'э' => 'e',  'ю' => 'yu', 'я' => 'ya',
    ];

    /**
     * Creates a slug to be used for pretty URLs
     *
     * @param  string $string String for transliterate
     * @param  string $delimiter Delimiter [Optional]
     * @return string
     */
    public function generate($string, $delimiter = '-')
    {
        if (function_exists('transliterator_transliterate')) {
            $string = $this->transliterateViaCharMap($string);
        } else {
            $string = $this->transliterateViaIntl($string);
        }

        $string = preg_replace('/[-\s]+/', $delimiter, $string);
        $string = preg_replace('/^[-\s]+/', '', $string);
        $string = preg_replace('/[-\s]+$/', '', $string);


        return trim($string, $delimiter);
    }

    protected function transliterateViaIntl($string)
    {
        $string = transliterator_transliterate('Any-Latin; Latin-ASCII; [:Punctuation:] Remove; Lower()', $string);

        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F"\'ʹ]+/S', '', $string);
    }

    protected function transliterateViaCharMap($string)
    {
        $string = strtolower($string);
        $string = str_replace(array_keys($this->charMap), array_values($this->charMap), $string);

        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F"\'ʹ]+/S', '', $string);
    }
}
