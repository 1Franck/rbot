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
use RBot\DotNotation;
use RBot\Exception;

class Config extends DotNotation
{

    /**
     * Set array of data OR a file optionnaly
     *
     * @param array $vars
     */
    public function __construct($vars)
    {
        if(is_array($vars)) $this->values = $vars;
        elseif(is_string($vars)) $this->loadFile($vars);
        //else throw exception 
        
        $this->initConfig();
        $this->processConfig();
    }

    /**
     * Load a php file as an arrays of data
     *
     * @param string $file
     */
    public function loadFile($file)
    {
        if(pathinfo($file, PATHINFO_EXTENSION) === 'php' && file_exists($file)) {
            $vars = include $file;
            $this->values = $vars;
        }
        else throw new Exception\ConfigFileMissing('Invalid configuration file ('.strip_tags(basename($file)).')');
    }

    /**
     * Prepare config based on environment
     */
    protected function initConfig()
    {
        if($this->have('all')) {
            $newconfig = $this->arrayMergeRecursive($this->get('all'), $this->get(RBot::env()));
            $this->values = $newconfig;
            //print_r($newconfig);
        }
    }

    /**
     * Process some config like php key
     */
    protected function processConfig()
    {
        if($this->have('php')) {
            $php_settings = $this->get('php');
            foreach($php_settings as $k => $v) {
                ini_set($k, $v);
            }
        }
    }
    

    /**
     * Merge two arrays recursively overwriting the keys in the first array
     * if such key already exists
     *
     * @param  mixed $a Left array to merge right array into
     * @param  mixed $b Right array to merge over the left array
     * @return mixed
     */
    public function arrayMergeRecursive($a, $b)
    {
        // merge arrays if both variables are arrays
        if (is_array($a) && is_array($b)) {
            // loop through each right array's entry and merge it into $a
            foreach ($b as $key => $value) {
                if (isset($a[$key])) {
                    $a[$key] = $this->arrayMergeRecursive($a[$key], $value);
                } else {
                    if($key === 0) $a= array(0 => $this->arrayMergeRecursive($a, $value));
                    else $a[$key] = $value;
                }
            }
        } 
        else $a = $b; // one of values is not an array

        return $a;
    }
}