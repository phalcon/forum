#!/usr/bin/env bash

apt-get install -y nginx

cp ./tests/config/nginx-travis.conf /etc/nginx/nginx.conf
echo '127.0.0.1 pforum.loc' | sudo tee -a /etc/hosts

nginx -v
nginx -t
/etc/init.d/nginx restart

curl -I pforum.loc
