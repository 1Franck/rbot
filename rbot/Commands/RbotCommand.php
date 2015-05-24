<?php
/*
 * This file is part of the RBot app.
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
        $this->_options->add('install', 'install rbot');

    }

    /**
     * [run description]
     * @return [type] [description]
     */
    public function process()
    {
        if($this->_no_result === true) {
            $this->help();
        }
        else {

        }

        //echo $this->help();
        //$this->debug();
    }

    /**
     * Rbot version
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function opt_version($value)
    {
        Console::nl();
        if(!defined('RBOT_CLI')) {
            Console::Add([
                '██████╗ ██████╗  ██████╗ ████████╗',
                '██╔══██╗██╔══██╗██╔═══██╗╚══██╔══╝',
                '██████╔╝██████╔╝██║   ██║   ██║   ',
                '██╔══██╗██╔══██╗██║   ██║   ██║   ',
                '██║  ██║██████╔╝╚██████╔╝   ██║   ',
                '╚═╝  ╚═╝╚═════╝  ╚═════╝    ╚═╝   ',
                ' ',
            ]);
        }

        Console::AddAndDie([
            'rbot version '.RBot::VERSION.' / php '.phpversion().' / '.date('D j F Y H:i:s O'),
            '',
        ]);
    }

    /**
     * List App Commands
     * 
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function opt_list()
    {
        Console::nl();
        $app_commands = array_diff(scandir(RBot::getCmdPath()), array('..', '.'));

        if(!empty($app_commands)) {
            Console::add(count($app_commands).' command'.((count($app_commands) > 1) ? 's' : '').' found:');

            foreach($app_commands as $c) {
                Console::add(' '.strtolower($c));
            }
        }

        Console::nl();
        Console::output();
    }

    /**
     * Install rbot
     * 
     * @return
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
            Console::AddAndDie('System already installed or database is not empty');
            return;
        }

        // create tables
        RBot::db()->schema()->create('queue', function($table) {

            $table->engine = 'InnoDB';
            $table->bigIncrements('id')->unsigned();
            $table->string('command', 255);
            $table->text('task');
            $table->timestamp('dt_created');
            $table->timestamp('dt_executed');
            $table->tinyInteger('repeat')->unsigned();
            $table->integer('repeat_time')->unsigned();
        });

        RBot::db()->schema()->create('console', function($table) {

            $table->engine = 'InnoDB';
            $table->bigIncrements('id')->unsigned();
            $table->text('line')->nullable();
            $table->timestamp('dt_created');
            $table->text('command');
        });
        

        Console::AddAndDie('Installation completed successfully');
    }
}