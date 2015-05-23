"use strict";

/**
 * Rbot Angular App
 */
var app = angular.module('rbot', []);

/**
 * Main controller
 */

app.controller('consoleController', ['$scope', '$http', function($s, $http) {

    $s.cmd_history       = [];
    $s.cmd_input         = "$ ";
    $s.cmd_input_default = "$ ";
    $s.console           = "";

    $s.cmdTyping = function($event) {

        var keycode = (window.event ? $event.keyCode : $event.which);
        //console.log(keycode);

        if(keycode == 13) {
            //enter
            request();
            $s.cmd_history.push($s.cmd_input);
            $s.cmd_input = $s.cmd_input_default;
        }
        else if(keycode == 8) {
            //backspace
            if($s.cmd_input.trim() == "$") {
                $s.cmd_input = "";
            }
        }
        else if(keycode == 38) {
            //up
            $s.cmd_input = $s.cmd_history[$s.cmd_history.length-1];
        }
        else if(keycode == 40) {
            //down
        }
        else if($s.cmd_input.trim() == "" && keycode == 32) {
            $s.cmd_input = "$";
        }
    };



    function request() {

        console.log($s.cmd_input);
        $http.post('index.php', {cmd: $s.cmd_input})
            .success(function (data) {
                console.log("Resolved: " + $s.cmd_input);   
                $s.console = data + "\n" + $s.console;
            })
        .error(function () {
            console.log("Cannot resolve: " + $s.cmd_input);
        });
    }

    function init() { }

    init();

}]);