<?php

return new \Phalcon\Config(array(
                                'database' => array(
                                    'adapter'  => 'Mysql',
                                    'host'     => 'localhost',
                                    'username' => 'root',
                                    'password' => '',
                                    'name'     => 'phalcon_forum',
                                ),
                                'application' => array(
                                    'controllersDir' => __DIR__ . '/../../app/controllers/',
                                    'modelsDir'      => __DIR__ . '/../../app/models/',
                                    'viewsDir'       => __DIR__ . '/../../app/views/',
                                    'pluginsDir'     => __DIR__ . '/../../app/plugins/',
                                    'libraryDir'     => __DIR__ . '/../../app/library/',
                                    'baseUri'        => '/',
                                ),
                                'models' => array(
                                    'metadata' => array(
                                        'adapter' => 'Memory'
                                    )
                                ),
                                'github' => array(
                                    'clientId' => '7968d916b0585a7fcc0c',
                                    'clientSecret' => 'c2be4cdaf132ec31814479de0ff46bf833a9375c',
                                    'redirectUri' => 'http://www.phalcon-forum.local/login/oauth/access_token'
                                ),
                                'amazon' => array(
                                    'AWSAccessKeyId' => '',
                                    'AWSSecretKey' => ''
                                )
                           ));
