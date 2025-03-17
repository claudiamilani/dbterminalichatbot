#!/bin/bash
# Change APP_PATH to match application root path
APP_PATH='/mdl/lft-app'
echo "LFT-APP AutoUpdate script started on $(date +%d-%m-%Y%t%H:%M)"
cd $APP_PATH

if [[ -f "$APP_PATH/storage/framework/down" ]]; then
    echo "Application was already down"
    APP_STATUS='DOWN'
else
    APP_STATUS='UP'
    php artisan down
fi

git reset --hard
git pull
composer update --no-dev
if [[ $1 == '-m' ]]; then
        echo 'Rebuilding database. Existing data will be erased';
        php artisan migrate:fresh --force
else
        php artisan migrate --force
fi
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan horizon:publish
php artisan horizon:terminate
if [[ $APP_STATUS == 'UP' ]]; then
    php artisan up
else
    echo 'Keeping application in the original DOWN state'
fi