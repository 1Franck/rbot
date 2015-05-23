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
use RBot\Exception;

use PDOException;

/*
 * Install RBot
 */
class InstallCommand extends Command 
{
    /**
     * Command Options
     */
    public function setOptions() 
    {
    }

    /**
     * Process the command
     */
    public function process()
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
            $table->text('line');
            $table->timestamp('dt_created');
            $table->string('command', 255);
            $table->text('task');
        });
        

        Console::AddAndDie('Installation completed successfully');

    }
}