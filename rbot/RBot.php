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

use RBot\Exception;
use PDOException;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Container\Container as Container;
use Illuminate\Events\Dispatcher as Dispatcher;

use Sinergi\Config\Config as Config;

use GetOptionKit\Option;
use GetOptionKit\OptionCollection;
use GetOptionKit\ContinuousOptionParser;

/**
 * RBot core
 */
class RBot
{
    const PRODUCTION = 'prod';
    const SANDBOX    = 'dev';
    const VERSION    = '0.3b';

    private static $_config;
    private static $_db;
    private static $_env;
    private static $_cmd_path;

    /**
     * Init RBot config and database connexion
     * 
     * @param  string $env
     */
    static function init($env)
    {
        self::$_env      = $env;
        self::$_cmd_path = realpath(__DIR__).'/../app/Commands';
        self::$_config   = new Config(__DIR__.'/../app/configs');
        self::$_config->setEnvironment($env);

        //print_r(self::$_config);

        self::_connect();
    }

    /**
     * Get conf with restriction
     * 
     * @param   string $opt  
     * @return  mixed
     */
    static function conf($opt)
    {
        $no = ['db'];
        if(in_array(trim($opt), $no)) return;
        return self::_conf($opt);
    }

    /**
     * Check if conf exists
     * 
     * @param  string  $opt
     * @return boolean     
     */
    static function hasConf($opt)
    {
        $conf_opt = self::_conf($opt);
        if(isset($conf_opt)) return true;
        return false;
    }

    /**
     * Get conf 
     * 
     * @param   string $opt  
     * @return  mixed
     */
    static public function _conf($opt)
    {
        return self::$_config->get('app.'.$opt);
    }

    /**
     * Get db manager
     *
     * @return object
     */
    static function db()
    {
        return self::$_db;
    }

    /**
     * Return true or false
     * 
     * @return boolean
     */
    static function dbCheck($table = null)
    {
        try {
            if(!is_object(self::db())) return false;
            self::db()->schema();
            if(isset($table)) {
                return self::db()->schema()->hasTable($table);
            }
            return true;
        }
        catch(PDOException $e) {
            return false;
        }
    }

    /**
     * Get current env
     * 
     * @return string
     */
    static function env()
    {
        return self::$_env;
    }

    /**
     * Get App Commands path
     * 
     * @return string
     */
    static function getCmdPath()
    {
        return self::$_cmd_path;
    }

    /**
     * Rbot in cli mode ?
     * 
     * @return boolean
     */
    static function cliMode()
    {
        if(php_sapi_name() === 'cli') return true;
        return false;
    }

    /**
     * Retreive/Set arguments
     * 
     * @param  array|string $new_argv refine request
     * @return array
     */
    static function argv($new_argv = null)
    {
        global $argv;

        if(isset($new_argv)) {
            if(is_string($new_argv)) {
                $argv = explode(' ', $new_argv);
            }
            else $argv = $new_argv;
        }
 
        if((!isset($argv) || !is_array($argv)) && isset($_SERVER['argv'])) {
            return $_SERVER['argv'];
        }
        return $argv;
    }

    /**
     * Retreive arguments as string
     * 
     * @return array
     */
    static function argvString()
    {
        $argv = self::argv();
        if(is_array($argv)) {
            return implode(' ', $argv);
        }
        elseif(is_string($argv)) {
            return $argv;
        }
    }

    /**
     * Check if string exists in argv
     * 
     * @param  object  $option GetOptionKit\Option instance
     * @return boolean         
     */
    static function hasArgv(Option $option)
    {
        $argv = self::argv();
        if(in_array('-'.$option->short,$argv) || in_array('--'.$option->long,$argv)) {
            return true;
        }
        return false;
    }

    /**
     * Connect to RBot database
     */
    private static function _connect()
    {
        if(is_array(self::_conf('db')) && !empty(self::_conf('db'))) {

            try {
                self::$_db = new Capsule;
                self::$_db->addConnection(self::_conf('db'));
                self::$_db->setEventDispatcher(new Dispatcher(new Container));
                self::$_db->bootEloquent();
                self::$_db->setAsGlobal();
            }
            catch(PDOException $e) {
                throw new Exception\Database('Can\'t connect to database');
            }


            

            self::$_config->set('rbot.'.self::$_env.'.db', []);
        }
        else {
            throw new Exception\ConfigMissing('Database "db" config missing');
        }
    }

    /**
     * Execute shell command
     * 
     * @param  string  $cmd             
     * @param  string  $opts            
     * @param  boolean $cmd_opts_string 
     * @return string                   
     */
    static function shell($cmd, $opts = '', $cmd_opts_string = false)
    {
        if($cmd_opts_string) {
            $command = 'php -a '.self::$_cmd_path.'/'.$cmd;
        }
        else {
            $command = 'php -a '.self::$_cmd_path.'/'.$cmd.'/'.$cmd.'.php '.$opts;
        }

        $esc = escapeshellcmd($command);
        $last_line = system($esc, $result);

        return $result;
    }

    /**
     * Run a command object
     * 
     * @param  Command $cmd
     */
    static function run(Command $cmd, $new_argv = null)
    {
        if(isset($new_argv)) self::argv($new_argv);
        $cmd->run();
    }
}