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
use RBot\Exception;

class ConsolePreset
{
    protected $_presets = [
        'error' => [
            'color' => '#FF9999'
        ],
        'warning' => [
            'color' => '#FFFF99'
        ],
        'important' => [
            'color'       => '#F9F9F9',
            'text-shadow' => '1px 2px 3px #000'
        ],
        'success' => [
            'color' => '#9EFF99'
        ],
        'notice' => [
            'color' => '#3498DB'
        ]
    ];

    /**
     * Check app config console.presets and merge
     */
    public function __construct()
    {
        $conf = RBot::conf('console.presets');
        if(is_array($conf) && !empty($conf)) {
            $this->_presets = array_merge($this->_presets, $conf);
        }
    }
    
    /**
     * Set a new variable
     *
     * @param string $name
     * @param misc   $val
     */
    public function __set($name, $val)
    {
        $this->_presets[$name] = $val;
    }

    /**
     * Get a variable
     *
     * @param  string $name
     * @return misc   Will return null if variable keyname is not found
     */
    public function &__get($name)
    {
        if(array_key_exists($name, $this->_presets)) return $this->_presets[$name];
        else return ${null};
    }

    /**
     * Isset variable
     *
     * @param  string $name
     * @return bool
     */
    public function __isset($name)
    {
        return (array_key_exists($name, $this->_presets)) ? true : false;
    }

    /**
     * Unset variable
     *
     * @param string $name
     */
    public function __unset($name)
    {
        if(array_key_exists($name, $this->_presets)) unset($this->_presets[$name]);
    }

}

