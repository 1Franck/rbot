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

To use the webcli and/or queue system, you must install RBot database.

Once you have installed dependencies with composer,
create an empty database and put infos in your `app/configs/rbot.php`

Open a command line and type this:

```
rbot $ -v
```

You should see message like this:

`rbot version 0.1b / php 5.6.3 / Sat 30 May 2015 08:35:55 -0400`

If you see something else, this may be due to a problem with database configuration.

Now you are ready to install RBot database with:

```
rbot $ --install
```

### Configure the queue system with crontab

Create a cron job to run every minutes.
```
* * * * * php path/to/cron.php
```

### Queue system

To add a command to rbot queue:

```
rbot $queue -r -t=3600 / [command] [options]
```

In this example, rbot will execute the command every hour. If you don't 
specify `-r`, rbot will execute the command only one time.