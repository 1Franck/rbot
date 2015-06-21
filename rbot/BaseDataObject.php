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

abstract class BaseDataObject
{
    /**
     * Data array
     * @var array
     */
    protected $_data;

    /**
     * Set a new variable
     *
     * @param string $name
     * @param misc   $val
     */
    public function __set($name, $val)
    {
        $this->_data[$name] = $val;
    }

    /**
     * Get a variable
     *
     * @param  string $name
     * @return misc   Will return null if variable keyname is not found
     */
    public function &__get($name)
    {
        if(array_key_exists($name, $this->_data)) return $this->_data[$name];
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
        return (array_key_exists($name, $this->_data)) ? true : false;
    }

    /**
     * Unset variable
     *
     * @param string $name
     */
    public function __unset($name)
    {
        if(array_key_exists($name, $this->_data)) unset($this->_data[$name]);
    }
}