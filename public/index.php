<?php

namespace App\Commands;
define('NS_APP', __NAMESPACE__.'\\');

session_start('rbot-web');

require __DIR__.'/../rbot/loader.php';

use RBot\RBot;
use RBot\Exception\CommandNotFound;
use App\App;
use Exception;

// process ajax request
if($_SERVER['REQUEST_METHOD'] === 'POST') {

    $req = json_decode(file_get_contents('php://input'), true);

    try {

        $cmd = '';
        if(isset($req['cmd'])) {
            $cmd = filter_var($req['cmd'], FILTER_SANITIZE_STRING);
        }

        RBot::init(RBot::SANDBOX);

        $app = new app();
        $app->run(RBot::argv('rbot '.$cmd));


    }
    catch(Exception $e) {
        echo '<span class="red">'.$e->getMessage().'</span>';
    }

    exit();
}
// or first loading page request
?><!DOCTYPE html>
<html ng-app="rbot">
<head>
    <link rel="stylesheet" href="assets/css/rbot.css">
</head>
<body>

    <div ng-controller="consoleController">
        <form>
            <input type="text" id="cmd" ng-model="cmd_input" ng-keydown="cmdTyping($event)" autofocus spellcheck="false">
            <!-- <button type="submit">go</button> -->
        </form>
        <pre ng-bind-html="console | to_trusted">{{ console }} </pre>
    </div>

    <script src="assets/js/libs.js"></script>
    <script src="assets/js/rbot.min.js"></script>
</body>
</html>