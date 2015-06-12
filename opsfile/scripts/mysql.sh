#!/usr/bin/env bash

echo ">>> Installing MySQL Server $2"
echo $MYSQL_PASSWORD
echo $DB_NAME

#[["$MYSQL_PASSWORD" ]] && { echo "!!! MySQL root password not set. Check the Vagrant file."; exit 1; }

mysql_package=mysql-server

# Install MySQL Server
# -qq implies -y --force-yes
sudo apt-get install -qq $mysql_package
service mysql start

# Setup password and create database
mysqladmin -u root password $MYSQL_PASSWORD
mysqladmin -u root -p$MYSQL_PASSWORD create $DB_NAME

# Import database
mysql -u root -p$MYSQL_PASSWORD  $DB_NAME <  /tmp/forum.sql

