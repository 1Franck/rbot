<?php

namespace App\Commands;
define('NS_APP', __NAMESPACE__.'\\');

session_start('rbot-web');

require __DIR__.'/../rbot/loader.php';

use RBot\RBot;
use RBot\Exception;
use RBot\Console;
use RBot\ConsoleHistory;
use App\App;

// process ajax request
if($_SERVER['REQUEST_METHOD'] === 'POST') {

    $req = json_decode(file_get_contents('php://input'), true);

    try {
        RBot::init(RBot::SANDBOX);

        if(isset($req['h'])) {
            if(!isset($_SESSION['logged'])) exit();
            if(isset($_SESSION['last_console_id'])) $hid = $_SESSION['last_console_id'];
            else $hid = filter_var($req['h'], FILTER_SANITIZE_NUMBER_INT);
            ConsoleHistory::getLatestLinesFrom($hid);
            exit();
        }

        if(isset($req['ch'])) {
            die(ConsoleHistory::getCommands());
        }

        $cmd = '';
        if(isset($req['cmd'])) {
            $cmd = filter_var($req['cmd'], FILTER_SANITIZE_STRING);
        }

        $app = new app();
        $app->run(RBot::argv('rbot '.$cmd));
    }
    catch(Exception\GenericException $e) {
        $suffix = (RBot::env() === 'dev') ? get_class($e).' ' : '';
        Console::addAndOutput($e->getMessage().'   "'.$suffix.'"', ['color' => '#ff9999']);
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>RBot</title>
</head>
<body>
    <div ng-controller="consoleController" ng-dblclick="focusCmd()">
        <pre id="console"></pre>
        <div id="intel" ng-model="intel"></div>
        <input type="text" id="cmd" ng-model="cmd_input" ng-keydown="cmdTyping($event)" autofocus spellcheck="false">
    </div>
    <script src="assets/js/libs.js"></script>
    <script src="assets/js/rbot.min.js"></script>
</body>
</html>