# RBot
####Experimental CLI and webCLI php application framework with cron jobs system

### Requirements
    PHP 5.4+
    MySQL or MariaDB (for queuing system)
    http server(for web cli)


### Installation

`1` Download the repositery or clone it

`2` Install dependencies via Composer

```
$ composer install
```
This will install those libraries:
 - illuminate/database (Laravel 5.1 Database Component)
 - c9s/GetOptionKit (A powerful GetOpt toolkit for PHP)
 - sinergi/config (PHP configurations loading library)
  
`3` Install grunt for editing/theming rbot webcli assets (optionnal)

```
$ npm install grunt --save-dev
```