<?php

/*
  +------------------------------------------------------------------------+
  | Phosphorum                                                             |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-present Phalcon Team (https://www.phalconphp.com)   |
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

namespace Phosphorum\Services\Controller;

use Phalcon\Di;
use Phalcon\Registry;
use Phalcon\Assets\Manager;
use Phosphorum\Assets\Filters\NoneFilter;
use Phosphorum\Exception\RuntimeException;
use Phosphorum\Exception\InvalidParameterException;

/**
 * Phosphorum\Services\Controller\AssetsService
 *
 * @package Phosphorum\Services\Controller
 */

class AssetsService
{
    /** @var Di $di */
    private $di;

    /** @var Registry $registry */
    private $registry;

    /** @var Manager $manager */
    private $manager;

    public function __construct(Manager $manager = null)
    {
        $this->di = Di::getDefault();
        $this->registry = $this->di->get('registry');

        if ($manager === null) {
            $manager = $this->di->get('assets');
        }
        $this->manager = $manager;
    }

    /**
     * @return Manager
     */
    public function getManager()
    {
        $this->setJsCollection();
        $this->setCssCollection();

        return $this->manager;
    }

    /**
     * @return void
     * @throws RuntimeException
     */
    private function setJsCollection()
    {
        try {
            $this->manager
                ->collection('globalJs')
                ->setTargetPath($this->getPath('public') . 'assets/global.js')
                ->setTargetUri('assets/global.js')
                ->addJs($this->getPath('public') . 'js/jquery-3.2.1.min.js', true, false)
                ->addJs($this->getPath('public') . 'js/bootstrap.min.js', true, false)
                ->addJs($this->getPath('public') . 'js/forum.js', true)
                ->addJs($this->getPath('public') . 'js/prism.js', true)
                ->join(true)
                ->addFilter(new NoneFilter());

            $this->manager
                ->collection('editorJs')
                ->setTargetPath($this->getPath('public') . 'assets/editor.js')
                ->setTargetUri('assets/editor.js')
                ->addJs($this->getPagedownPath('Markdown.Converter.js'), true)
                ->addJs($this->getPagedownPath('Markdown.Sanitizer.js'), true)
                ->addJs($this->getPagedownPath('Markdown.Editor.js'), true)
                ->join(true)
                ->addFilter(new NoneFilter());

            $this->manager
                ->collection('autocompleteJs')
                ->setTargetPath($this->getPath('public') . 'assets/autocomplete.js')
                ->setTargetUri('assets/autocomplete.js')
                ->addJs($this->getPath('public') . 'js/lodash.min.js', true, false)
                ->addJs($this->getPath('public') . 'js/jquery.elastic.js', true)
                ->addJs($this->getPath('public') . 'js/jquery.events.input.js', true, false)
                ->addJs($this->getPath('public') . 'js/jquery.mentionsInput.js', true, false)
                ->join(true)
                ->addFilter(new NoneFilter());
        } catch (RuntimeException $e) {
            $this->di->get('logger')->error($e->getMessage());
        }
    }

    /**
     * @return void
     * @throws RuntimeException
     */
    private function setCssCollection()
    {
        $params = $this->getCssCollectionParam();

        try {
            $this->manager
                ->collection($params['collectionName'])
                ->setTargetPath($this->getPath('public') . "assets/{$params['fileName']}")
                ->setTargetUri("assets/{$params['fileName']}")
                ->addCss($this->getPath('public') . 'css/bootstrap.min.css', true, false)
                ->addCss($this->getPath('public') . 'css/fonts.css', true)
                ->addCss($this->getPath('public') . 'css/octicons.css', true)
                ->addCss($this->getPath('public') . 'css/diff.css', true)
                ->addCss($this->getPath('public') . 'css/style.css', true)
                ->addCss($this->getPath('public') . 'css/prism.css', true)
                ->addCss($this->getPath('public') . "css/{$params['themeFile']}", true)
                ->join(true)
                ->addFilter(new NoneFilter());

            $this->manager
                ->collection('editorCss')
                ->setTargetPath($this->getPath('public') . 'assets/editor.css')
                ->setTargetUri('assets/editor.css')
                ->addCss($this->getPath('public') . 'css/editor.min.css', true, false)
                ->join(true)
                ->addFilter(new NoneFilter());

            $this->manager
                ->collection('autocompleteCss')
                ->setTargetPath($this->getPath('public') . 'assets/autocomplete.css')
                ->setTargetUri('assets/autocomplete.css')
                ->addCss($this->getPath('public') . 'css/jquery.mentionsInput.css', true)
                ->join(true)
                ->addFilter(new NoneFilter());
        } catch (RuntimeException $e) {
            $this->di->get('logger')->error($e->getMessage());
        }
    }

    /**
     * @return array
     */
    private function getCssCollectionParam()
    {
        $param['collectionName'] = 'globalCss';
        $param['fileName'] = 'global-default.css';
        $param['themeFile'] = 'theme.css';

        if ($this->di->has('session') && $this->di->get('session')->get('identity-theme') === 'L') {
            $param['collectionName'] = 'globalWhiteCss';
            $param['fileName'] = 'global-white.css';
            $param['themeFile'] = 'theme-white.css';
        }

        return $param;
    }

    /**
     * Get path from registry
     * @param string $directory
     * @return string
     */
    protected function getPath($directory)
    {
        return $this->registry->offsetGet('paths')->{$directory};
    }

    /**
     * Get path to pagedown's file
     * @param string $fileName
     * @return string
     * @throws InvalidParameterException
     */
    private function getPagedownPath($fileName)
    {
        $filePath = $this->getPath('basePath') . '/vendor/stackexchange/pagedown/' . $fileName;
        if (file_exists($filePath)) {
            return $filePath;
        }

        throw new InvalidParameterException("Pagedown's file '{$fileName}' isn't exist in vendor");
    }
}
