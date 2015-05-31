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
use RBot\Console;

/**
 * For webcli
 */
class ConsoleLine
{
    /**
     * Transform line to html line 
     * 
     * @param  array  $line
     * @return string    
     */
    static function render($line)
    {
        if(RBot::cliMode()) return $line['line'];

        $style = '';

        if(!empty($line['options'])) {
            foreach($line['options'] as $k=>$v) {
                $style .= $k.':'.$v.';';
            }
        }

        return '<span style="'.$style.'">'.$line['line'].'</span>';
    }
}