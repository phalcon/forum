Phosphorum 2
============

[![Build Status](https://secure.travis-ci.org/phalcon/forum.svg?branch=master)](http://travis-ci.org/phalcon/forum)

This is the official Phalcon Forum you can adapt it to your own needs or improve it if you want.

Please write us if you have any feedback.

Thanks.

Requirements
------------
You can clone the repository and then install dependencies using composer:

```bash
php composer.phar install
```

Requirements
------------
This application uses Github as authentication system, you need a client id and secret id
to be set up in the configuration (app/config/config.php):

* Curl extension (http://php.net/manual/en/book.curl.php)
* Openssl extension (http://php.net/manual/en/book.openssl.php)

NOTE
----
The master branch will always contain the latest stable version. If you wish
to check older versions or newer ones currently under development, please
switch to the relevant branch.

Required version: >= 1.2.6

Get Started
-----------

#### Requirements

To run this application on your machine, you need at least:

* PHP >= 5.3.9
* Apache Web Server with mod rewrite enabled or Nginx Web Server
* Latest Phalcon Framework extension installed and enabled

Then you'll need to create the database and initialize schema:

    echo 'CREATE DATABASE forum CHARSET=utf8 COLLATE=utf8_unicode_ci' | mysql -u root
    cat schemas/forum.sql | mysql -u root forum

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

License
-------
Phosphorum is open-sourced software licensed under the New BSD License.
