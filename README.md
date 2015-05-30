# RBot
Experimental CLI and webCLI php application framework with cron jobs system

### Requirements
    PHP 5.4+
    MySQL or MariaDB (for cron jobs system)
    http server(for web cli)

### Project Installation

`1` Download the repositery or clone it

`2` Install dependencies via Composer

```
$ composer install
```
This will install those libraries:
 - illuminate/database (Laravel 5.1 Database Component)
 - c9s/GetOptionKit (A powerful GetOpt toolkit for PHP)
 - sinergi/config (PHP configurations loading library)
  
`3` (optionnal) Install grunt for editing/theming rbot webcli assets 

```
$ npm install grunt --save-dev
```


### RBot Installation

Once you have installed dependencies with composer,
create a database and put infos in your `app/configs/rbot.php`

Open a command line and type this:

```
rbot $ -v
```

You should see message like this:

`rbot version 0.1b / php 5.6.3 / Sat 30 May 2015 08:35:55 -0400`

If you see something else, this may be due to a problem with database configuration.

Now you can install RBot database with:

```
rbot $ --install
```
