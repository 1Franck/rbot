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
 * Generic test function for rbot 
 *
 * Do nothing
 */
class TestCommand extends Command 
{
    /**
     * Command Options
     */
    public function setOptions() 
    {
        $this->_options->add('h|help', 'test' );
        $this->_options->add('f|foo:', 'option requires a value.' )
            ->isa('String');

        $this->_options->add('b|bar+', 'option with multiple value.' )
            ->isa('Number');

        $this->_options->add('z|zoo?', 'option with optional value.' )
            ->isa('Boolean')
            ;

        $this->_options->add('o|output?', 'option with optional value.' )
            ->isa('File');
            //->defaultValue('output.txt');

        // works for -vvv  => verbose = 3
        $this->_options->add('v|verbose', 'verbose')
            ->isa('Number')
            ->incremental();

        $this->_options->add('file:', 'option value should be a file.' )
            ->trigger(function($value) {
                echo "Set value to :";
                var_dump($value);
            })
            ->isa('File');

        $this->_options->add('d|date', 'show date time' );
        $this->_options->add('long', 'long option name only.' );
        $this->_options->add('s', 'short option name only.' );
        $this->_options->add('m', 'short option m');
        $this->_options->add('4', 'short option with digit');
    }

    /**
     * Process the command
     */
    public function process()
    {
        if(!$this->hasResult()) $this->help();
        //$this->debug();

        /*Console::add(RBot::conf('db').'43434');
        Console::output();*/
    }

    /**
     * Command option "foo"
     * 
     * @param  mixed $value
     */
    public function opt_foo($value)
    {
        Console::nl();
        Console::add("Foo >>> ".$value);
        Console::output();
    }

    /**
     * Command option "date"
     * 
     * @param  mixed $value
     */
    public function opt_date($value)
    {
        Console::add(date('Y-m-d H:i:s'), ['color'=>'#fff']);
        Console::output();
    }
}