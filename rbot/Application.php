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
use RBot\Exception;
use RBot\Console;


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

        $this->_auth();

        $argv = RBot::argv();
        if(empty($argv)) return;

        array_shift($argv);

        if(!empty($argv)) {

            $first_char = substr($argv[0],0,1);

            if($first_char === $this->rbot_command_prefix && strlen($argv[0]) == 1) {
                $classname = 'RBot\Commands\RBotCommand';
            }
            elseif($first_char === $this->rbot_command_prefix && strlen($argv[0]) > 1) {
                $cmd = substr($argv[0], 1, strlen($argv[0]));
                $classname = 'RBot\Commands\\'.ucfirst($cmd).'Command';
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
    private function _auth()
    {
        if(RBot::cliMode()) return;

        if(is_array(RBot::conf('auth'))) {

            //... session is started
            if(session_status() == PHP_SESSION_NONE) {
                throw new Exception\AuthException('No session started, can\'t use auth');
            }

            //... not logged
            if(!isset($_SESSION['logged'])) {

                $argv = RBot::argv();
                array_shift($argv); //remove rbot

                if(!empty($argv) && count($argv) == 3 && substr($argv[0],0,1) === $this->rbot_command_prefix) {
                    // try to log
                    $u_hash = hash(RBot::conf('auth.hash'), $argv[1]);
                    $p_hash = hash(RBot::conf('auth.hash'), $argv[2]);

                    if($u_hash === RBot::conf('auth.user_hash') &&
                        $p_hash === RBot::conf('auth.password_hash')) {

                        $_SESSION['logged'] = true;
                        RBot::argv([]);

                        Console::noLog();
                        Console::nl();
                        Console::addAndDie('Greeting master...');
                    }
                    else {
                        sleep(1);
                        throw new Exception\AuthException('Login failed');
                    }
                }
                else {
                    throw new Exception\AuthException('You need to login first');
                }
            }
        }
    }

}