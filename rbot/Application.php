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
use RBot\Exception;
use RBot\Console;


/*
 * RBot application
 */
abstract class Application
{

    static $RBOT_CMD_PREFIX = '#';

    /**
     * Call app init()
     */
    public function __construct()
    {
        $this->init();
        $this->_parseRequest();
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

        $this->auth();

        $argv = RBot::argv();
        if(empty($argv)) return;

        array_shift($argv);

        if(!empty($argv)) {

            // order is:  
            // "# [with_or_without_arg]"
            // "#command"
            // "? "
            // "command"

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
        if(RBot::cliMode() || !$this->hasAuth()) return;
        $auth = new Authentication;
    }

    /**
     * Return
     * @return boolean
     */
    public function hasAuth()
    {
        $auth_conf = RBot::conf('auth');
        if(isset($auth_conf) && is_array($auth_conf)) {
            return true;
        }
        else return false;
    }

    /**
     * Parse angular request
     */
    public function _parseRequest()
    {
        if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $req = json_decode(file_get_contents('php://input'), true);

            foreach($req as $k => $v) {
                $method = 'request'.ucfirst($k);
                if(method_exists($this, $method)) {
                    $this->$method($v);
                }
            }
        }
    }

    public function requestH($value)
    {
        # code...
    }

    public function requestCh($value)
    {

    }

    public function requestCmd($value)
    {
        
    }
}