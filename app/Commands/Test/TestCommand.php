<?php
/*
 * This file is part of the RBot app.
 *
 * (c) Francois Lajoie <o_o@francoislajoie.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Commands\Test;

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
        $this->_options->add('h|help', 'test app command' );
        $this->_options->add('f|foo:', 'option requires a value.' )
            ->isa('String');

        $this->_options->add('b|bar+', 'option with multiple value.' )
            ->isa('Number');

        $this->_options->add('z|zoo?', 'option with optional value.' )
            ->isa('Boolean');

        $this->_options->add('o|output?', 'option with optional value.' )
            ->isa('File');
            //->defaultValue('output.txt');

        // works for -vvv  => verbose = 3
        $this->_options->add('v|verbose:', 'verbose')
            ->isa('Number')
            ->incremental();

        /*$this->_options->add('file:', 'option value should be a file.' )
            ->trigger(function($value) {
                echo "Set value to :";
                var_dump($value);
            })
            ->isa('File');*/

        $this->_options->add('d|date', 'show date time' );
        $this->_options->add('d|dong', 'long option name only.' );
        $this->_options->add('long', 'long option name only.' );
        $this->_options->add('s:', 'short option name only.' )
                        ->isa('Number')
                        ->trigger(function($value) {
                            return $value;
                        });
        $this->_options->add('m', 'short option m');
        $this->_options->add('4', 'short option with digit');
        $this->_options->add('u|url?', 'url option')->isa('Number');
        $this->_options->add('ip?', 'ip option')->isa('ip');
        $this->_options->add('ipv4?', 'ipv4 option')->isa('ipv4');
        $this->_options->add('ipv6?', 'ipv6 option')->isa('ipv6');
        $this->_options->add('e|email?', 'email option')->isa('email');

        $this->_options->add('r|regex:', 'regex option')->isa('regex');
    }

    /**
     * Process the command
     */
    public function process()
    {
        if(!$this->hasResult() && !$this->hasErrors()) $this->help();
        //$this->debug();

        /*Console::add(RBot::conf('db').'43434');
        Console::output();*/
    }

    /**
     * Command option "foo"
     * 
     * @param  mixed $value
     */
    public function optionFoo($value)
    {
        Console::nl();
        Console::add("Foo >>> ".$value);
        Console::output();
    }

    /**
     * Command option "ip"
     * 
     * @param  mixed $value
     */
    public function optionIp($ip)
    {
        Console::preset('warning', ['color' => '#444']);
        Console::nl();
        Console::add("ip >>> ".$ip, 'success');
        Console::add("ip >>> ".$ip, 'error');
        Console::add("ip >>> ".$ip, 'important');
        Console::add("ip >>> ".$ip, 'warning');
        Console::add("ip >>> ".$ip, 'notice');
        Console::add("ip >>> ", 'custom1');
        Console::output();
    }

    /**
     * Command option "ipv4"
     * 
     * @param  mixed $value
     */
    public function optionIpv4($ip)
    {
        $this->opt_ip($ip);
    }

    /**
     * Command option "ipv4"
     * 
     * @param  mixed $value
     */
    public function optionRegex($value)
    {
        Console::add($value, ['color'=>'#fff']);
        Console::output();
    }
    
    /**
     * Command option "ipv6"
     * 
     * @param  mixed $value
     */
    public function optionIpv6($ip)
    {
        $this->opt_ip($ip);
    }

    /**
     * Command option "s"
     * 
     * @param  mixed $value
     */
    public function optionS($value)
    {
        Console::add($value, ['color'=>'#fff']);
        Console::output();
    }

    /**
     * Command option "date"
     * 
     * @param  mixed $value
     */
    public function optionDate($value)
    {
        Console::add(date('Y-m-d H:i:s'), ['color'=>'#fff']);
        Console::output();
    }

    /**
     * Command option "date"
     * 
     * @param  mixed $value
     */
    public function optionDong($value)
    {
        Console::add("2   ".date('Y-m-d H:i:s'), ['color'=>'#fff']);
        Console::output();
    }
}