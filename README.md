Phosphorum 2
============

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/duythien/forum?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

[![Build Status](https://secure.travis-ci.org/phalcon/forum.svg?branch=master)](http://travis-ci.org/phalcon/forum)

This is the official Phalcon Forum you can adapt it to your own needs or improve it if you want.

Please write us if you have any feedback.

Thanks.

Requirements
------------
You must clone the repository and then install dependencies using composer:

```bash
php composer.phar install
```

Requirements
------------
Change the owner of `app/logs` and `app/cache` to whatever user your web server is running as.

This application uses Github as authentication system, you need a client id and secret id
to be set up in the configuration (app/config/config.php):

* Curl extension (http://php.net/manual/en/book.curl.php)
* Openssl extension (http://php.net/manual/en/book.openssl.php)
* Internationalization (intl) extension (http://php.net/manual/en/book.intl.php)

NOTE
----
The master branch will always contain the latest stable version. If you wish
to check older versions or newer ones currently under development, please
switch to the relevant branch.

Required version: >= 1.3.0

Get Started
-----------

#### Requirements

To run this application on your machine, you need at least:

* PHP >= 5.4.0
* Apache Web Server with mod rewrite enabled or Nginx Web Server
* Latest Phalcon Framework extension installed and enabled
* [Beanstalkd](http://kr.github.io/beanstalkd/) server

Then you'll need to create the database and initialize schema:

    echo 'CREATE DATABASE forum CHARSET=utf8 COLLATE=utf8_unicode_ci' | mysql -u root
    cat schemas/forum.sql | mysql -u root forum

Starting the Beanstalkd client
------------------------------
A PHP client to deliver e-mails must be enabled in background:

```bash
php scripts/send-notifications-consumer.php &
```

Initial Test Data
-----------------
You can create fake entries on an empty Phosphorum installation by running:

```bash
php scripts/random-entries.php
```

Tests
-----
Phosphorum use [Codeception](http://codeception.com) functional tests. Execute:

```bash
php codecept.phar run
```

Detailed output:

```bash
php codecept.phar run --debug
```
#### Running with Docker

To play Phalcon forum with Docker you just edit password to "root" in file config.example.php and do follow command below:

```
docker build -t phalcon-forum .
docker run -it -p 80:80 phalcon-forum
```
Finally if everything went accordingly, we'll be welcomed with Phalcon forum when pointing the browser to http://127.0.0.1

License
-------
Phosphorum is open-sourced software licensed under the New BSD License.
