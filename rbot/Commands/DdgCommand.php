<?php
/*
 * This file is part of the RBot.
 *
 * (c) Francois Lajoie <o_o@francoislajoie.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RBot\Commands;

use RBot\RBot;
use RBot\Command;
use RBot\Console;

/*
 * Duckduckgo api
 */
class DdgCommand extends Command 
{
    /**
     * Command Options
     */
    public function setOptions() 
    {
        $this->_options->add('s+', 'Search query' )->isa('String');
        
    }

    /**
     * Process the command
     */
    public function process()
    {
        $query = $this->rawData();

        //http://api.duckduckgo.com/?q=valley+forge+national+park&format=json&pretty=1
        $json = json_decode(file_get_contents('http://api.duckduckgo.com/?q='.urlencode($this->rawData()).'&format=json&pretty=1'));

        $this->_guess($json);
    }

    /**
     * Guess the good and short anwser
     * 
     * @param  object $anwser       
     */
    protected function _guess($anwser)
    {
        if(empty($anwser->AnswerType)) {
            if(!empty($anwser->AbstractText)) {
                Console::add($anwser->AbstractText);
            }
            elseif(!empty($anwser->AbstractURL)) {
                 Console::add($anwser->AbstractURL);
            }
            else {
                Console::add(htmlentities(print_r($anwser, true)));
            }
            //Console::add(htmlentities(print_r($anwser, true)));
        }
        else {
            Console::add($anwser->AnswerType.' >> '.strip_tags($anwser->Answer));
        }

        Console::output();
    }

}