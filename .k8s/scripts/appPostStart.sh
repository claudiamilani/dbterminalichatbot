#!/bin/bash
#
# Copyright (c) 2024. Medialogic S.p.A.
#

# Ensures that required framework directories are in place and execs db migrations.
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/testing
mkdir -p /var/www/html/storage/framework/views
cd /var/www/html && php artisan storage:link --force && php artisan config:cache && php artisan migrate --force