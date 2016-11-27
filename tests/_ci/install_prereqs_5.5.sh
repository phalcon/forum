#!/usr/bin/env bash

DIR=$(readlink -enq $(dirname $0))
CFLAGS="-O2 -g3 -fno-strict-aliasing -std=gnu90";

pecl channel-update pecl.php.net

enable_extension() {
	ENABLED=$(php -m | grep $1)

    if [ -z "${ENABLED}" ]; then
    	echo -e "Enabling the ${1} extension..."
        phpenv config-add "$DIR/$1.ini"
    else
    	echo -e "The ${1} extension already enabled. Skip..."
    fi
}

install_extension() {
    INSTALLED=$(pecl list $1 | grep 'not installed')

    if [ -z "${INSTALLED}" ]; then
        printf "\n" | pecl upgrade $1 &> /dev/null
    else
        printf "\n" | pecl install $1 &> /dev/null
    fi

    enable_extension $1
}

enable_extension memcached
install_extension imagick
