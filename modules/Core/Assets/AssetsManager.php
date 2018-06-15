<?php
declare(strict_types=1);

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-present Phalcon Team and contributors               |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Core\Assets;

use Phalcon\Assets\Manager;
use Phalcon\Tag;
use Phosphorum\Core\Assets\Version\Strategy\AppVersionStrategy;
use Phosphorum\Core\Assets\Version\StrategyInterface;

/**
 * Phosphorum\Core\Assets\AssetsManager
 *
 * @package Phosphorum\Core\Assets
 */
final class AssetsManager extends Manager
{
    /** @var StrategyInterface  */
    protected $versioningStrategy;

    /** @var Tag */
    protected $tagManager;

    /**
     * AssetsManager constructor.
     *
     * @param Tag   $tagManager
     * @param array $options
     */
    public function __construct(Tag $tagManager, array $options = [])
    {
        parent::__construct($options);

        $this->versioningStrategy = $this->createVersioningStrategy();
        $this->tagManager = $tagManager;
    }

    /**
     * Versioning strategy factory method.
     *
     * @return StrategyInterface
     */
    protected function createVersioningStrategy(): StrategyInterface
    {
        $options = $this->getOptions();

        if (isset($options['strategy'])) {
            $strategy = $options['strategy'];
        } else {
            $strategy = AppVersionStrategy::class;
        }

        return new $strategy();
    }

    /**
     * Prints the HTML for JS resources.
     *
     * @param  string $collectionName
     *
     * @return string
     */
    public function cachedOutputJs(string $collectionName): string
    {
        $collection = $this->collection($collectionName);

        $fileName = $this->versioningStrategy->resolve();
        if ($fileName === null) {
            return $this->outputJs($collectionName);
        }

        $collection->setTargetUri($fileName);

        return $this->tagManager->javascriptInclude($collection->getTargetUri());
    }

    /**
     * Prints the HTML for CSS resources.
     *
     * @param string $collectionName
     *
     * @return string
     */
    public function cachedOutputCss(string $collectionName): string
    {
        $collection = $this->collection($collectionName);

        $fileName = $this->versioningStrategy->resolve();
        if ($fileName === null) {
            return $this->outputCss($collectionName);
        }

        $collection->setTargetUri($fileName);

        return $this->tagManager->stylesheetLink($collection->getTargetUri());
    }
}
