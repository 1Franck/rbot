<?php

namespace App\Commands;
define('NS_APP', __NAMESPACE__.'\\');

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