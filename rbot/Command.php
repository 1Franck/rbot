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

use GetOptionKit\OptionCollection;
use GetOptionKit\OptionParser;
use GetOptionKit\OptionPrinter\ConsoleOptionPrinter;

use Exception;
use GetOptionKit\Exception\InvalidOptionException;
use GetOptionKit\Exception\RequireValueException;
use GetOptionKit\Exception\NonNumericException;

/**
 * Base class for App Commands
 */
abstract class Command
{
    /**
     * Command options
     * @var [type]
     */
    public $options = [];

    /**
     * Commands spec
     * @var [type]
     */
    protected $_specs;

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
        $this->_specs   = new OptionCollection;
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

        foreach($this->_specs as $k => $s) {
            $this->_no_result = true;
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

    public function help()
    {
        $printer = new ConsoleOptionPrinter;
        return $printer->render($this->_specs);
    }

    public function __toString()
    {
        return $this->help();
    }

    private function _parseArgv()
    {
        $parser = new OptionParser($this->_specs);

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