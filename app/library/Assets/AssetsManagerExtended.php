<?php

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

namespace Phosphorum\Assets;

use Phalcon\Assets\Manager;
use Phalcon\Tag;
use Phosphorum\AssetsHash\HashManager\AssetsHashVersion as AssetsHash;
use Phalcon\Di;
use Phalcon\Assets\Collection;

/**
 * Add Assets caching
 */
class AssetsManagerExtended extends Manager
{
    /**
     * Prints the HTML for JS resources
     *
     * @param string|null $collectionName the collection name
     * @return string the result of the collection
     **/
    public function cachedOutputJs($collectionName = null)
    {
        $collection = $this->collection($collectionName);
        $hashManager = new AssetsHash($collection);
        $hashManager->setLogger(Di::getDefault()->get('logger'));

        $name = $hashManager->getHashedFileName();
        if (empty($name)) {
            return $this->outputJs($collectionName);
        }

        $collection->setTargetUri($name);
        return Tag::javascriptInclude($collection->getTargetUri());
    }

    /**
     * Prints the HTML for CSS resources
     *
     * @param string|null $collectionName the collection name
     * @return string the collection result
     **/
    public function cachedOutputCss($collectionName = null)
    {
        $collection = $this->collection($collectionName);
        $hashManager = new AssetsHash($collection);
        $hashManager->setLogger(Di::getDefault()->get('logger'));

        $name = $hashManager->getHashedFileName();
        if (empty($name)) {
            return $this->outputCss($collectionName);
        }

        $collection->setTargetUri($name);
        return Tag::stylesheetLink($collection->getTargetUri());
    }


