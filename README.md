# SnowTricks
[![SymfonyInsight](https://insight.symfony.com/projects/46512455-8112-40b4-b626-6de244ff0852/mini.svg)](https://insight.symfony.com/projects/46512455-8112-40b4-b626-6de244ff0852)
![Static Badge](https://img.shields.io/badge/symfony-6.3-000000?logo=symfony)
![Static Badge](https://img.shields.io/badge/bootstrap-5.1.3-7952B3?logo=bootstrap)
![Static Badge](https://img.shields.io/badge/php-8.1-777BB4?logo=php)
![Static Badge](https://img.shields.io/badge/phpmyadmin-5.1.1-6C78AF?logo=phpmyadmin)
![Static Badge](https://img.shields.io/badge/twig-3.x-green?logo=twig)

# Project 6

Building of a community website themed around snowboard tricks using Symfony framework.
You can add description, pictures, video urls to create a wiki-like article and talk about them with other riders ! 

# Getting started

This site is to used in a [WAMP](https://www.wampserver.com/) environement, you can use LAMP or XAMP and adapt the settings needed.

- First fork the repository or download it from [this](https://github.com/BenjVA/SnowTricks) page.
- Now download [Composer](https://getcomposer.org/download/) if it's not already on your machine. It will be needed to manage libraries.
- [Scoop](https://scoop.sh/) is a command line installer for Windows used to install [Node.js](https://nodejs.org/en/download) using the command ```scoop install node.js```, to manage assets.
- Then in ***.env*** replace line 27 by the DBMS settings you use, and line 39 by the mailer you use. Local mails will not function unless you configure with [Mailhog](https://github.com/mailhog/MailHog) or similar.
- Install [Symfony CLI](https://symfony.com/download) if you don't have it already installed.
- Then run this to install all dependencies of the project
```bash
composer install
```
- Create a new database using the DATABASE_URL in ***.env*** in a terminal with : 
```bash
php bin/console doctrine:database:create
```
- Generate the database schema :
```bash
php bin/console doctrine:schema:update --force
```
- and run this command to load the initial data fixtures :
```bash
php bin/console doctrine:fixtures:load
```


### Now you should be ready to launch the symfony server
Run
```bash
symfony server:start
```
Open a browser and go to localhost:8000 where the site should be displayed !

#
Created for the Openclassrooms PHP/Symfony apps developer training.
