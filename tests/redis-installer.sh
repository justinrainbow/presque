#!/bin/bash

git clone git://github.com/nicolasff/phpredis.git
sh -c "cd phpredis && phpize && phpize && ./configure && sudo make install"
echo "extension=redis.so" >> `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`