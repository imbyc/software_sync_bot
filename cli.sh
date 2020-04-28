#!/bin/bash


docker run -it --rm --name cli \
--env COMPOSER_HOME=/tmp \
--volume $(pwd):/var/www/code \
-w /var/www/code -u $(id -u):$(id -g) \
--entrypoint /bin/sh \
registry.cn-hangzhou.aliyuncs.com/public0/hyperf-base-image