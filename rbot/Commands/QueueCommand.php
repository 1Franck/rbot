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

/*
 * Queue command
 *
 * Add task command to RBot queue
 */
class QueueCommand extends Command 
{

    private $_entity = [
        'task'        => '',
        'dt_created'  => '',
        'repeat'      => 0,
        'repeat_time' => 0,
    ];

    /**
     * Command Options
     */
    public function setOptions() 
    {
        $this->_options->add('t|time:', 'option requires a value.' )
                       ->isa('Number');

        $this->_options->add('r|repeat', 'repeat');

        $this->_options->add('l|list', 'list current tasks queue');
    }

    /**
     * Process the command
     */
    public function preProcess()
    {
        $argv = RBot::argvString();

        $parts = explode('/', $argv);

        if(count($parts) == 2) {

            $this->_entity['task'] = $parts[1];
            $this->_entity['dt_created'] = date('Y-m-d H:i:s');

            RBot::argv($parts[0]);
        }
    }


    /**
     * Process the command
     */
    public function process()
    {
        if(!$this->hasResult()) $this->help();

        if(!empty($this->_entity['task'])) {
            RBot::db()->table('queue')->insert($this->_entity);
            Console::AddAndDie("Task added !");
        }
    }

    /**
     * Command option "time"
     * 
     * @param  mixed $value
     */
    public function opt_time($value)
    {
        $this->_entity['repeat_time'] = $value;
    }

    /**
     * Command option "repeat"
     */
    public function opt_repeat()
    {
        $this->_entity['repeat'] = 1;
    }
}