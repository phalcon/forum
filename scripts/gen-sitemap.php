<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file docs/LICENSE.txt.                        |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

/**
 * This script generates backup and uploads it to Dropbox
 */
require 'cli-bootstrap.php';

use Phalcon\Di;
use Phalcon\Config;
use Phalcon\DI\Injectable;
use Phosphorum\Models\Posts;
use League\Flysystem\Filesystem;
use Phalcon\Logger\Adapter\Stream;
use League\Flysystem\Adapter\Local;

class GenerateSitemap extends Injectable
{
    public function run()
    {
        $log = new Stream('php://stdout');

        /** @var Config $config */
        $config = Di::getDefault()->getShared('config');

        $expireDate = new DateTime('now', new DateTimeZone('UTC'));
        $expireDate->modify('+1 day');

        $sitemap = new DOMDocument("1.0", "UTF-8");
        $sitemap->formatOutput = true;

        $urlset = $sitemap->createElement('urlset');
        $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $urlset->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');

        $baseUrl = $config->get('site')->url;

        $url = $sitemap->createElement('url');
        $url->appendChild($sitemap->createElement('loc', $baseUrl));
        $url->appendChild($sitemap->createElement('changefreq', 'daily'));
        $url->appendChild($sitemap->createElement('priority', '1.0'));
        $urlset->appendChild($url);

        $karmaSql = 'number_views + ' .
                    '((IF(votes_up IS NOT NULL, votes_up, 0) - IF(votes_down IS NOT NULL, votes_down, 0)) * 4) + ' .
                    'number_replies';

        $parametersPosts = [
            'conditions' => 'deleted != 1',
            'columns'    => "id, slug, modified_at, {$karmaSql} AS karma",
            'order'      => 'karma DESC'
        ];
        $posts = Posts::find($parametersPosts);

        $parametersKarma = [
            'column' => $karmaSql,
            'conditions' => 'deleted != 1'
        ];
        $karma = Posts::maximum($parametersKarma);

        $modifiedAt = new DateTime('now', new DateTimeZone('UTC'));

        foreach ($posts as $post) {
            $modifiedAt->setTimestamp($post->modified_at);

            $postKarma = $post->karma / ($karma + 100);

            $url = $sitemap->createElement('url');
            $href = trim($baseUrl, '/') . '/discussion/' . $post->id . '/' . $post->slug;
            $url->appendChild(
                $sitemap->createElement('loc', $href)
            );

            $valuePriority = $postKarma > 0.7 ? sprintf("%0.1f", $postKarma) : sprintf("%0.1f", $postKarma + 0.25);
            $url->appendChild(
                $sitemap->createElement('priority', $valuePriority)
            );
            $url->appendChild($sitemap->createElement('lastmod', $modifiedAt->format('Y-m-d\TH:i:s\Z')));
            $urlset->appendChild($url);
        }

        $sitemap->appendChild($urlset);

        $adapter = new Local(dirname(dirname(__FILE__)) . '/public');
        $filesystem = new Filesystem($adapter);

        if ($filesystem->has('sitemap.xml')) {
            $result = $filesystem->update('sitemap.xml', $sitemap->saveXML() . PHP_EOL);
        } else {
            $result = $filesystem->write('sitemap.xml', $sitemap->saveXML() . PHP_EOL);
        }

        if ($result) {
            $log->info('The sitemap.xml was successfully updated');
        } else {
            $log->error('Failed to update the sitemap.xml file');
        }
    }
}

try {
    $task = new GenerateSitemap();
    $task->run();
} catch (Exception $e) {
    fwrite(STDERR, 'ERROR: ' . $e->getMessage() . PHP_EOL);
    fwrite(STDERR, $e->getTraceAsString() . PHP_EOL);
    exit(1);
}
