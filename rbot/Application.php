<?php
/*
 * This file is part of the RBot app.
 *
 * (c) Francois Lajoie <o_o@francoislajoie.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RBot;

use RBot\RBot;
use RBot\Exception\CommandNotFound;


/*
 * RBot application
 */
abstract class Application
{

    public $rbot_command_prefix = '$';

    /**
     * Call app init()
     */
    public function __construct()
    {
        $this->init();
    }

    public function init(){}

    /**
     * Run app with current argv
     *
     * @param  strign $new_argv
     */
    public function run($new_argv = null)
    {
        if(isset($new_argv)) {
            RBot::argv($new_argv);
        }

        $argv = RBot::argv();
        if(empty($argv)) return;

        array_shift($argv);

        if(!empty($argv)) {

            if(substr($argv[0],0,2) === $this->rbot_command_prefix) {
                $classname = 'RBot\Commands\RBotCommand';
            }
            else {
                $cmd = $argv[0];
                $classname = NS_APP.ucfirst($cmd).'\\'.ucfirst($cmd).'Command';

                if(!class_exists($classname, true)) {
                    throw new CommandNotFound("Command not found... $ -l to view all commands");
                }
            }
            RBot::run(new $classname);
        }
    }
}