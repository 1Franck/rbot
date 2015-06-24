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

use GetOptionKit\Option;
use GetOptionKit\OptionCollection;
use GetOptionKit\OptionParser;
use GetOptionKit\OptionResult;

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
     * @var array
     */
    protected $_result;

    /**
     * No result
     * @var boolean
     */
    private $_has_result = false;

    /**
     * Has error(s)
     */
    private $_has_errors = false;

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
     * Check if command has options parsing result
     * 
     * @return boolean
     */
    public function hasResult()
    {
        return $this->_has_result;
    }

    /**
     * Check if command has error(s)
     * 
     * @return boolean
     */
    public function hasErrors()
    {
        return $this->_has_errors;
    }

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

        $this->_has_result = false;

        foreach($this->_options as $k => $s) {
            if(is_object($this->_result) && $this->_result->has($k)) {
                
                if(($s->defaultValue === $s->getValue()) && !RBot::hasArgv($s)) {
                    continue;
                }
                $method = 'option'.ucfirst($k);
                if(method_exists($this, $method)) {
                    $this->$method($s->getValue(), $s);
                    $this->_has_result = true;
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
        Console::add("Help for command " .$this->_getClassCmdName());
        Console::nl();

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

            for($i=1;$i <= (($max_length-$length)+4);++$i) {
                $opt_line .= ' ';
            }

            Console::add($opt_line.' '.$o->desc);
        }

        Console::nl();
        Console::output();
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
            if(count(Rbot::argv()) < 1) {
                Console::addAndDie($this->help());
            }
        } 
        catch(InvalidOptionException $e ) {
            $exception = $e->getMessage();
        }
        catch(RequireValueException $e ) {
            $exception = $e->getMessage();
        }
        catch(NonNumericException $e ) {
            $exception = $e->getMessage();
        }
        catch(Exception $e ) {
            $exception = $e->getMessage();
        }

        if(isset($exception)) {
            $this->_has_errors = true;
            Console::addAndOutput($exception);
        }
    }

    /**
     * Get command raw data arguments as string
     * 
     * @return string
     */
    public function rawData() {
        $argv = Rbot::argv();
        if(is_array($argv) && count($argv) > 1) {
            $argv = array_slice($argv, 2);
            $argv = implode(' ',$argv);
        }
        return $argv;
    }

    /**
     * Get current class command name
     * @return string
     */
    protected function _getClassCmdName()
    {
        $part = explode('\\', get_class($this));
        return str_ireplace('Command', '', strtolower($part[count($part)-1]));
    }
}