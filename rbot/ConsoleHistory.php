<?php
/*
 * This file is part of the RBot.
 *
 * (c) Francois Lajoie <o_o@francoislajoie.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RBot;

use RBot\RBot;
use RBot\Exception;
use RBot\Console;

class ConsoleHistory
{
    /**
     * Get latest lines from db
     * 
     * @param  integer $id
     */
    static function getLatestLinesFrom($id)
    {
        if(!RBot::dbCheck('console')) return;

        $lines = RBot::db()->table('console')->where('id','>', $id)->get();

        if(!empty($lines)) {

            $_SESSION['last_console_id'] = $lines[(count($lines)-1)]->id;

            Console::$log = false;

            foreach($lines as $l) {
                $l->options = unserialize($l->options);
                Console::add($l->line, $l->options);
                $last_id = $l->id;
            }

            Console::output();
        }
    }

    /**
     * Get command history
     */
    static function getCommands($limit = 0)
    {
        if(!RBot::dbCheck('console')) return;

        $cmds = RBot::db()
                    ->table('console')
                    ->select('command')
                    ->addSelect('dt_created')
                    ->where('cli', '=', '0')
                    ->groupBy('command')
                    ->orderBy('dt_created', 'asc')
                    ->get();


        if(!empty($cmds)) {
            foreach ($cmds as $i => $c) {
                if(substr(trim($c->command),0,5) === 'rbot ') {
                    $cmds[$i]->command = substr($c->command, 5);
                }
            }
        }

        return json_encode($cmds);

    }
}