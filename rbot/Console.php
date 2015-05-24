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

class Console 
{
    // console lines array
    static protected $_lines = [];

    // end of line used
    static $EOL = PHP_EOL;

    // log lines to database
    static $log = true;

    /**
     * Add line(s) to console
     * 
     * @param string|array $data
     */
    static function add($data)
    {
        if(!is_array($data)) $data = [$data];

        foreach($data as $d) {
            self::$_lines[] = [
                'line' => $d,
                'ts'   => date('Y-m-d H:i:s'),
                'cmd'  => join(' ',RBot::argv())
            ];
        }
    }

    /**
     * Output console lines
     * @param  boolean $die use die() instead of echo
     */
    static function output($die = false)
    {
        if($die) die(self::_renderAll());
        echo self::_renderAll();
    }

    /**
     * add() + outputDie()
     * @param string|array $data
     */
    static function addAndDie($data)
    {
        self::add($data);
        self::outputDie();
    }

    /**
     * die the output!
     */
    static function outputDie()
    {
        self::output(true);
    }

    /**
     * All others are faker, im the real one here! who work hard to process the stuff baby.
     * 
     * @return string
     */
    static protected function _renderAll()
    {
        $lines = [];
        if(!empty(self::$_lines)) {
            foreach(self::$_lines as $l) {
                if(self::$log === true) {
                    RBot::db()->table('console')->insert([
                        'dt_created' => $l['ts'], 
                        'line' => $l['line'], 
                        'command' => $l['cmd']
                    ]);
                }
                $lines[] = $l['line'];
            }
            
        }
        return join("\n", $lines);
    }
}