# Project requirements

Business Logic:
We have football teams. Each team has a name, country, money balance and players.
Each player has name and surname.
Teams can sell/buy players.
What we need in our app:
- Make a page (with pagination) displaying all teams and their players.
- Make a page where we can add a new team and its players.
- Make a page where we can sell/buy a player for a certain amount between two teams.

Requirements:
You should use the Symfony PHP Framework (please don't use API Platform).
Follow PSR-12/PER, in JS follow JavaScript Standard Style.
Unit tests are welcome.
Add a README file with installation and startup instructions.
Do not use CRUD bundles like EasyAdmin.
Treat the task as a full-fledged project.

Delivery:
When completed, please publish your project on github.com in a private repo, provide access to the repo to the following 2 github usernames:
aivchen (https://github.com/aivchen)
michalas (https://github.com/michalas)
and confirm your github repo URL here so we can match your application with the github repo.

## Prerequired
You have install PHP8 or newer.
If you want run the server with symfony, install symfony-cli.
You must have mysql and set the configuration at .env file.

## Installation

### 1. Install composer

$ composer install

### 2. Migrate Database

$ php bin/console doctrine:database:create

$ php bin/console make:migration

$ php bin/console doctrine:migrations:migrate

### 3. Run the server

$ symfony server:start

or

$ php -S localhost:8000 -t public/

## Test 
You can test project as following
```
$ php bin/console --env=test doctrine:database:create

$ php bin/console --env=test doctrine:schema:create

$ php bin/phpunit
```