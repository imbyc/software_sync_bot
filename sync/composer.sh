#!/bin/bash

docker run -it --rm --name composer \
--volume ${COMPOSER_HOME:-$HOME/.composer}:/tmp \
-v $(pwd):/var/www/code \
-w /var/www/code -u $(id -u):$(id -g) \
registry.cn-hangzhou.aliyuncs.com/public0/hyperf-base-image \
composer $*