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
use RBot\BaseDataObject;
use RBot\Exception;

/**
 * For webcli
 */
class ConsoleLine extends BaseDataObject
{
    /**
     * Default data
     * @var
     */
    protected $_data = [
        'line'       => '',   // line string
        'dt_created' => '',   // line datetime creation
        'command'    => '',   // string command that output this
        'options'    => '',   // option preset name of style array
        'rep'        => [],   // replacement array
        'cli'        => false // line is for cli mode(we skip render)
    ];

    /**
     * Construct
     * 
     * @param array $data
     */
    public function __construct($data = null) 
    {
        //$data is object(ex: db record)
        if(is_object($data)) {
            $array = [];
            foreach($data as $k => $v) {
                if($k === 'options') $v = unserialize($v);
                $array[$k] = $v;
            }
            $data = $array;
        }

        //merge
        parent::__construct($data);
    }

    /**
     * Return line as array for db insert
     * @return array
     */
    public function toDbArray()
    {
        return [
            'line'       => $this->line,
            'options'    => (is_array($this->options) && !empty($this->options) ? serialize($this->options) : ''),
            'command'    => $this->command,
            'dt_created' => $this->dt_created,
            'cli'        => $this->cli
        ];
    }

    /**
     * Is line empty ?
     * 
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->line);
    }

    /**
     * Transform line to html line 
     * 
     * @param  array  $line
     * @return string    
     */
    public function render($ts = true)
    {
        $this->_replacements();

        // we are in cli, so no html
        if($this->cli) return $this->line;

        $data_attributes = [
            'cmd' => hash('joaat', $this->command),
            'ts'  => strtotime($this->dt_created),
            'dt'  => $this->dt_created
        ];

        $style = '';
        if(is_array($this->options) && !empty($this->options)) {
            foreach($this->options as $k => $v) {
                $style .= $k.':'.$v.';';
            }
        }

        $attrs_string = '';
        foreach($data_attributes as $k => $v) {
            $attrs_string .= ' data-'.$k.'="'.$v.'"';
        }

        $line = '<span '.$attrs_string.' style="'.$style.'">'.$this->line.'</span>';

        if($ts) $line = '<span class="line-ts-block line-ts" data-ts="'.$this->dt_created.'" title="'.$this->dt_created.'"></span> '.$line;

        return $line;
    }

    /**
     * Replace string token(s)
     *
     * Token syntax: {{tokename}}
     * 
     * @return string      
     */
    protected function _replacements()
    {
        if(is_array($this->rep) || is_object($this->rep)) {
            foreach($this->rep as $k => $v) {
                $v = filter_var($v ,FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
                $this->line = str_ireplace('{{'.$k.'}}', $v, $this->line);
            }
        }
        return $this->line;
    }

    /**
     * Trim line 
     * 
     * (DONT WORK)
     * 
     * @param  $lines [description]
     * @return        [description]
     */
    protected function _trim($lines)
    {
        if(!empty($lines)) {
            foreach($lines as $i => $l) {
                if(empty($l->line)) unset($lines[$i]);
                else break; 
            }
            $c = count($lines) - 1;
            
            for($i=$c;$i>0;--$i) {
                if(empty($lines[$i]->line)) unset($lines[$i]);
                else break; 
            }
        }
        return $lines;
    }
}