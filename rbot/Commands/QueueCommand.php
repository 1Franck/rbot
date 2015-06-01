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

        $this->_options->add('c|clear?', 'clear a specific queue item id or all queue items.')
                       ->defaultValue('all');
    }

    /**
     * Process the command
     */
    public function preProcess()
    {
        $argv = RBot::argvString();

        $parts = explode('/', $argv);

        if(count($parts) == 2) {

            $this->_entity['task'] = trim($parts[1]);
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
            if(RBot::dbCheck('queue')) {
                RBot::db()->table('queue')->insert($this->_entity);
                Console::AddAndOutput("Task added !");
            }
            else {
                Console::AddAndOutput("No queue table found :(");
            }
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

    /**
     * Clear queue item(s)
     */
    public function opt_clear($value)
    {
        if(RBot::dbCheck('queue')) {
            if($value === 'all') {
                RBot::db()->table('queue')->delete();
                Console::add('Queue items cleared');
            }
            elseif(is_numeric($value) && $value > 0) {
                RBot::db()->table('queue')->where('id', '=', $value)->delete();
                Console::add('Queue item cleared');
            }
        }
        else Console::add('Install rbot first');

        Console::output();
    }

    /**
     * List current queue
     */
    public function opt_list()
    {
        if(RBot::dbCheck('queue')) {

            $queue = RBot::db()->table('queue')->get();

            if(empty($queue)) {
                Console::addAndOutput('Queue is empty...', ['color' => '#CCC']);
                return;
            }

            Console::add('Current queue list:', ['color' => '#CCC']);

            $tpl = '-> id:{{id}} dtc:{{dt_created}} r:{{repeat}} rt:{{repeat_time}}s e:{{execution}} dte:{{dt_executed}}';

            foreach($queue as $q) {
                if(empty($q->dt_executed) || $q->dt_executed === '0000-00-00 00:00:00') {
                    $q->dt_executed = 'never';
                }
                Console::add($q->task, ['font-style' => 'italic']);
                Console::add($tpl, [], $q);
            }
        }
        else Console::add('Install rbot first');

        Console::output();
    }
}