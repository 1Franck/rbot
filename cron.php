<?php
/*
 * This file is part of the RBot.
 *
 * (c) Francois Lajoie <o_o@francoislajoie.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Commands;
if(!defined('NS_APP')) define('NS_APP', __NAMESPACE__.'\\');

require __DIR__.'/rbot/loader.php';

use RBot\RBot;
use RBot\Cron;
use RBot\Exception;
use App\App;

try {
    $app  = new app();
    $cron = new Cron();
    $cron->run($app);
}
catch(Exception\GenericException $e) {
    echo "\n".$e->getMessage();
}