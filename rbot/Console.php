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
use RBot\ConsoleLine;
use RBot\ConsolePreset;

class Console 
{
    // array of lines objects 
    static protected $_lines = [];

    // console preset object
    static protected $_preset;

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
    static function add($data, $options = [], $rep = null)
    {
        if(!is_array($data)) $data = [$data];

        $cmd = RBot::argvString();

        if(is_string($options)) {
            $options = self::preset($options);
            if(!is_array($options)) $options = [];
        }

        if(!empty($data)) {
            foreach($data as $l) {
                if($l instanceof ConsoleLine) {
                    self::$_lines[] = $l;
                }
                else {
                    //create a line object
                    self::$_lines[] = new ConsoleLine([
                        'line'       => $l,
                        'command'    => $cmd,
                        'options'    => $options,
                        'rep'        => $rep,
                    ]);
                }
            }
        }
    }

    /**
     * Add an output
     *
     * @see  add() for params
     */
    static function addAndOutput($data, $options = []) 
    {
        self::add($data, $options);
        self::output();
    }

    /**
     * Output console lines
     */
    static function output()
    {
        self::_logAll();
        echo self::_renderAll();
        self::$_lines = [];
    }

    /**
     * Add a separator
     * 
     * @param  integer $w     width
     * @param  string  $char  character used, by default - 
     */
    static function separator($w, $char = '-') 
    {
        self::nl();
        self::add(str_pad($char, $w, $char));
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
     * Data Options Preset
     * 
     * @param  string $name
     * @param  array  $value
     * @return mixed
     */
    static function preset($name = null, $value = null)
    {
        if(!is_object(self::$_preset)) {
            self::$_preset = new ConsolePreset;
        }

        if(isset($name)) {

            if(isset($value)) {
                self::$_preset->$name = $value;
                return;
            }
            else {
                return self::$_preset->$name;
            }
        }

        return self::$_preset;
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
     * All others are faker, im the real one here! who work hard to process the stuff baby.
     * 
     * @return string
     */
    static protected function _renderAll()
    {
        $lines = [];
        if(!empty(self::$_lines)) {
            foreach(self::$_lines as $l) { 
                $lines[] = $l->render();
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
            $lines    = self::$_lines; 
            $cli_mode = RBot::cliMode();

            if(!empty($lines)) {

                foreach($lines as $i => $l) {

                    $lines[$i]->cli = $cli_mode;
                    $lines[$i]->render();
                    $lines[$i] = $lines[$i]->toDbArray();

                }
            }

            if(RBot::dbCheck('console')) {
                RBot::db()->table('console')->insert($lines);
            }
        }
    }
}