#!/usr/bin/env sh

wget -Otideways-php.tar.gz https://github.com/tideways/php-profiler-extension/archive/v4.1.1.tar.gz
tar xvfz tideways-php.tar.gz -C /tmp
cd /tmp/php-profiler-extension-4.1.1
phpize
./configure
make
