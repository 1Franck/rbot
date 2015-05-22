<?php

namespace App;

use RBot\Application;

/**
 * My RBot app
 */
class App extends Application
{
    public function init()
    {
        $this->rbot_command_prefix = '$';
    }
}