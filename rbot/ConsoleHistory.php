<?php
/*
 * This file is part of the RBot app.
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
    static function getLatestLinesFrom($id)
    {
        if(!RBot::dbCheck('console')) return;

        $lines = RBot::db()->table('console')->where('id','>', $id)->get();

        if(!empty($lines)) {

            $_SESSION['last_console_id'] = $lines[(count($lines)-1)]->id;

            Console::$log = false;

            foreach($lines as $l) {
                Console::add($l->line);
                $last_id = $l->id;
            }

            Console::output();
        }
    }
}