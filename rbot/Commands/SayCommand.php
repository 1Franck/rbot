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
 * Say command
 */
class SayCommand extends Command 
{
    /**
     * Command Options
     */
    public function setOptions() {}

    /**
     * Process the command
     */
    public function process()
    {
        $say = $this->rawData();

        if(!empty($say)) {
            Console::addAndOutput($say);
        }
    }
}