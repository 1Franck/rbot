<?php
/*
 * This file is part of the RBot.
 *
 * (c) Francois Lajoie <o_o@francoislajoie.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RBot\Commands;

use RBot\RBot;
use RBot\Command;
use RBot\Console;

use Illuminate\Database\Capsule\Manager as Capsule;
use PDOException;

/*
 * RBot main command
 */
class RbotCommand extends Command 
{

    static $DATETIME_FORMAT = '';

    /**
     * Command Options
     */
    public function setOptions() 
    {
        // works for -vvv  => verbose = 3
        $this->_options->add('v|version', 'rbot version');
        $this->_options->add('list', 'list all application commands');
        $this->_options->add('install', 'install rbot database');
        $this->_options->add('killdb', 'drop all tables in rbot database');
        $this->_options->add('logout', 'logout user if apply');
    }

    /**
     * [run description]
     * @return [type] [description]
     */
    public function process()
    {
        
        if(!$this->hasResult()) {
            $this->help();
        }
        else {

        }

        //echo $this->help();
        //$this->debug();
    }

    /**
     * Rbot version
     */
    public function opt_version($value)
    {
        Console::noLog();
        Console::nl();
        if(!RBot::cliMode()) {
            Console::Add([
                '██████╗ ██████╗  ██████╗ ████████╗',
                '██╔══██╗██╔══██╗██╔═══██╗╚══██╔══╝',
                '██████╔╝██████╔╝██║   ██║   ██║   ',
                '██╔══██╗██╔══██╗██║   ██║   ██║   ',
                '██║  ██║██████╔╝╚██████╔╝   ██║   ',
                '╚═╝  ╚═╝╚═════╝  ╚═════╝    ╚═╝   ',
                ' ',
            ], ['color' => '#ccc']);
        }

        Console::addAndOutput([
            'rbot version '.RBot::VERSION.' / php '.phpversion().' / '.date('D j F Y H:i:s O'),
            '',
        ], ['color' => '#3498DB']);
    }

    /**
     * Logout
     */
    public function opt_logout()
    {
        @session_destroy();
        Console::noLog();
        Console::addAndOutput('Good bye...');
    }

    /**
     * List App Commands
     */
    public function opt_list()
    {
        Console::nl();

        /*$paths   = [__DIR__, RBot::getCmdPath()];
        $folders = [];
        $rows    = 0;

        foreach($paths as $i => $p) {
            $result = scandir($p);
            if(is_array($result)) {
                $rf = array_diff($result, array('..', '.'));
                foreach($rf as $f) {
                    if($i = 0) {
                        $col2 = '$'.str_replace('command.php', '', strtolower($f));
                    }
                    else {
                        $col1 = strtolower($f);
                    }
                    $prefix  = ($i == 0) ? '$' : '';
                    $files[] = $prefix.str_replace('command.php', '', strtolower($f));
                }

            }
        }



       print_r($files);*/

/*
       $app_commands = array_diff(scandir(), array('..', '.'));
        $rbot_commands = array_diff(scandir(__DIR__), array('..', '.'));

        if(!empty($app_commands) || !empty($rbot_commands)) {
            
            Console::add(count($app_commands).' command'.((count($app_commands) > 1) ? 's' : '').' found:');

            if(count($app_commands) >= count($rbot_commands)) {
                $ar1 = $app_commands;
                $ar2 = $rbot_commands;
            }
            else {
                $ar1 = $rbot_commands;
                $ar2 = $app_commands;
            }

            foreach($ar1 as $i => $cmd) {
                $str = strtolower($cmd);
                if(isset($ar2[$i])) {
                    $str .= str_repeat(" ",10).strtolower($ar2[$i]);
                }
                Console::add($str);
            }
            


            foreach($app_commands as $c) {
                Console::add(' '.strtolower($c));
            }
        }
        */

        Console::nl();
        Console::output();
    }

    /**
     * Install rbot tables
     */
    public function opt_install()
    {
        // database exists ?
        try {
            RBot::db()->schema();
        }
        catch(PDOException $e) {
            throw new Exception\Database('Can\'t find database '.RBot::conf('db.database'));
            return;
        }

        // table exists ?
        if(RBot::db()->schema()->hasTable('queue') || RBot::db()->schema()->hasTable('users')) {
            Console::noLog();
            Console::nl();
            Console::addAndOutput('System already installed or database is not empty');
            return;
        }

        // create tables
        RBot::db()->schema()->create('queue', function($table) {

            $table->engine = 'InnoDB';
            $table->bigIncrements('id')->unsigned();
            //$table->string('command', 255);
            $table->text('task');
            $table->timestamp('dt_created');
            $table->timestamp('dt_executed');
            $table->tinyInteger('repeat')->unsigned()->default(0);
            $table->integer('repeat_time')->unsigned()->default(0);
            $table->integer('execution')->unsigned()->default(0);
        });

        RBot::db()->schema()->create('console', function($table) {

            $table->engine = 'InnoDB';
            $table->bigIncrements('id')->unsigned();
            $table->text('line')->nullable();
            $table->timestamp('dt_created');
            $table->text('command');
            $table->text('options');
        });
        
        Console::AddAndOutput('Installation completed successfully');
    }


    /**
     * Drop rbot tables
     */
    public function opt_killdb()
    {

        if(!RBot::dbCheck()) return;

        $pdo = RBot::db()->connection()->getPdo();
        foreach($pdo->query('SHOW TABLES') as $row)  {
            RBot::db()->schema()->dropIfExists($row[0]);
            //die;
            //echo "$row[0]";
        }

        die('done!');
        //$tables = Capsule::raw("show tables")->get();

       /* print_r($tables);

        if(!empty($tables)) {
            foreach($tables as $t) {
                
            }
        }*/

        //
    }

}