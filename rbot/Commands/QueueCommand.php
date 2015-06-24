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
        $this->_options->add('r|repeat', 'repeat the given task');

        $this->_options->add('t|time:', 'specify the repetion time in sec' )
                       ->isa('Number');

        $this->_options->add('l|list', 'list current tasks queue');

        $this->_options->add('c|clear?', 'clear a specific queue item id or all queue items.')
                       ->defaultValue('all');

        $this->_options->add('run', 'run queue tasks');              
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
        if(!empty($this->_entity['task']) && !$this->hasErrors()) {
            if(RBot::dbCheck('queue')) {
                RBot::db()->table('queue')->insert($this->_entity);
                Console::AddAndOutput("Task added !");
            }
            else {
                Console::AddAndOutput("No queue table found :(", 'warning');
            }
        }
        elseif(!$this->hasResult() && !$this->hasErrors()) $this->help();
    }

    /**
     * Command option "time"
     * 
     * @param  mixed $value
     */
    public function optionTime($value)
    {
        $this->_entity['repeat_time'] = $value;
        if(empty($this->_entity['task'])) {
            Console::AddAndOutput("No task specified | #queue [opt(s)] / [task]", 'warning');
        }
    }

    /**
     * Command option "repeat"
     */
    public function optionRepeat()
    {
        $this->_entity['repeat'] = 1;
        if(empty($this->_entity['task'])) {
            Console::AddAndOutput("No task specified | #queue [opt(s)] / [task]", 'warning');
        }
    }

    /**
     * Clear queue item(s)
     */
    public function optionClear($value)
    {
        if(RBot::dbCheck('queue')) {
            if($value === 'all') {
                RBot::db()->table('queue')->delete();
                Console::add('All queue items cleared');
            }
            elseif(is_numeric($value) && $value > 0) {
                RBot::db()->table('queue')->where('id', '=', $value)->delete();
                Console::add('Queue item({{id}}) cleared', [], ['id' => $value]);
            }
        }
        else Console::add('Install rbot first');

        Console::output();
    }

    /**
     * Clear queue item(s)
     */
    public function optionRun()
    {
        if(RBot::dbCheck('queue')) {
           include_once __DIR__.'/../../cron.php';
        }
        else Console::add('Install rbot first');

        Console::output();
    }

    /**
     * List current queue
     */
    public function optionList()
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
                $extra = '';
                if($q->faulty == 1) {
                    $extra = '-> f:{{faulty}} fm:{{fault_msg}}';
                }
                /*$option = ['color' => '#CCC'];
                if($q->faulty == 1) $option['color'] = "#ff0000";*/
                if(empty($q->dt_executed) || $q->dt_executed === '0000-00-00 00:00:00') {
                    $q->dt_executed = 'never';
                }
                Console::add($q->task, ['font-style' => 'italic']);
                Console::add($tpl, [], $q);
                if(!empty($extra)) Console::add($extra, ['color' => ''], $q);
            }
        }
        else Console::add('Install rbot first');

        Console::output();
    }



}