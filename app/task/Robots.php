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

namespace Phosphorum\Task;

use Phalcon\Cli\Console\Exception;
use Phosphorum\Console\AbstractTask;

/**
 * Phosphorum\Task\Robots
 *
 * @package Phosphorum\Task
 */
class Robots extends AbstractTask
{
    /**
     * @Doc("Generate the robots.txt")
     */
    public function generate()
    {
        /** @var \League\Flysystem\Filesystem $filesystem */
        $filesystem = singleton('filesystem', [$this->basePath . DIRECTORY_SEPARATOR . 'public']);

        /** @var \Phalcon\Config $config */
        $config  = container('config');
        $baseUrl = rtrim($config->get('site')->url, '/');
        $robots  = $this->getRobotsTemplate($baseUrl);

        if ($filesystem->has('robots.txt')) {
            $result = $filesystem->update('robots.txt', $robots);
        } else {
            $result = $filesystem->write('robots.txt', $robots);
        }

        if ($result) {
            $this->output('The robots.txt was successfully updated');
        } else {
            throw new Exception('Failed to update the robots.txt file');
        }
    }

    /**
     * Gets robots.txt template.
     *
     * @param  string $baseUrl
     * @return string
     */
    protected function getRobotsTemplate($baseUrl)
    {
        $content=<<<EOL
User-agent: *
Disallow: /400
Disallow: /401
Disallow: /403
Disallow: /404
Disallow: /500
Disallow: /503
Allow: /
Sitemap: $baseUrl/sitemap.xml
EOL;
        return $content;
    }
}
