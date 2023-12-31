#!/bin/bash

Env=$(grep APP_ENV .env | cut -d '=' -f2)
environment=${2:-dev}
echo -n "[Run only dev!] Are you sure, this script will delete the current database (y/n)? "
read answer

if [ "$answer" != "${answer#[Yy]}" ] && [ "$Env" != "prod" ] ;then

  composer install
  php bin/console lexik:jwt:generate-keypair
  php bin/console d:s:u --force
  php bin/console doctrine:fixtures:load
  php bin/console --env=test doctrine:database:create
  php bin/console --env=test d:s:u --force

  chmod 777 -R var/cache/*

else
  echo exit
fi