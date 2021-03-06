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
 * Cron command
 *
 * Add task command to RBot cron
 */
class CronCommand extends Command 
{

    protected $_command_desc = 
        'RBot Cron jobs system'."\n".
        '-------------------------------------------------------'."\n".
        'Syntax: #cron [options] (/ [command] ([command args]))';

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

        $this->_options->add('l|list?', 'list current tasks cron');

        $this->_options->add('c|clear?', 'clear a specific cron item id or all cron items.')
                       ->defaultValue('all');

        $this->_options->add('run', 'run cron tasks');              
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
            if(RBot::dbCheck('cron')) {
                RBot::db()->table('cron')->insert($this->_entity);
                Console::AddAndOutput("Task added !");
            }
            else {
                Console::AddAndOutput("No cron table found :(", 'warning');
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
            Console::AddAndOutput("No task specified | #cron [opt(s)] / [task]", 'warning');
        }
    }

    /**
     * Command option "repeat"
     */
    public function optionRepeat()
    {
        $this->_entity['repeat'] = 1;
        if(empty($this->_entity['task'])) {
            Console::AddAndOutput("No task specified | #cron [opt(s)] / [task]", 'warning');
        }
    }

    /**
     * Clear cron item(s)
     */
    public function optionClear($value)
    {
        if(RBot::dbCheck('cron')) {
            if($value === 'all') {
                RBot::db()->table('cron')->delete();
                Console::add('All cron jobs cleared');
            }
            elseif(is_numeric($value) && $value > 0) {
                RBot::db()->table('cron')->where('id', '=', $value)->delete();
                Console::add('Job item({{id}}) cleared', [], ['id' => $value]);
            }
        }
        else Console::add('Install rbot first');

        Console::output();
    }

    /**
     * Clear cron item(s)
     */
    public function optionRun()
    {
        if(RBot::dbCheck('cron')) {
           include_once __DIR__.'/../../cron.php';
        }
        else Console::add('Install rbot first');

        Console::output();
    }

    /**
     * List current cron
     */
    public function optionList($value)
    {
        if(RBot::dbCheck('cron')) {

            $direction = 'asc';
            $orders = [
                't'   => 'task',
                'dtc' => 'dt_created',
                'dte' => 'dt_executed',
                'r'   => 'repeat',
                'rt'  => 'repeat_time',
                'e'   => 'execution',
                'f'   => 'faulty',
                'fm'  => 'fault_msg',
            ];

            $custom_order = '';
            if(!empty($value) && array_key_exists(trim($value), $orders)) {
                $order = $orders[trim($value)];
                $custom_order = $order.','.$direction;
            }
            else $order = 'dt_created';

            $cron = RBot::db()->table('cron')->orderBy($order, $direction)->get();

            if(empty($cron)) {
                Console::addAndOutput('Cron is empty...', ['color' => '#CCC']);
                return;
            }

            Console::add('Current cron list'.($custom_order ? ' (order:'.$custom_order.')' : '').':',
                         ['color' => '#CCC']);
            Console::add("Legend: ".'[dtc:creation date][r:repeat flag][rt:repeat time]');
            Console::add("\t".'[e:# of executions][dte:last execution date]');
            Console::add("\t".'[f:faulty flag][fm:fault message]');
            Console::nl();

            $tpl = '-> id:{{id}} dtc:{{dt_created}} r:{{repeat}} rt:{{repeat_time}}s e:{{execution}} dte:{{dt_executed}}';

            foreach($cron as $q) {
                $extra = '';
                if($q->faulty == 1) {
                    $extra = '-> f:{{faulty}} fm:{{fault_msg}}';
                }
                /*$option = ['color' => '#CCC'];
                if($q->faulty == 1) $option['color'] = "#ff0000";*/
                if(empty($q->dt_executed) || $q->dt_executed === '0000-00-00 00:00:00') {
                    $q->dt_executed = 'never';
                }
                Console::add($q->task, ['color' => '#ccc']);
                Console::add($tpl, [], $q);
                if(!empty($extra)) Console::add($extra, ['color' => ''], $q);
            }
        }
        else Console::add('Install rbot first');

        Console::output();
    }
}