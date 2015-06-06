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
../assets               web assets folder for rbot
..index.php             webcli app point entry
/rbot                   RBot lib
../Commands             RBot commands
/resources              web assets src for gruntjs
cron.php                RBot cron runner (crontab)
rbotc                   cli app point entry for linux
rbot.bat                for windows command (shorcut for rbotc)
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
    public function opt_foo($value)
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
    public function opt_date($value)
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
    public function opt_ip($ip)
    {
        Console::add("ip >>> ".$ip, 'success');
        Console::output();
    }

    //....
}

```

### Queue system

To add a command to rbot queue:

```
rbot $queue -r -t=3600 / [command] [options]
```

In this example, rbot will execute the command every hour. If you don't 
specify `-r`, rbot will execute the command only one time.

