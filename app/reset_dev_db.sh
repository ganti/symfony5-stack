#!/bin/sh
cp .env.dev .env.dev.local
php bin/console d:d:d --force -e dev
php bin/console d:d:c -e dev
php bin/console d:s:c -n -e dev
php bin/console d:m:m -n -e dev
php bin/console d:f:l -n -e dev
