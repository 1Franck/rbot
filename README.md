# RBot
Experimental CLI and webCLI php application framework with cron jobs system

### Requirements
    PHP 5.5+
    PHP Composer
    MySQL or MariaDB (for cron jobs system)
    HTTP server(for web cli)
    Node and Gruntjs (for editing web assets only)

### Project Installation

`1` Download the repositery or clone it

`2` Install dependencies via Composer

```
$ composer install
```
This will install those libraries:
 - [illuminate/database](https://github.com/illuminate/database) (Laravel 5.1 Database Component)
 - [c9s/GetOptionKit](https://github.com/c9s/GetOptionKit) (A powerful GetOpt toolkit for PHP)
 - [sinergi/config](https://github.com/sinergi/config) (PHP configurations loading library)
  
`3` (optionnal) Install grunt for editing/theming rbot webcli assets 


### Application Structure

```
/app                    Application folder
../configs              Configurations folder
../Commands             App commands scripts
....FoobarCommand.php   'foobar' Command Class
..app.php               App bootstrap
/public                 Webcli public folder
../assets               Web assets folder for rbot
..index.php             Webcli app point entry
/rbot                   RBot lib
../Commands             RBot commands
/resources              Web assets src for gruntjs
cron.php                RBot cron runner (crontab)
rbotc                   Cli app point entry for linux
rbotc.bat                for windows command (shorcut for rbotc)
```

### Cli vs WebCli syntax

Cli syntax : `$ rbotc [command] [args]`
```
$ rbotc mycommand -a hello -b -c=1
```

WebCli syntax : `[command] [args]`
```
mycommand -a hello -b -c=1
```

To differentiate your application commands from RBot commands, use prefix symbol `$` before command name. 
The following example will execute `say` command (`rbot/Commands/SayCommand.php`) :

```
$ rbotc $say Hi!
```

And the following example will execute, if exists, `say` command (`app/Commands/Say/SayCommand.php`).
```
$ rbotc say Hi!
```
There is one exception, if you specify RBot prefix `$` without the name, it will 
automatically use rbot command (`rbot/Commands/RBotCommand.php`).
```
$ rbotc $ -v
```
Is the same as:
```
$ rbotc $rbot -v
```
The RBot prefix `$` can be changed in your app class.

### RBot configuration and database installation

To use the WebCli and/or queue system, you must configure and install RBot database.

Once you have installed dependencies with composer, create an empty database and put connection 
infos in your `app/configs/dev/app.php`

```php
<?php
return [
    'db' => [
        'driver'    => 'mysql',
        'host'      => '127.0.0.1',
        'database'  => 'rbot_dev',
        'username'  => 'root',
        'password'  => '',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
    ],
];
```

Open a command line and type this:

```
rbotc $ --install
```

You should see message like this:

`Installation completed successfully!`

If you see something else, this may be due to a problem with database configuration.


### Configure the queue system with crontab

Create a cron job to run every minutes.
```
* * * * * php path/to/cron.php

```
### Use Queue system

To add a command to rbot queue `$queue [-r] [-t=<int>] / [command] [options]`. Example:

```
$queue -r -t=3600 / $say Hello you!
```

In this example, rbot will execute the command every hour. If you don't specify `-r`, rbot will execute the command only one time.


### Webcli configuration

If you plan to use the rbot webcli on a web server, don't forget to add authentication to your app configuration.
For obvious security reasons, i don't recommend webcli if your app do low level things. 
Be careful when using the webcli since all console data can be intercepted.

```php
'auth' => [
    'hash'          => 'sha512',
    'user_hash'     => 'user hash result',
    'password_hash' => 'password hash result',
    'ip'            => '127.0.0.1', // optionnal, can be an array (ex: ['127.0.0.1', 'X.X.X.X', ...])
],

```




### Command class example
```php
<?php
namespace RBot\Commands;

use RBot\RBot;
use RBot\Command;
use RBot\Console;

/*
 * Generic test function for rbot 
 */
class FoobarCommand extends Command 
{
    /**
     * Command Options
     */
    public function setOptions() 
    {
        $this->_options->add('f|foo:', 'option requires a value.' )->isa('String');
        $this->_options->add('d|date', 'show date time' );
        $this->_options->add('url:', 'url option')->isa('url');
        $this->_options->add('ip:', 'ip option')->isa('ip');
        $this->_options->add('ipv4:', 'ipv4 option')->isa('ipv4');
        $this->_options->add('ipv6:', 'ipv6 option')->isa('ipv6');
        $this->_options->add('email:', 'email option')->isa('email');
    }

    /**
     * Process the command
     */
    public function process()
    {
        if(!$this->hasResult() && !$this->hasErrors()) $this->help();
    }

    /**
     * Command option "foo"
     * 
     * @param  mixed $value
     */
    public function optionFoo($value)
    {
        Console::nl();
        Console::add("Foo >>> ".$value);
        Console::output();
    }

    /**
     * Command option "date"
     * 
     * @param  mixed $value
     */
    public function optionDate($value)
    {
        Console::add('Hi, here\'s the date: {{now}}', 
                     'important', 
                     ['now' => date('Y-m-d H:i:s')]);
        Console::output();
    }

    /**
     * Command option "ip"
     * 
     * @param  mixed $value
     */
    public function optionIp($ip)
    {
        Console::add("ip >>> ".$ip, 'success');
        Console::output();
    }

    //....
}

```

