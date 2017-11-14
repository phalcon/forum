<?php

/*
   +------------------------------------------------------------------------+
   | Phalcon forum                                                          |
   +------------------------------------------------------------------------+
   | Copyright (c) 2011-2017 Phalcon Team (https://www.phalconphp.com)      |
   +------------------------------------------------------------------------+
   | This source file is subject to the New BSD License that is bundled     |
   | with this package in the file LICENSE.txt.                             |
   |                                                                        |
   | If you did not receive a copy of the license and are unable to         |
   | obtain it through the world-wide-web, please send an email             |
   | to license@phalconphp.com so we can send you a copy immediately.       |
   +------------------------------------------------------------------------+
   | Authors: Sergii Svyrydenko <sergey.v.sviridenko@gmail.com>             |
   +------------------------------------------------------------------------+
 */

namespace Phosphorum\Provider\Markdown\Plugins;

use Ciconia\Markdown;
use Ciconia\Common\Text;
use Ciconia\Extension\ExtensionInterface;

class SpecSymbolExtension implements ExtensionInterface
{
    public function register(Markdown $markdown)
    {
        $markdown->on('inline', [$this, 'escapeText']);
    }

    public function escapeText(Text $text)
    {
        $replaceArray = [];
        $arrTags = ['code', 'ins', 'del'];

        foreach ($arrTags as $tag) {
            $text->replace("{<{$tag}>.*?</{$tag}>}m", function (Text $w) use (&$replaceArray) {
                $count = count($replaceArray) + 1;
                $replaceArray[$count] = $w->getString();
                $w->replaceString($w->getString(), "%%replaced" . $count . "%%");

                return $w;
            });
        }

        $str = htmlspecialchars($text->getString());
        foreach ($replaceArray as $key => $value) {
            $str = str_replace("%%replaced" . $key . "%%", $value, $str);
        }

        $text->setString($str);
    }

    public function getName()
    {
        return 'escapeText';
    }
}
