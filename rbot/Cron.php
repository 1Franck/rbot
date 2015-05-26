<?php
/*
 * This file is part of the RBot app.
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

use Illuminate\Database\Capsule\Manager as Capsule;

/*
 * RBot Cron
 */
class Cron
{
    /**
     * Start cron
     */
    public function __construct()
    {
        RBot::init(RBot::SANDBOX);
    }

    /**
     * Run App Queue
     * 
     * @param  Rbot\Application $app
     */
    public function run($app)
    {
        Capsule::connection()->disableQueryLog();

        $results = Capsule::select(
            'SELECT * FROM queue WHERE 
            (dt_executed IS NULL AND (FROM_UNIXTIME(UNIX_TIMESTAMP(dt_created)+repeat_time, "%Y-%m-%d %H:%i:%s")) <= NOW()) OR 
            (dt_executed IS NOT NULL AND (FROM_UNIXTIME(UNIX_TIMESTAMP(dt_executed)+repeat_time, "%Y-%m-%d %H:%i:%s")) <= NOW())'
        );

        if(!empty($results)) {
            foreach($results as $r) {

                $app->run(RBot::argv($r->task));

                if($r->repeat == 0) {
                    //delete task
                    Capsule::table('queue')->where('id', '=', $r->id)->delete();
                }
                else {
                    //update task
                    Capsule::table('queue')
                        ->where('id', '=', $r->id)
                        ->update(['dt_executed' => date('Y-m-d H:i:s')]);
                }
            }
        }
    }
}