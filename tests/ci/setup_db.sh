#!/usr/bin/env bash
#
#  Phosphorum
#
#  Copyright (c) 2013-present Phalcon Team (https://www.phalconphp.com)
#
#  This source file is subject to the New BSD License that is bundled
#  with this package in the file LICENSE.txt.
#
#  If you did not receive a copy of the license and are unable to
#  obtain it through the world-wide-web, please send an email
#  to license@phalconphp.com so we can send you a copy immediately.

echo 'CREATE DATABASE phosphorum CHARSET=utf8 COLLATE=utf8_unicode_ci' | mysql -u root
echo "CREATE USER 'phosphorum'@'%' IDENTIFIED BY 'secret'" | mysql -u root
echo "GRANT ALL PRIVILEGES ON phosphorum.* TO 'phosphorum'@'%' WITH GRANT OPTION" | mysql -u root
cat schemas/forum.sql | mysql -u root phosphorum
