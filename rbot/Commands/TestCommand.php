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
use RBot\Command;

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
        $this->_specs->add('h|help', 'test' );
        $this->_specs->add('f|foo:', 'option requires a value.' )
            ->isa('String');

        $this->_specs->add('b|bar+', 'option with multiple value.' )
            ->isa('Number');

        $this->_specs->add('z|zoo?', 'option with optional value.' )
            ->isa('Boolean')
            ;

        $this->_specs->add('o|output?', 'option with optional value.' )
            ->isa('File');
            //->defaultValue('output.txt');

        // works for -vvv  => verbose = 3
        $this->_specs->add('v|verbose', 'verbose')
            ->isa('Number')
            ->incremental();

        $this->_specs->add('file:', 'option value should be a file.' )
            ->trigger(function($value) {
                echo "Set value to :";
                var_dump($value);
            })
            ->isa('File');

        $this->_specs->add('d|debug', 'debug message.' );
        $this->_specs->add('long', 'long option name only.' );
        $this->_specs->add('s', 'short option name only.' );
        $this->_specs->add('m', 'short option m');
        $this->_specs->add('4', 'short option with digit');
    }

    /**
     * [run description]
     * @return [type] [description]
     */
    public function process()
    {
        echo $this->help();
        $this->debug();
    }

    public function opt_foo($value)
    {
        echo "Foo >>> ".$value;
    }
}