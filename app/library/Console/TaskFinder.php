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

namespace Phosphorum\Console;

use SplFileInfo;
use ReflectionClass;
use ReflectionMethod;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Phalcon\Annotations\Collection;

/**
 * Phosphorum\Console\TaskFinder
 *
 * @package Phosphorum\Console
 */
class TaskFinder
{
    protected $path;

    protected $commands = [];

    /**
     * TaskFinder constructor.
     *
     * @param string $path Tasks path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Scan for application tasks.
     *
     * @return array
     */
    public function scan()
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->path),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $entry) {
            $this->addCommand($entry);
        }

        return $this->commands;
    }

    public function addCommand(SplFileInfo $file)
    {
        if (!$file->isFile() || !$file->isReadable()) {
            return;
        }

        if ($file->getExtension() !== 'php') {
            return;
        }

        $classes = $this->getClassesFromFile($file->getPathname());

        if (empty($classes) || !class_exists($classes[0])) {
            return;
        }

        $className = $classes[0];

        /** @var \Phalcon\Annotations\AdapterInterface $reader */
        $reader = container('annotations');

        $reflectionClass = new ReflectionClass($className);
        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

        if (empty($methods)) {
            return;
        }

        foreach ($methods as $method) {
            if ($method->isConstructor() || $method->isDestructor() || !$method->isUserDefined()) {
                continue;
            }

            if ($reflectionClass->getName() !== $method->getDeclaringClass()->getName()) {
                continue;
            }

            $annotations = $reader->getMethod($className, $method->getName());

            $name = $method->getName();

            if ($name === 'main') {
                $name = '';
            }

            if ($alias = $this->getAnnotationDoc($annotations, 'Alias')) {
                $name = $alias;
            }

            $command = explode('\\', $className);
            $command = strtolower(array_pop($command));

            $description = $this->getAnnotationDoc($annotations, 'Doc');
            $definition  = compact('description', 'name', 'command');


            if (!isset($this->commands[$className])) {
                $this->commands[$className] = [];
            }

            $this->commands[$className][] = $definition;
        }
    }

    public function getAnnotationDoc(Collection $collection, $label)
    {
        $description = null;

        if ($collection->has($label)) {
            $description = $collection->get($label);
            $description = implode('', $description->getArguments());
        }

        return $description;
    }

    public function getClassesFromFile($file)
    {
        $sourceCode = file_get_contents($file);
        $classes = [];
        $tokens = token_get_all($sourceCode);
        $tokenCount = count($tokens);
        $namespace = '';

        for ($i = 0; $i < $tokenCount; $i++) {
            if ($tokens[$i][0] === T_NAMESPACE) {
                $namespace = '';
                for ($j = $i + 1; $j < $tokenCount; $j++) {
                    if ($tokens[$j][0] === T_STRING) {
                        $namespace .= $tokens[$j][1] . '\\';
                    } else {
                        if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                            break;
                        }
                    }
                }
            }

            if ($tokens[$i][0] === T_CLASS) {
                if (!isset($tokens[$i - 2])) {
                    $classes[] = $namespace . $tokens[$i + 2][1];
                    continue;
                }
                if ($tokens[$i - 2][0] === T_NEW) {
                    continue;
                }
                if ($tokens[$i - 1][0] === T_WHITESPACE and $tokens[$i - 2][0] === T_DOUBLE_COLON) {
                    continue;
                }
                if ($tokens[$i - 1][0] === T_DOUBLE_COLON) {
                    continue;
                }
                $classes[] = $namespace . $tokens[$i + 2][1];
            }
        }

        return $classes;
    }
}
