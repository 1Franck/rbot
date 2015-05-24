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

        $cmd = join(' ',RBot::argv());

        if(!empty($data)) {
            foreach($data as $d) {
                self::$_lines[] = [
                    'line'       => $d,
                    'dt_created' => date('Y-m-d H:i:s'),
                    'command'    => $cmd
                ];
            }
        }
    }

    /**
     * Newline (return)
     * 
     * @param  integer $many
     */
    static function nl($many = 1)
    {
        $nl = [''];
        self::add(array_pad($nl, $many, ''));
    }

    /**
     * Desactivated db log
     * @return;
     */
    static function noLog()
    {
        Console::$log = false;
    }

    /**
     * Output console lines
     * @param  boolean $die use die() instead of echo
     */
    static function output($die = false)
    {
        if($die) {
            self::_logAll();
            die(self::_renderAll());
        }
        echo self::_renderAll();
        self::_logAll();

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
                $lines[] = $l['line'];
            }
        }
        return join("\n", $lines);
    }

    /**
     * Log console lines into database
     */
    static protected function _logAll()
    {
        if(self::$log === true) {
            RBot::db()->table('console')->insert(self::$_lines);
        }
    }
}