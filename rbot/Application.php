<?php
/*
 * This file is part of the RBot.
 *
 * (c) Francois Lajoie <o_o@francoislajoie.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RBot;

use RBot\RBot;
//use RBot\Authentication;
use RBot\Exception;
use RBot\Console;


/*
 * RBot application
 */
abstract class Application
{

    static $RBOT_CMD_PREFIX = '$';

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

        $this->_auth();

        $argv = RBot::argv();
        if(empty($argv)) return;

        array_shift($argv);

        if(!empty($argv)) {

            // order is:  
            // "$ [with_or_without_arg]"
            // "$command"
            // "? "

            $first_char = substr($argv[0],0,1);

            if($first_char === self::$RBOT_CMD_PREFIX && strlen($argv[0]) == 1) {
                $classname = 'RBot\Commands\RBotCommand';
            }
            elseif($first_char === self::$RBOT_CMD_PREFIX && strlen($argv[0]) > 1) {
                $cmd = substr($argv[0], 1, strlen($argv[0]));
                $classname = 'RBot\Commands\\'.ucfirst($cmd).'Command';
            }
            elseif($first_char === "?" && strlen($argv[0]) == 1) {
                $cmd = substr($argv[0], 1, strlen($argv[0]));
                $classname = 'RBot\Commands\DdgCommand';
            }
            else {
                $cmd = $argv[0];
                $classname = NS_APP.ucfirst($cmd).'\\'.ucfirst($cmd).'Command';
            }

            if(!class_exists($classname, true)) {
                throw new Exception\CommandNotFound('Command not found... $ --list to view all commands');
            }

            RBot::run(new $classname);
        }
    }

    /**
     * Auth for the web cli
     */
    public function auth()
    {
        $this->_auth();
    }

    /**
     * Auth for the web cli
     */
    private function _auth()
    {
        if(RBot::cliMode()) return;

        $auth = new Authentication;
    }
}