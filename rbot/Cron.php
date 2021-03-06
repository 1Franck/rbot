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
            'SELECT * FROM cron WHERE 
            (dt_executed IS NULL AND UNIX_TIMESTAMP(dt_created + INTERVAL repeat_time SECOND) <= UNIX_TIMESTAMP(NOW())) OR 
            (dt_executed IS NOT NULL AND UNIX_TIMESTAMP(dt_executed + INTERVAL repeat_time SECOND) <= UNIX_TIMESTAMP(NOW()))'
        );

        if(!empty($results)) {
            foreach($results as $r) {

                RBot::argv(' '.trim($r->task));

                try {
                    $app->run(RBot::argv());
                    $faulty    = false;
                    $fault_msg = null;
                }
                catch(Exception\GenericException $e) {
                    $faulty    = true;
                    $fault_msg = $e->getMessage();
                }

                if($r->repeat == 0 && $faulty === false) {
                    //delete task
                    Capsule::table('cron')->where('id', '=', $r->id)->delete();
                }
                else {
                    //update task
                    Capsule::table('cron')
                        ->where('id', '=', $r->id)
                        ->update([
                            'dt_executed' => date('Y-m-d H:i:s'), 
                            'execution'   => $r->execution+1,
                            'faulty'      => $faulty,
                            'fault_msg'   => $fault_msg
                        ]);
                }
            }
        }
    }
}