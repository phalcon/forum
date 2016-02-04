<?php

namespace Helper;

use Codeception\Module;

/**
 * Acceptance Helper
 *
 * Here you can define custom actions
 * all public methods declared in helper class will be available in $I
 *
 * @package Helper
 */
class Acceptance extends Module
{
    /**
     * Parse XML with Sitemap schema and return its URLs
     *
     * @param $string
     * @return array
     */
    public function parseSitemap($string)
    {
        $urls = [];
        $x = new \SimpleXMLElement($string);
        foreach ($x->url as $n) {
            $urls[] = $n->loc->__toString();
        }

        return $urls;
    }
}
