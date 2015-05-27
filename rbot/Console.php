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

use RBot\ConsoleLine;

class Console 
{
    // console lines array
    static protected $_lines = [];

    // end of line used
    static $EOL = PHP_EOL;

    // log lines to database
    static $log = true;

    // log also empty lines to database
    // 
    // If false, the web cli output won't contains empty lines, 
    // since webcli always use database history. But for regular cli, 
    // output will contains empty since it don't use directly the database.
    static $log_empty_line = true;

    // remove empty lines at the start and the end of $_lines
    static $log_trim_linesblock = true;

    /**
     * Add line(s) to console
     * 
     * @param string|array $data
     */
    static function add($data, $options = [])
    {
        if(!is_array($data)) $data = [$data];

        $argv = RBot::argv();
        if(is_array($argv)) $cmd = join(' ',$argv);
        else $cmd = '';

        if(!empty($data)) {
            foreach($data as $d) {
                $line = [
                    'line'       => $d,
                    'dt_created' => date('Y-m-d H:i:s'),
                    'command'    => $cmd,
                    'options'    => $options
                ];
                self::$_lines[] = $line;
            }
        }
    }

    /**
     * Newline (return) 
     * don't work if $log_empty_line=false;
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
        //Console::$log = false;
    }

    /**
     * Output console lines
     * @param  boolean $die use die() instead of echo
     */
    static function output($die = false)
    {
        self::_logAll();
        echo self::_renderAll();
        self::$_lines = [];
        if($die) die();
    }

    /**
     * add() + outputDie()
     * @param string|array $data
     */
    static function addAndDie($data, $options = [])
    {
        self::add($data, $options);
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
                $lines[] = ConsoleLine::render($l);
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

            //create a copy to keep empty space for _renderAll 
            $lines = self::$_lines; 

            if(!empty($lines)) {

                foreach($lines as $i => $l) {
                    $lines[$i]['options'] = serialize($lines[$i]['options']);
                }

                if(self::$log_empty_line === false) {
                    foreach($lines as $i => $l) {
                        if(empty($l['line'])) unset($lines[$i]);
                    }
                }

                if(self::$log_trim_linesblock === true) {
                    $lines = self::_trim($lines);
                }
            }

            if(RBot::dbCheck('console')) {
                RBot::db()->table('console')->insert($lines);
            }
        }
    }

    static protected function _trim($lines)
    {
        if(!empty($lines)) {
            foreach($lines as $i => $l) {
                if(empty($l['line'])) unset($lines[$i]);
                else break; 
            }
            $c = count($lines) - 1;
            
            for($i=$c;$i>0;--$i) {
                if(empty($lines[$i]['line'])) unset($lines[$i]);
                else break; 
            }
        }
        return $lines;
    }
}