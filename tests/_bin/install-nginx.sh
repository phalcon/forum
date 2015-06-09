#!/usr/bin/env bash

apt-get install nginx

cp ../config/nginx.conf /etc/nginx/nginx.conf
/etc/init.d/nginx restart
