# Phosphorum 3

[![Build Status](https://secure.travis-ci.org/phalcon/forum.svg?branch=master)](http://travis-ci.org/phalcon/forum)

Phosphorum is an engine for building flexible, clear and fast forums.
You can adapt it to your own needs or improve it if you want.

Please write us if you have any feedback.

It is used by:
* [Phalcon Framework Forum][15]
* [Zephir Language Forum][16]

## NOTE

The master branch will always contain the latest stable version. If you wish
to check older versions or newer ones currently under development, please
switch to the relevant [branch][19]/[tag][20].

## Get Started

### Requirements

To run this application on your machine, you need at least:

* [Curl][1] extension
* [Openssl][2] extension
* Internationalization ([intl][3]) extension
* Mbstring ([mbstring][4]) extension
* [Composer][5]
* PHP >= 5.5
* [Apache][6] Web Server with [mod_rewrite][7] enabled or [Nginx][8] Web Server
* Latest stable [Phalcon Framework release][9] extension enabled
* [Beanstalkd][10] server

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
Directories within the `app/cache` and the `app/logs` directory should be writable by your web server or **Phosphorum may not run**.

#### OAuth

This application uses [Github as authentication system][18], you need a Client ID and a Secret ID
to be set up in the configuration (`app/config/config.php`).

#### Starting the Beanstalkd client

A PHP client to deliver e-mails must be enabled in background:

```bash
$ php scripts/send-notifications-consumer.php &
```

You can serve it with [Supervisor][17] by using following config:

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

Phosphorum uses [Codeception][11] functional, acceptance and unit tests.

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

For more details about Console Commands see [here][13].

## License

Phosphorum is an open-sourced software licensed under the [New BSD License][14]. © Phalcon Framework Team and contributors

[1]: http://php.net/manual/en/book.curl.php
[2]: http://php.net/manual/en/book.openssl.php
[3]: http://php.net/manual/en/book.intl.php
[4]: http://php.net/manual/en/book.mbstring.php
[5]: https://getcomposer.org/
[6]: http://httpd.apache.org/
[7]: http://httpd.apache.org/docs/current/mod/mod_rewrite.html
[8]: http://nginx.org/
[9]: https://github.com/phalcon/cphalcon/releases
[10]: http://kr.github.io/beanstalkd/
[11]: http://codeception.com
[13]: http://codeception.com/docs/reference/Commands
[14]: https://github.com/phalcon/forum/blob/master/LICENSE.txt
[15]: https://forum.phalconphp.com/
[16]: https://forum.zephir-lang.com/
[17]: http://supervisord.org/
[18]: https://developer.github.com/v3/oauth/
[19]: https://github.com/phalcon/forum/branches
[20]: https://github.com/phalcon/forum/tags
