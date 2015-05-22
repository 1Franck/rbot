#!/usr/bin/env php
<?php
/*
 * This file is part of the RBot app.
 *
 * (c) Francois Lajoie <o_o@francoislajoie.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Commands\Test;

require __DIR__.'/../../../rbot/loader.php';

use RBot\RBot;

RBot::init(RBot::SANDBOX);
RBot::run(new TestCommand());