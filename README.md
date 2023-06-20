# Equip Rent Sport

## Setup instructions
You can run `sh setup.sh` which will do all the steps for you ;)

### Step 1 copy .env file
`cp .env.example .env`

### Step 2 Sail and composer setup.
When using laravel sail. We need to run `sail` command before doing anything else. You must run following command which will ask docker to install all required dependencies through docker with required php version.

```shell
sudo docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
```

Now this will make `sail` command available for us to be used for installation purposes.

### Step 3 Build sail
We need to prepare containers, etc. run `./vendor/bin/sail build` it will take a bit.

### Step 4 Start Sail container
`./vendor/bin/sail up -d`

Now you should have everything ready for the env to work.

### Step 5 Generate laravel key
`./vendor/bin/sail artisan key:gen`

Now, you should have a domain called `http://laravel.test`

### Step 6 Migrate tables
`./vendor/bin/sail artisan migrate`

That's it.

### Some commands
1. If you hit some address already in use errors `sudo service nginx stop && service apache2 stop && service mysql stop`

2. update `~/.bashrc` or `~/.zshrc` to make your life easier.
   `alias sail="./vendor/bin/sail"`

3. If you are getting permission issue at step 6 for the moment (LOCAL ONLY) run `chmod -R 777 storage/logs`

4. For using git you can run `./vendor/bin/sail bash` then `git` for authentication you can generate an ssh key with `ssh-keygen` store in default location. run `cat ~/.ssh/id_rsa.pub` copy content goto `https://bitbucket.org/account/settings/ssh-keys/` add your key. Now, you should be able to make a push from container but this is just temporary not permanent.

### Linting
1. To ensure code formatting and standards, run `composer phpcs`  for static analysis
2. To fix issues from above automatically run `composer phpcbf`. Not all issues will be autofixed, manual fixes might be needed.

### Testing

- Run `./vendor/bin/phpunit` to run the entire test suite

- To run a single test `./vendor/bin/phpunit --filter test_if_a_product_can_be_created`
