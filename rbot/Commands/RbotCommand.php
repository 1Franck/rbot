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
use RBot\Console;
use RBot\Command;

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
        $this->_specs->add('v|version', 'rbot version')
            ->isa('Number')
            ->incremental();

        $this->_specs->add('list', 'list all commands');

    }

    /**
     * [run description]
     * @return [type] [description]
     */
    public function process()
    {
        if($this->_no_result) {
            echo $this->help();
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

        Console::AddAndDie([
            '██████╗ ██████╗  ██████╗ ████████╗',
            '██╔══██╗██╔══██╗██╔═══██╗╚══██╔══╝',
            '██████╔╝██████╔╝██║   ██║   ██║   ',
            '██╔══██╗██╔══██╗██║   ██║   ██║   ',
            '██║  ██║██████╔╝╚██████╔╝   ██║   ',
            '╚═╝  ╚═╝╚═════╝  ╚═════╝    ╚═╝   ',
            ' ',
            'rbot version '.RBot::VERSION.' / php '.phpversion().' / '.date('D j F Y H:i:s O'),
        ]);
    }

    /**
     * List App Commands
     * 
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function opt_list($value)
    {
        $app_commands = array_diff(scandir(RBot::getCmdPath()), array('..', '.'));

        if(!empty($app_commands)) {
            Console::add(count($app_commands).' command'.((count($app_commands) > 1) ? 's' : '').' found:');

            foreach($app_commands as $c) {
                Console::add(' '.strtolower($c));
            }
            
        }

        Console::output();
    }
}