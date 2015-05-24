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

use GetOptionKit\Option;
use GetOptionKit\OptionCollection;
use GetOptionKit\OptionParser;
use GetOptionKit\OptionPrinter\ConsoleOptionPrinter;

use GetOptionKit\Exception\InvalidOptionException;
use GetOptionKit\Exception\RequireValueException;
use GetOptionKit\Exception\NonNumericException;

use Exception;

/**
 * Base class for App Commands
 */
abstract class Command
{

    /**
     * Commands spec
     * @var array
     */
    protected $_options;

    /**
     * Command spec result
     * @var [type]
     */
    protected $_result;

    /**
     * No result
     * @var boolean
     */
    protected $_no_result = true;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->_options = new OptionCollection;
        $this->setOptions();
    }

    /**
     * Child class methods
     */
    public function setOptions(){}
    public function preProcess() {}
    public function process(){}

    /**
     * Run the command
     *
     * 1. Call pre specs process
     * 2. Parse argv
     * 2. Call specs option methods if exists (syntax: opt_[spec-key-name])
     * 3. Call main process method
     */
    public function run()
    {
        $this->preProcess();
        $this->_parseArgv();

        $this->_no_result = true;
        foreach($this->_options as $k => $s) {
            if($this->_result->has($k)) {
                $this->_no_result = false;
                if(method_exists($this, 'opt_'.$k)) {
                    $m = 'opt_'.$k;
                    $this->$m($s->getValue());
                }
            }
        }
        
        $this->process();
    }

    /**
     * Show help
     */
    public function help()
    {
        $printer = new ConsoleOptionPrinter;
        //return $printer->render($this->_options);

        $max_length = 0;
        foreach($this->_options as $i => $o) {
            $opt_line = $this->_renderOption($o);
            $length = strlen($opt_line);
            if($length > $max_length) {
                $max_length = $length;
            }
        }

        foreach($this->_options as $o) {
            $opt_line = $this->_renderOption($o);
            $length = strlen($opt_line);

            for($i=1;$i <= (($max_length-$length)+8);++$i) {
                $opt_line .= ' ';
            }

            Console::add($opt_line.' '.$o->desc);
        }

    /*     Console::add();
            Console::add($o->desc);
            //Console::add($option->desc);*/

        Console::add('');
        Console::outputDie();
    }

    /**
     * Format option to a string
     * 
     * @param  object $opt GetOptionKit\Option
     * @return string
     */
    public function _renderOption($opt)
    {
        $c1 = '';
        if ( $opt->short && $opt->long ) {
            $c1 = sprintf('-%s, --%s',$opt->short,$opt->long);
        } elseif( $opt->short ) {
            $c1 = sprintf('-%s',$opt->short);
        } elseif( $opt->long ) {
            $c1 = sprintf('--%s',$opt->long );
        }
        if(!defined('RBOT_CLI')) {
            $c1 .= str_replace(['<','>'], ['(',')'], strtolower($opt->renderValueHint()));
        }
        else $c1 .= $opt->renderValueHint();
        return trim($c1);
    }

    /**
     * __toString()
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->help();
    }

    /**
     * Parse options
     */
    private function _parseArgv()
    {
        $parser = new OptionParser($this->_options);

        try {
            $this->_result = $parser->parse(Rbot::argv());
            if(count(Rbot::argv()) < 2) {
                Console::addAndDie($this->help());
            }
        } 
        catch(InvalidOptionException $e ) {
            Console::addAndDie($e->getMessage());
        }
        catch(RequireValueException $e ) {
            Console::addAndDie($e->getMessage());
        }
        catch(NonNumericException $e ) {
            Console::addAndDie($e->getMessage());
        }
        catch(Exception $e ) {
            Console::addAndDie($e->getMessage());
        }
    }

    public function debug()
    {

        foreach ($this->_result as $key => $spec) {
            echo $spec . "\n";
        }
        

    }
}