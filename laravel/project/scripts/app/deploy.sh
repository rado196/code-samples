#!/usr/bin/env bash
source "$(dirname $0)/../.helpers.sh"

cd "$(dirname $0)/../../" || exit 1

# print welcome message
print_deployment_started

sleep 2s

# update permissions
print_row_wait "Updating permissions"
bash ./scripts/app/update-permissions.sh > /dev/null 2>&1
exit_fail_or_done $? 10000

# making storage symbolink
print_row_wait "Making storage symbolink"
php artisan storage:link --quiet --no-interaction > /dev/null 2>&1
exit_fail_or_done $? 10001

# installing composer packages
print_row_wait "Installing composer packages"
composer install --no-interaction > /dev/null 2>&1
exit_fail_or_done $? 10002

# update composer packages
print_row_wait "Updating composer packages"
composer update --no-interaction > /dev/null 2>&1
exit_fail_or_done $? 10003

# dumping composer packages
print_row_wait "Dumping composer packages"
composer dump --no-interaction > /dev/null 2>&1
exit_fail_or_done $? 10004

# update node modules
print_row_wait "Updating node modules"
npm install > /dev/null 2>&1
exit_fail_or_done $? 10005

# migrating database
print_row_wait "Migrating database"
php artisan migrate --force > /dev/null 2>&1
exit_fail_or_done $? 10006

# clearing config cache
print_row_wait "Clearing config cache"
php artisan config:clear > /dev/null 2>&1
exit_fail_or_done $? 10007

# clearing data cache
print_row_wait "Clearing data cache"
php artisan cache:clear > /dev/null 2>&1
exit_fail_or_done $? 10008

# clearing route cache
print_row_wait "Clearing route cache"
php artisan route:clear > /dev/null 2>&1
exit_fail_or_done $? 10009

# clearing view cache
print_row_wait "Clearing view cache"
php artisan view:clear > /dev/null 2>&1
exit_fail_or_done $? 10010

# clearing event cache
print_row_wait "Clearing event cache"
php artisan event:clear > /dev/null 2>&1
exit_fail_or_done $? 10011

# clearing npm cache
print_row_wait "Clearing npm cache"
npm cache clean --force > /dev/null 2>&1
exit_fail_or_done $? 10012

# clearing npm cache
print_row_wait "Clearing composer cache"
composer clear-cache --no-interaction > /dev/null 2>&1
exit_fail_or_done $? 10013

# success message
print_finished_successful "Deployment completed successfully"
