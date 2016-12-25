<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

return [
    // Bad symbols for sanitize
    'badChars' => [
        '/',
        '\\',
        '<',
        '>',
        ',',
        '$',
        '?',
        ':',
        ';',
        "\t",
        "\n",
        "\r",
        '"',
        "'",
        ' ',
        '*',
        '#',
        '&',
    ],

    // Incorrect first char
    'incorrectFirstChar' => [
        'й' => 'q',
        'ц' => 'w',
        'у' => 'e',
        'к' => 'r',
        'е' => 't',
        'н' => 'y',
        'г' => 'u',
        'ш' => 'i',
        'щ' => 'o',
        'з' => 'p',
        'ф' => 'a',
        'ы' => 's',
        'в' => 'd',
        'а' => 'f',
        'п' => 'g',
        'р' => 'h',
        'о' => 'j',
        'л' => 'k',
        'д' => 'l',
        'я' => 'z',
        'ч' => 'x',
        'с' => 'c',
        'м' => 'v',
        'и' => 'b',
        'т' => 'n',
        'ь' => 'm',
    ],

    'incorrectFirstLevelDomains' => [
        'edy' => 'edu',
        'con' => 'com',
    ],

    // Incorrect domain names
    'incorrectDomains' => [
        'hotmal.co'   => 'hotmail.com',
        'hotmil.co'   => 'hotmail.com',
        'homail.com'  => 'hotmail.com',
        'gmai.com'    => 'gmail.com',
        'gmail.comra' => 'gmail.com',
        'outloo.com'  => 'outlook.com',
        'mail.'       => 'mail.ru',
        'mail.rt'     => 'mail.ru',
        'mail.rui'    => 'mail.ru',
        'майл.р'      => 'mail.ru',
        'майл.ру'     => 'mail.ru',
        'маилру'      => 'mail.ru',
        'maill.ru'    => 'mail.ru',
        'meil.ru'     => 'mail.ru',
        'indox.ru'    => 'inbox.ru',
        'yndex.ru'    => 'yandex.ru',
        'yand.ru'     => 'yandex.ru',
        'yande.ru'    => 'yandex.ru',
        'yan.ru'      => 'yandex.ru',
        'comcast.ent' => 'comcast.net',
    ],

    // Domains for fix missed @
    'atDomains' => [
        'mail.ru',
        'list.ru',
        'bk.ru',
        'inbox.ru',
        'yandex.com',
        'yandex.ru',
        'ya.ru',
        'gmail.com',
        'yahoo.com',
        'hotmail.com',
        'ukr.net',
        'facebook.com',
    ],

    // Chars to convert (lowercase)
    'utfLower' => [
        'а' => 'a',
        'е' => 'e',
        'к' => 'k',
        'о' => 'o',
        'p' => 'p',
        'с' => 'c',
        'у' => 'y',
        'х' => 'x',
    ],

    // Application email parts
    'appParts' => [
        '@users.noreply.github.com',
        '@oauth.odnoklassniki.ru',
        '@auto.login',
        '@facebook.com',
        '@proxymail.facebook.com',
        '@fb.com',
        '@vkontakte.ru',
        '@vk.com',
        '@tfbnw.net',
        '@mamba.ru',
        '@ader.ru',
    ],

    // Role email parts
    'roleParts' => [
        'abuse@',
        'accounting@',
        'admin@',
        'admissions@',
        'all@',
        'billing@',
        'booking@',
        'careers@',
        'contact@',
        'contact-us@',
        'corp@',
        'customerservice@',
        'custserv@',
        'editor@',
        'everyone@',
        'finance@',
        'feedback@',
        'ftp@',
        'info@',
        'information@',
        'investorrelations@',
        'jobs@',
        'help@',
        'helpdesk@',
        'hostmaster@',
        'mail@',
        'marketing@',
        'media@',
        'news@',
        'noc@',
        'no-reply@',
        'noreply@',
        'office@',
        'ops@',
        'postmaster@',
        'privacy@',
        'remove@',
        'request@',
        'resumes@',
        'root@',
        'sales@',
        'security@',
        'spam@',
        'subscribe@',
        'support@',
        'test@',
        'usenet@',
        'users@',
        'uucp@',
        'webmaster@',
        'www@',
    ],

    // Corp email parts
    'corpParts' => [
        '@corp.mail.ru',
        '@team.ya(?:ndex)?.ru',
        '@rambler-co.ru',
        '@google.com',
    ],
];
