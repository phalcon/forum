# Phosphorum 3

[![Build Status](https://secure.travis-ci.org/phalcon/forum.svg?branch=master)][:ci:]

Phosphorum is an engine for building flexible, clear and fast forums.
You can adapt it to your own needs or improve it if you want.

Please write us if you have any feedback.

It is used by:
* [Phalcon Framework Forum][:pforum:]
* [Zephir Language Forum][:zforum:]

## NOTE

The master branch will always contain the latest stable version. If you wish
to check older versions or newer ones currently under development, please
switch to the relevant [branch][:branch:]/[tag][:tags:].

## Get Started

### Requirements

To run this application on your machine, you need at least:

* [Curl][:ext-curl:] extension
* [Openssl][:ext-ssl:] extension
* Internationalization ([intl][:ext-intl:]) extension
* Mbstring ([mbstring][:ext-mbs:]) extension
* [Composer][:composer:]
* PHP >= 5.5
* [Apache][:httpd:] Web Server with [mod_rewrite][:rewrite:] enabled or [Nginx][:nginx:] Web Server
* Latest stable [Phalcon Framework release][:phalcon:] extension enabled
* [Beanstalkd][:beanstalkd:] server

### Installation

#### 1. Getting started

Install composer into a common location or in your project:

```sh
$ curl -s http://getcomposer.org/installer | php
```

Then create a project as follows:

```sh
$ composer create-project phalcon/forum
```

#### 2. Creating a database

Then you'll need to create a database and initialize a schema:

```sh
$ echo 'CREATE DATABASE forum CHARSET=utf8 COLLATE=utf8_unicode_ci' | mysql -u root
$ cat schemas/forum.sql | mysql -u root phosphorum
```

#### 3. Set up the project

Open the `.env` file and configure its credentials.

#### Schema Changes

Changes to the database that you need to make if your Phosphorum version is...

* Older than 2.0.0: `schemas/upgrade-to-2.0.0.sql`
* Older than 2.0.1: `schemas/upgrade-to-2.0.1.sql`

#### Initial Test Data

You can create fake entries on an empty Phosphorum installation by running:

```bash
$ php scripts/random-entries.php
```

#### Directory Permissions

After installing Phosphorum, you may need to configure some permissions.
Directories within the `app/cache` and the `app/logs` directory should be writable by your web server or
**Phosphorum may not run**.

#### OAuth

This application uses [Github as authentication system][:oauth:], you need a Client ID and a Secret ID
to be set up in the configuration (`app/config/config.php`).

#### Starting the Beanstalkd client

A PHP client to deliver e-mails must be enabled in background:

```bash
$ php scripts/send-notifications-consumer.php &
```

You can serve it with [Supervisor][:superv:] by using following config:

```ini
; /etc/supervisor/conf.d/send-notifications-consumer.conf
;
; This is an example.
; Please update the config according to your environment

[program:notifications_consumer]
command=/usr/bin/php send-notifications-consumer.php
directory=/var/www/forum/scripts/
autostart=true
autorestart=true
user=www-data
stderr_logfile=/var/www/forum/app/logs/notification_consumer.err.log
stdout_logfile=/var/www/forum/app/logs/notification_consumer.out.log
```

## Tests

Phosphorum uses [Codeception][:codc:] functional, acceptance and unit tests.

First you need to re-generate base classes for all suites:

```bash
$ vendor/bin/codecept build
```

Then, you will able to run all tests with `run` command:

```bash
$ vendor/bin/codecept run
# OR
$ vendor/bin/codecept run --debug # Detailed output
```

Execute the `unit` test with the `run unit` command:

```bash
$ vendor/bin/codecept run unit
```

For more details about Console Commands see [here][:codccom:].

## License

Phosphorum is an open-sourced software licensed under the [New BSD License][:license:].
© Phalcon Framework Team and contributors

[:ci:]: http://travis-ci.org/phalcon/forum
[:ext-curl:]: http://php.net/manual/en/book.curl.php
[:ext-ssl:]: http://php.net/manual/en/book.openssl.php
[:ext-intl:]: http://php.net/manual/en/book.intl.php
[:ext-mbs:]: http://php.net/manual/en/book.mbstring.php
[:composer:]: https://getcomposer.org/
[:httpd:]: http://httpd.apache.org/
[:rewrite:]: http://httpd.apache.org/docs/current/mod/mod_rewrite.html
[:nginx:]: http://nginx.org/
[:phalcon:]: https://github.com/phalcon/cphalcon/releases
[:beanstalkd:]: http://kr.github.io/beanstalkd/
[:codc:]: http://codeception.com
[:codccom:]: http://codeception.com/docs/reference/Commands
[:license:]: https://github.com/phalcon/forum/blob/master/LICENSE.txt
[:pforum:]: https://forum.phalconphp.com/
[:zforum:]: https://forum.zephir-lang.com/
[:superv:]: http://supervisord.org/
[:oauth:]: https://developer.github.com/v3/oauth/
[:branch:]: https://github.com/phalcon/forum/branches
[:tags:]: https://github.com/phalcon/forum/tags
