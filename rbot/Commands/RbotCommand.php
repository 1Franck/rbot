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

    /**
     * Command Options
     */
    public function setOptions() 
    {
        $this->_options->add('v|version', 'rbot version');
        $this->_options->add('list', 'list all application commands');
        $this->_options->add('install', 'install rbot database');
        $this->_options->add('killdb', 'drop all tables in rbot database');
        $this->_options->add('logout', 'logout user if apply');
    }

    /**
     * process opt, nothing special here
     */
    public function process()
    {
        if(!$this->hasResult()) {
            $this->help();
        }
    }

    /**
     * Rbot version
     */
    public function opt_version($value)
    {
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
        Console::addAndOutput('Good bye...');
        @session_destroy();
    }

    /**
     * List App Commands
     */
    public function opt_list()
    {
        $paths    = [__DIR__, RBot::getCmdPath()];
        $rows     = 0;
        $col1     = [];
        $col2     = [];
        $list_pad = 20;

        foreach($paths as $i => $p) {
            $result = scandir($p);
            if(is_array($result)) {
                $rf = array_diff($result, array('..', '.'));
                foreach($rf as $f) {
                    $prefix = '';
                    if($i == 0) {
                        $prefix = '$';
                        $col2[] = $prefix.str_replace('command.php', '', strtolower($f));
                    }
                    else {
                        $col1[] = strtolower($f);
                    }
                    
                    //$files[] = $prefix.str_replace('command.php', '', strtolower($f));
                }
            }
        }

        if(count($col1) >= count($col2)) {
            $rows = count($col1);
        }
        else $rows = count($col2);

        $output = [];
        for($i=0;$i<$rows;++$i) {
            if(isset($col1[$i])) {
                $output[$i] = str_pad($col1[$i], 20);
            }
            else $output[$i] = str_pad('', 20);
        
            if(isset($col2[$i])) {
                $output[$i] .= $col2[$i];
            }
        }

        $header = count($col1).' app commands, '.count($col2).' rbot commands found:';
        Console::nl();
        Console::addAndOutput($header);
        Console::separator(strlen($header));
        Console::addAndOutput($output);
        Console::separator(strlen($header));
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
            $table->timestamp('dt_created')->nullable();
            $table->timestamp('dt_executed')->nullable();
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
            $table->tinyInteger('cli')->unsigned()->default(0);
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

        $this->opt_logout();

        //$tables = Capsule::raw("show tables")->get();

       /* print_r($tables);

        if(!empty($tables)) {
            foreach($tables as $t) {
                
            }
        }*/

        //
    }

}