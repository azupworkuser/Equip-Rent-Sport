#!/bin/bash

docker info > /dev/null 2>&1

echo "Checking if .env file exists";
if [ ! -f .env ]; then
    echo "Copying .env.example";
    cp .env.example .env;
    echo 'copied!';
fi

echo ""
echo "Running docker setup for installing composer files"
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs

echo ""
echo "Running laravel sail build command"
./vendor/bin/sail build --no-cache
echo "Done!";

CYAN='\033[0;36m'
LIGHT_CYAN='\033[1;36m'
WHITE='\033[1;37m'
NC='\033[0m'

echo ""
echo "Now you can run ./vendor/bin/sail up -d"
./vendor/bin/sail up -d

echo ""
echo "Generating a key for laravel setup"
./vendor/bin/sail artisan key:gen

echo ""
echo "Migrating"
./vendor/bin/sail artisan migrate

echo ""

if sudo -n true 2>/dev/null; then
    sudo chown -R $USER: .
else
    echo -e "${WHITE}Please provide your password so we can make some final adjustments to your application's permissions.${NC}"
    echo ""
    sudo chown -R $USER: .
fi

echo ""
echo -e "${WHITE}Start Hacking run ./vendor/bin/sail up"

echo "completed!";
