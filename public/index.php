<?php

namespace App\Commands;
define('NS_APP', __NAMESPACE__.'\\');

session_start('rbot-web');

require __DIR__.'/../rbot/loader.php';

use RBot\RBot;
use RBot\Exception;
use RBot\ConsoleHistory;
use App\App;

// process ajax request
if($_SERVER['REQUEST_METHOD'] === 'POST') {

    $req = json_decode(file_get_contents('php://input'), true);

    try {

        RBot::init(RBot::SANDBOX);

        $cmd = '';
        if(isset($req['cmd'])) {
            $cmd = filter_var($req['cmd'], FILTER_SANITIZE_STRING);
        }

        if(isset($req['h'])) {
            if(!isset($_SESSION['logged'])) exit();
            if(isset($_SESSION['last_console_id'])) $hid = $_SESSION['last_console_id'];
            else $hid = filter_var($req['h'], FILTER_SANITIZE_NUMBER_INT);
            ConsoleHistory::getLatestLinesFrom($hid);
            exit();
        }

        

        $app = new app();
        $app->run(RBot::argv('rbot '.$cmd));


    }
    catch(Exception\GenericException $e) {
        $prefix = (RBot::env() === 'dev') ? get_class($e).' ' : '';
        echo "\n".'<span class="red">'.$prefix.$e->getMessage().'</span>'."\n";
    }
    /*catch(Exception $e) {
        echo '<span class="red">'.$e->getMessage().'</span>';
    }*/

    exit();
}
// or first loading page request
?><!DOCTYPE html>
<html ng-app="rbot">
<head>
    <link rel="stylesheet" href="assets/css/rbot.css">
</head>
<body>

    <div ng-controller="consoleController" ng-dblclick="focusCmd()">
        <pre id="console"></pre>
        <!--<pre id="console" ng-bind-html="console | to_trusted"></pre>-->
        <div id="intel" ng-model="intel"></div>
        <input type="text" id="cmd" ng-model="cmd_input" ng-keydown="cmdTyping($event)" autofocus spellcheck="false">
    </div>

    <script src="assets/js/libs.js"></script>
    <script src="assets/js/rbot.min.js"></script>
</body>
</html>