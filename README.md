Phosphorum
==========

Phalcon PHP is a web framework delivered as a C extension providing high
performance and lower resource consumption.

This is the official Phalcon Forum you can adapt it to your own needs or improve it
if you want.

Please write us if you have any feedback.

Thanks.

Requirements
------------
This application uses Github as authentication system, you need a client id and secret id
set in the configuration (app/config/config.php)

* Http extension (pecl.php.net/package/pecl_http)
* Curl extension (http://php.net/manual/en/book.curl.php)

NOTE
----
The master branch will always contain the latest stable version. If you wish
to check older versions or newer ones currently under development, please
switch to the relevant branch.

Required version: >= 0.9.0

Get Started
-----------

#### Requirements

To run this application on your machine, you need at least:

* >= PHP 5.3.3
* Apache Web Server with mod rewrite enabled
* Latest Phalcon Framework extension installed/enabled

Then you'll need to create the database and initialize schema:

    echo 'CREATE DATABASE forum' | mysql -u root
    cat schemas/forum.sql | mysql -u root forum

Tests
-----

Uses [Codeception](http://codeception.com) functional tests. Execute:

    php codecept.phar run

Detailed output:

    php codecept.phar run --debug    


License
-------
Phosphorum is open-sourced software licensed under the New BSD License.
