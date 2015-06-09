#!/usr/bin/env bash

apt-get install nginx

cp ./tests/config/nginx-travis.conf /etc/nginx/nginx.conf
echo 'pforum.loc 127.0.0.1' >> /etc/hosts
nginx -t
/etc/init.d/nginx restart
curl -I pforum.loc
