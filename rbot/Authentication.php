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
use RBot\Console;
use RBot\Application;
use RBot\Exception;

/*
 * RBot Authentication
 */
class Authentication
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_auth();
    }

    /**
     * Auth for the web cli
     */
    private function _auth()
    {
        if(is_array(RBot::conf('auth'))) {

            //... session is started
            if(session_status() == PHP_SESSION_NONE) {
                throw new Exception\AuthException('No session started, can\'t use auth');
            }

            //... ip verification
            if(!empty(RBot::conf('auth.ip'))) {

                $ip = RBot::conf('auth.ip');
                $ip_auth = false;

                if(is_array($ip) && in_array($this->_getIp(), $ip)) $ip_auth = true;
                elseif($ip === $this->_getIp()) $ip_auth = true;

                if($ip_auth === false) {
                    throw new Exception\AuthException('Can\'t validate your address');
                }
            }

            //... not logged
            if(!isset($_SESSION['logged'])) {

                $argv = RBot::argv();

                if(!is_array($argv)) {
                    throw new Exception\AuthException('You need to login first');
                }

                array_shift($argv); //remove rbot

                if(!empty($argv) && count($argv) == 3 && substr($argv[0],0,1) === Application::$RBOT_CMD_PREFIX) {
                    // try to log
                    $u_hash = hash(RBot::conf('auth.hash'), $argv[1]);
                    $p_hash = hash(RBot::conf('auth.hash'), $argv[2]);

                    if($u_hash === RBot::conf('auth.user_hash') &&
                        $p_hash === RBot::conf('auth.password_hash')) {

                        $_SESSION['logged'] = true;
                        RBot::argv('');
                        Console::nl();
                        Console::addAndOutput('Greeting master...');
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

    /**
     * Get client ip
     * 
     * @return string
     */
    private function _getIp()
    {
        $ip = '';

        //check ip from share internet
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) { 
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        //to check ip is pass from proxy
        elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { 
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return trim($ip);
    }

}