    /**
     * Rewrite function
     * @param Collection $collection
     * @param callback $callback
     * @param string $type
     */
    public function output(Collection $collection, $callback, $type)
    {
        $useImplicitOutput = $this->_implicitOutput;
        $output = "";
        $logger = Di::getDefault()->get('logger');

        /** Get the resources as an array */
        $resources = $this->collectionResourcesByType($collection->getResources(), $type);

        /** Get filters in the collection */
        $filters = $collection->getFilters();

        /** Get the collection's prefix */
        $prefix = $collection->getPrefix();
        $typeCss = "css";
        $sourceBasePath = BASE_PATH.'/public/';
        $targetBasePath = BASE_PATH.'/public/assets/';
    $logger->error((string)__LINE__);
        /** Prepare options if the collection must be filtered */
        if (count($filters)) {
            $options = $this->_options;
    $logger->error((string)__LINE__);
            /** Check for global options in the assets manager */
            if (gettype($options) == "array") {
                /** The source base path is a global location where all resources are located */
                if (isset($options["sourceBasePath"])) {
                    $sourceBasePath = $options["sourceBasePath"];
                }

                /** The target base path is a global location where all resources are written */
                if (isset($options["targetBasePath"])) {
                    $targetBasePath = $options["targetBasePath"];
                }
            }

    $logger->error((string)__LINE__. ' - '. $sourceBasePath);
    $logger->error((string)__LINE__. ' - '. $targetBasePath);
            /** Check if the collection have its own source base path */
            $collectionSourcePath = $collection->getSourcePath();

            /**
             * Concatenate the global base source path with the collection one
             */
            if ($collectionSourcePath) {
                $completeSourcePath = $sourceBasePath . $collectionSourcePath;
            } else {
                $completeSourcePath = $sourceBasePath;
            }

            /** Check if the collection have its own target base path */
            $collectionTargetPath = $collection->getTargetPath();

            /** Concatenate the global base source path with the collection one */
            if ($collectionTargetPath) {
                $completeTargetPath = $targetBasePath . $collectionTargetPath;
            } else {
                $completeTargetPath = $targetBasePath;
            }
    $logger->error((string)__LINE__. ' - '. $completeSourcePath);
    $logger->error((string)__LINE__. ' - '. $completeTargetPath);
            /** Global filtered content */
            $filteredJoinedContent = "";

            /** Check if the collection have its own target base path */
            $join = $collection->getJoin();

            /** Check for valid target paths if the collection must be joined */
            if ($join) {
                /** We need a valid final target path */
                if (!$completeTargetPath) {
                    throw new \Exception("Path '". $completeTargetPath. "' is not a valid target path (1)");
                }

                if (is_dir($completeTargetPath)) {
                    throw new \Exception("Path '". $completeTargetPath. "' is not a valid target path (2), is dir.");
                }
            }
        }

        /** walk in resources */
        foreach ($resources as $resource) {
            /**
             * @var \Phalcon\Assets\ResourceInterface|\Phalcon\Assets\Resource $resource
             */
            $filterNeeded = false;
            $type = $resource->getType();

            /** Is the resource local? */
            $local = $resource->getLocal();

            /** If the collection must not be joined we must print a HTML for each one */
            if (count($filters)) {
                if ($local) {
                    /** Get the complete path */
                    $sourcePath = $resource->getRealSourcePath($completeSourcePath);
    $logger->error((string)__LINE__. ' - '. $sourcePath);
                    /** We need a valid source path */
                    if (!$sourcePath) {
                        $sourcePath = $resource->getPath();
                        throw new \Exception("Resource '". $sourcePath. "' does not have a valid source path");
                    }
                } else {
                    /** Get the complete source path */
                    $sourcePath = $resource->getPath();
    $logger->error((string)__LINE__. ' - '. $sourcePath);
                    /** resources paths are always filtered */
                    $filterNeeded = true;
                }

                /** Get the target path, we need to write the filtered content to a file */
                $targetPath = $resource->getRealTargetPath($targetBasePath);
    $logger->error((string)__LINE__. ' - '. $targetPath);
                /** We need a valid final target path */
                if (!$targetPath) {
                    throw new \Exception("Resource '". $sourcePath. "' does not have a valid target path");
                }

                if ($local) {
                    /**
                     * Make sure the target path is not the same source path
                     */
                    if ($targetPath == $sourcePath) {
                        throw new \Exception("Resource '". $targetPath. "' have the same source and target paths");
                    }

                    if (file_exists($targetPath)) {
                        if (compare_mtime($targetPath, $sourcePath)) {
                            $filterNeeded = true;
                        }
                    } else {
                        $filterNeeded = true;
                    }
                }

            } else {
                /** If there are not filters, just print/buffer the HTML */
                $path = $resource->getRealTargetUri();

                if ($prefix) {
                    $prefixedPath = $prefix . $path;
                } else {
                    $prefixedPath = $path;
                }

                /** Gets extra HTML attributes in the resource */
                $attributes = $resource->getAttributes();

                /** Prepare the parameters for the callback */
                $parameters = [];
                if (gettype($attributes) == "array") {
                    $attributes[0] = $prefixedPath;
                    $parameters[] = $attributes;
                } else {
                    $parameters[] = $prefixedPath;
                }
                $parameters[] = $local;

                /** Call the callback to generate the HTML */
                $html = call_user_func_array($callback, $parameters);

                /** Implicit output prints the content directly */
                if ($useImplicitOutput == true) {
                    echo $html;
                } else {
                    $output .= $html;
                }
    $logger->error((string)__LINE__. ' - '. $path);
                continue;
            }

            if ($filterNeeded == true) {
                /** Gets the resource's content */
                $content = $resource->getContent($completeSourcePath);

                /** Check if the resource must be filtered */
                $mustFilter = $resource->getFilter();

                /** Only filter the resource if it's marked as 'filterable' */
                if ($mustFilter == true) {
                    foreach ($filters as $filter) {

                        /** Filters must be valid objects */
                        if (gettype($filter) != "object") {
                            throw new \Exception("Filter is invalid");
                        }

                        /** Calls the method 'filter' which must return a filtered version of the content */
                        $filteredContent = $filter->filter($content);
                        $content = $filteredContent;

                    }
                    /** Update the joined filtered content */
                    if ($join == true) {
                        if ($type == $typeCss) {
                            $filteredJoinedContent .= $filteredContent;
                        } else {
                            $filteredJoinedContent .= $filteredContent . ";";
                        }
                    }
                } else {
                    /** Update the joined filtered content */
                    if ($join == true) {
                        $filteredJoinedContent .= $content;
                    } else {
                        $filteredContent = $content;
                    }
                }
    $logger->error((string)__LINE__. ' - '. $targetPath);
                if (!$join) {
                    /** Write the file using file-put-contents. This respects the openbase-dir also writes to streams */
                    file_put_contents($targetPath, $filteredContent);
                }
            }

            if (!$join) {
                /** Generate the HTML using the original path in the resource */
                $path = $resource->getRealTargetUri();
    $logger->error((string)__LINE__. ' - '. $path);
                if ($prefix) {
                    $prefixedPath = $prefix . $path;
                } else {
                    $prefixedPath = $path;
                }

                /** Gets extra HTML attributes in the resource */
                $attributes = $resource->getAttributes();

                /** Filtered resources are always local */
                $local = true;

                /** Prepare the parameters for the callback */
                $parameters = [];
                if (gettype($attributes) == "array") {
                    $attributes[0] = $prefixedPath;
                    $parameters[] = $attributes;
                } else {
                    $parameters[] = $prefixedPath;
                }
                $parameters[] = $local;

                /** Call the callback to generate the HTML */
                $html = call_user_func_array($callback, $parameters);

                /** Implicit output prints the content directly */
                if ($useImplicitOutput == true) {
                    echo $html;
                } else {
                    $output .= $html;
                }
            }
        }

        if (count($filters)) {

            if ($join == true) {
                /**
                 * Write the file using file_put_contents. This respects the openbase-dir also
                 * writes to streams
                 */
                file_put_contents($completeTargetPath, $filteredJoinedContent);
    $logger->error((string)__LINE__. ' - '. $completeTargetPath);
                /** Generate the HTML using the original path in the resource */
                $targetUri = $collection->getTargetUri();
    $logger->error((string)__LINE__. ' - '. $targetUri);
                if ($prefix) {
                    $prefixedPath = $prefix . $targetUri;
                } else {
                    $prefixedPath = $targetUri;
                }

                /** Gets extra HTML attributes in the collection */
                $attributes = $collection->getAttributes();

                /** Gets local */
                $local = $collection->getTargetLocal();
    $logger->error((string)__LINE__. ' - '. $local);
                /** Prepare the parameters for the callback */
                $parameters = [];
                if (gettype($attributes) == "array") {
                    $attributes[0] = $prefixedPath;
                    $parameters[] = $attributes;
                } else {
                    $parameters[] = $prefixedPath;
                }
                $parameters[] = $local;

                /** Call the callback to generate the HTML */
                $html = call_user_func_array($callback, $parameters);

                /** Implicit output prints the content directly */
                if ($useImplicitOutput == true) {
                    echo $html;
                } else {
                    $output .= $html;
                }
            }
        }

        return $output;
    }
}
