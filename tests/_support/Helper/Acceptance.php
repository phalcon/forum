<?php

namespace Helper;

use SimpleXMLElement;
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
     * @param string $string Response content
     * @return array
     */
    public function parseSitemap($string)
    {
        $urls = [];
        $xml  = new SimpleXMLElement($string);

        foreach ($xml->url as $node) {
            /** @var \SimpleXMLElement $node */
            if ($node instanceof SimpleXMLElement) {
                $urls[] = (string) $node->loc;
            }

        }

        return $urls;
    }

    public function seeResponseRegexp($regexp, $content)
    {
        $this->assertRegExp($regexp, $content);
    }
}
