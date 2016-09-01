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
use RBot\Application;

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
        $this->_options->add('l|list', 'list all application commands');
        $this->_options->add('i|install', 'install rbot database');
        $this->_options->add('killdb', 'drop all tables in rbot database');
        $this->_options->add('logout', 'logout user if apply');
        $this->_options->add('clear', 'clear console history table');
    }

    /**
     * process opt, nothing special here
     */
    public function process()
    {
        if(!$this->hasResult() && !$this->hasErrors()) {
            $this->help();
        }
    }

    /**
     * Rbot version
     */
    public function optionVersion()
    {
        if(!RBot::cliMode()) {
            Console::Add([
                '██████╗ ██████╗  ██████╗ ████████╗',
                '██╔══██╗██╔══██╗██╔═══██╗╚══██╔══╝',
                '██████╔╝██████╔╝██║   ██║   ██║   ',
                '██╔══██╗██╔══██╗██║   ██║   ██║   ',
                '██║  ██║██████╔╝╚██████╔╝   ██║   ',
                '╚═╝  ╚═╝╚═════╝  ╚═════╝    ╚═╝   ',
            ], ['text-shadow' => '1px 2px 3px #000']);
        }

        Console::addAndOutput([
            'rbot version '.RBot::VERSION.' / php '.phpversion().' / '.date('D j F Y H:i:s O'),
            '',
        ], 'notice');
    }

    /**
     * Logout
     */
    public function optionLogout()
    {
        Console::addAndOutput('Good bye...');
        @session_destroy();
    }

    /**
     * List App Commands
     */
    public function optionList()
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
                        $prefix = Application::$RBOT_CMD_PREFIX;
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
                $output[$i] = str_pad($col1[$i], 15);
            }
            else $output[$i] = str_pad('', 15);
        
            if(isset($col2[$i])) {
                $output[$i] .= $col2[$i];
            }
        }

        $header = count($col1).' app commands '."\n".count($col2).' rbot commands found:';
        Console::add($header);
        Console::nl();
        Console::add($output);
        Console::output();
    }

    /**
     * Install rbot tables
     */
    public function optionInstall()
    {
        // database exists ?
        if(!RBot::dbCheck()) {
            Console::addAndOutput('Can\'t find database '.RBot::conf('db.database'));
            return;
        }

        // table exists ?
        if(RBot::db()->schema()->hasTable('cron') || RBot::db()->schema()->hasTable('users')) {
            Console::addAndOutput('System already installed or database is not empty');
            return;
        }

        // create tables
        RBot::db()->schema()->create('cron', function($table) {

            $table->engine = 'InnoDB';
            $table->bigIncrements('id')->unsigned();
            //$table->string('command', 255);
            $table->text('task');
            $table->timestamp('dt_created')->nullable();
            $table->timestamp('dt_executed')->nullable();
            $table->tinyInteger('repeat')->unsigned()->default(0);
            $table->integer('repeat_time')->unsigned()->default(0);
            $table->integer('execution')->unsigned()->default(0);
            $table->tinyInteger('faulty')->unsigned()->default(0);
            $table->string('fault_msg', 255);
        });

        RBot::db()->schema()->create('console', function($table) {

            $table->engine = 'InnoDB';
            $table->bigIncrements('id')->unsigned();
            $table->text('line')->nullable();
            $table->timestamp('dt_created');
            $table->text('command')->nullable();
            $table->text('options');
            $table->tinyInteger('cli')->unsigned()->default(0);
        });
        
        Console::AddAndOutput('Installation completed successfully!');
    }


    /**
     * Drop all tables (even those who do not belong to the system)
     * inside the installed database.
     */
    public function optionKilldb()
    {
        if(!RBot::dbCheck()) {
            Console::AddAndOutput('There is nothing to kill...');
            return;
        }

        $pdo = RBot::db()->connection()->getPdo();
        $count = 0;
        foreach($pdo->query('SHOW TABLES') as $row)  {
            RBot::db()->schema()->dropIfExists($row[0]);
            ++$count;
        }

        if($count > 0){
            Console::AddAndOutput('Killed '.$count.' table'.(($count > 0) ? 's' : '').'! ');
        } else {
            Console::AddAndOutput('There is nothing to kill... ');
        } 

        $this->optionLogout();
    }

    /**
     * Clear console history table 
     */
    public function optionClear()
    {
        if(!RBot::dbCheck('console')) {
            Console::AddAndOutput('Install rbot first');
            return;
        }

        RBot::db()->table('console')->delete();
        Console::AddAndOutput('Console history cleared!');
    }

}