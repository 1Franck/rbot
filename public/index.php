<?php

namespace App\Commands;
define('NS_APP', __NAMESPACE__.'\\');

session_start('rbot-web');

require __DIR__.'/../rbot/loader.php';

use RBot\RBot;
use RBot\Exception;
use RBot\Console;
use App\App;

// process ajax request
if($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        RBot::init(RBot::SANDBOX);
        $app = new App;
    }
    catch(Exception\AuthException $e) {
        //die(json_encode(['error' => $e->getMessage()]));
        Console::addAndOutput($e->getMessage(), 'error');
    }
    catch(Exception\GenericException $e) {
        $exclass = get_class($e);
        $suffix = (RBot::env() === 'dev') ? $exclass : '';
        Console::addAndOutput($e->getMessage().'   "'.$suffix.'"', 'error');
    }
    catch(PDOException $e) {
        echo '<span class="red">'.$e->getMessage().'</span>';
    }

    die();
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
        <pre id="console" class="with-time"></pre>
        <div id="intel" ng-model="intel"></div>
        <input type="text" id="cmd" ng-model="cmd_input" ng-keydown="cmdTyping($event)" autofocus spellcheck="false">
    </div>
    <script src="assets/js/libs.js"></script>
    <script src="assets/js/rbot.min.js"></script>
</body>
</html>