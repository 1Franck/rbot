# RBot
Experimental CLI and WebCLI php application framework with cron jobs system

### Requirements
    PHP 5.5+
    PHP Composer
    MySQL or MariaDB (for cron jobs system)
    HTTP server(for WebCLI)
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
  
`3` (optionnal) Install grunt for editing/theming rbot WebCLI assets 


### Application Structure

```
/app                    Your application folder
../configs              App configurations folder
../Commands             App commands scripts
....FoobarCommand.php   'foobar' Command Class
..app.php               App bootstrap class
/public                 WebCLI public folder
../assets               Web assets folder for rbot
..index.php             WebCLI app point entry
/rbot                   RBot lib
../Commands             RBot commands
/resources              Web assets(css,js) source for gruntjs
cron.php                RBot cron runner (crontab)
rbotc                   CLI app point entry for linux
rbotc.bat               for windows command (shorcut for rbotc)
```

### CLI vs WebCLI syntax

CLI syntax : `# rbotc [command] [args]`
```
$ rbotc mycommand -a hello -b -c=1
```

WebCLI syntax : `[command] [args]`
```
mycommand -a hello -b -c=1
```

To differentiate your application commands from RBot commands, use prefix symbol `#` before command name. 
The following example will execute `say` command (`rbot/Commands/SayCommand.php`) :

```
$ rbotc #say Hi!
```

And the following example will execute, if exists, `say` command (`app/Commands/Say/SayCommand.php`).
```
$ rbotc say Hi!
```
There is one exception, if you specify RBot prefix `#` without the name, it will 
automatically use rbot command (`rbot/Commands/RBotCommand.php`).
```
$ rbotc # -v
```
Is the same as:
```
$ rbotc #rbot -v
```
The RBot prefix `#` can be changed in your app class.

### RBot configuration and database installation

To use the WebCLI and/or queue system, you must configure and install RBot database.

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
rbotc # --install
```

You should see message like this:

`Installation completed successfully!`

If you see something else, this may be due to a problem with database configuration.

### WebCLI configuration

:exclamation: If you plan to use the rbot WebCLI on a web server, don't forget to add authentication to your app configuration.
For obvious security reasons, i don't recommend WebCLI if your app do low level things. 
Be careful when using the WebCLI since all console data can be intercepted.

```php
'auth' => [
    'hash'          => 'sha512',
    'user_hash'     => 'user hash result',
    'password_hash' => 'password hash result',
    'ip'            => '127.0.0.1', // optionnal, can be an array (ex: ['127.0.0.1', 'X.X.X.X', ...])
],

```

Log syntax: 
```
# [username] [password]
```

The WebCLI use angularjs. Unlike in CLI, excecuted commands
in the WebCLI are not outputed directly. The WebCLI console grab (http pull)
latest lines data from table `console` where every lines is stored.

The advantage of this technique is that it bind the CLI ouput with the WebCLI, so
all output in CLI will also appear on logged WebCLI.

The disavantage is that http pull may induce a stress if the TTR(time to refresh) is too high or task(s) take to much times/memory to execute.

Finally, you can't use the WebCLI if you don't install rbot database, but you still can use rbot in CLI.


### Configure the queue system with crontab

Create a cron job to run every minutes.
```
* * * * * php path/to/cron.php

```
### Use the queue system

To add a command to rbot queue `#queue [-r] [-t=<int>] / [#][command] [options]`. 
In the next example, rbot will execute the command every hour. 
If you don't specify `-r`, rbot will execute the command only one time.:

```
#queue -r -t=3600 / #say Hello you!
```

List current task in queue:
```
#queue -l
```

Clear all item(s) in queue:
```
#queue -c
```

Clear a specific item in queue:
```
#queue -c 21
```

Execute manually all due tasks in queue:
```
#queue --run
```

### Create your own commands
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

### TODO

- finish doc