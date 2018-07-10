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

use Phalcon\Assets\Collection;
use Phalcon\Assets\Manager;
use Phalcon\Config;
use Phalcon\Platform\Exceptions\DomainException;
use Phalcon\Tag;
use Phosphorum\Core\Assets\Version\Strategy\AppVersionStrategy;
use Phosphorum\Core\Assets\Version\StrategyInterface;
use Phosphorum\Core\Environment;

/**
 * Phosphorum\Core\Assets\AssetsManager
 *
 * @package Phosphorum\Core\Assets
 */
final class AssetsManager extends Manager
{
    /** @var Tag */
    protected $tagManager;

    /** @var bool */
    protected $modifyFilename = false;

    /** @var bool */
    protected $checkMTimeAlways = false;

    /** @var Environment */
    protected $environment;

    /** @var StrategyInterface */
    protected $strategy;

    /**
     * AssetsManager constructor.
     *
     * @param Tag         $tagManager
     * @param Config      $config
     * @param Environment $environment
     */
    public function __construct(Tag $tagManager, Config $config, Environment $environment)
    {
        $this->tagManager = $tagManager;
        $this->environment = $environment;

        $this->checkMTimeAlways = $this->checkModificationTimeAlways($config);
        $this->modifyFilename = $config->get('modifyFilename', false);
        $this->strategy = $config->get('strategy', AppVersionStrategy::class);

        parent::__construct($config->toArray());
    }

    /**
     * Prints the HTML for JS resources.
     *
     * @param  string $collectionName
     *
     * @return string
     *
     * @throws DomainException
     */
    public function cachedOutputJs(string $collectionName): string
    {
        $collection = $this->collection($collectionName);
        $collection->rewind();

        if ($collection->valid() == false) {
            throw new DomainException(
                "The collection '{$collectionName}' doesn't exists."
            );
        }

        $versioningStrategy = $this->createVersioningStrategy($collection);

        $fileName = $versioningStrategy->resolve();
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
     *
     * @throws DomainException
     */
    public function cachedOutputCss(string $collectionName): string
    {
        $collection = $this->collection($collectionName);
        $collection->rewind();

        if ($collection->valid() == false) {
            throw new DomainException(
                "The collection '{$collectionName}' doesn't exists."
            );
        }

        $versioningStrategy = $this->createVersioningStrategy($collection);

        $fileName = $versioningStrategy->resolve();
        if ($fileName === null) {
            return $this->outputCss($collectionName);
        }

        $collection->setTargetUri($fileName);

        return $this->tagManager->stylesheetLink($collection->getTargetUri());
    }

    /**
     * Versioning strategy factory method.
     *
     * @param  Collection $collection
     *
     * @return StrategyInterface
     */
    protected function createVersioningStrategy(Collection $collection): StrategyInterface
    {
        return new $this->strategy(
            $collection,
            $this->modifyFilename,
            $this->checkMTimeAlways
        );
    }

    /**
     * Tell the Assets Version Strategy if we should check modification time in each request.
     *
     * @param  Config $config
     *
     * @return bool
     */
    protected function checkModificationTimeAlways(Config $config): bool
    {
        $isDevelopmentStage = $this->environment->isCurrentStage(Environment::DEVELOPMENT);

        return ($isDevelopmentStage || $config->get('debug', false));
    }
}
