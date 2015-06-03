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
        self::$RBOT_CMD_PREFIX = '#';
    }
}