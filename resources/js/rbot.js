"use strict";

/**
 * Rbot Angular App
 */
var app = angular.module('rbot', []);

var cmd_prefix = cmd_prefix || '#';

/**
 * HTML filter
 */
app.filter('to_trusted', ['$sce', function($sce){
    return function(text) {
        return $sce.trustAsHtml(text);
    };
}]);

/**
 * Main controller
 */
app.controller('consoleController', ['$scope', '$http', function($s, $http) {

    $s.cmd_history       = [];
    $s.cmd_history_index = 0;
    $s.cmd_input         = cmd_prefix;
    $s.cmd_input_default = cmd_prefix;

    var el = {
        cmd: document.getElementById("cmd"),
        console: document.getElementById("console")
    }

    var ui_cmds = {
        clear: function() {
            el.console.innerHTML = '';
        }
    }

    /**
     * Get console history
     */
    $s.getConsoleHistory = function() {
        $http.post('index.php', {h: 1})
            .success(function (data) {
                if(data.length>0) {
                    el.console.innerHTML += "\n" + data;
                    el.console.scrollTop = el.console.scrollHeight;
                }
            })
        .error(function () {
            console.log("Cannot retreive history");
        });
    }

    setInterval(function() {
        $s.getConsoleHistory();
    }, 1000);



    $s.getCommandHistory = function() {
        $http.post('index.php', {ch: 1})
            .success(function (data) {
                if(data.length>0) {
                    $s.cmd_history = data;
                    $s.cmd_history_index = data.length;
                }
            })
        .error(function () {
            console.log("Cannot retreive command history");
        });
    }

    $s.getCommandHistory();

    //set full screen console
    //console.innerHeight = window.outerHeight;

    /**
     * Analyse cmd input keydown
     */
    $s.cmdTyping = function($event) {

        var keycode = (window.event ? $event.keyCode : $event.which);
        //console.log(keycode);

        if(keycode == 13) {
            //enter
            if($s.cmd_input === cmd_prefix || $s.cmd_input === '') {
                el.console.innerHTML += "\n";
                el.console.scrollTop = el.console.scrollHeight;
            }
            else {
                request($s.getConsoleHistory);
                $s.cmd_history.push({ command: $s.cmd_input});
                $s.cmd_history_index = $s.cmd_history.length;
                $s.cmd_input = $s.cmd_input_default;
            }
        }
        else if(keycode == 8) {
            //backspace
            if($s.cmd_input == cmd_prefix) {
                $s.cmd_input = "";
            }
        }
        else if(keycode == 38) { //up
            console.log($s.cmd_history_index);
            
            $s.cmd_history_index--;
            if($s.cmd_history_index < 0) {
                $s.cmd_history_index = 0;
            }
            else {
                $s.cmd_input = $s.cmd_history[$s.cmd_history_index].command.trim();
            }
            $event.preventDefault();
            //return false;
        }
        else if(keycode == 40) { //down
            console.log($s.cmd_history_index);
            
            $s.cmd_history_index++;
            if($s.cmd_history_index > ($s.cmd_history.length-1)) {
                $s.cmd_history_index--;
                //$s.cmd_history_index = $s.cmd_history.length-1;
            }
            else if($s.cmd_history[$s.cmd_history_index].command !== undefined) {
                $s.cmd_input = $s.cmd_history[$s.cmd_history_index].command.trim();
            }
            
            $event.preventDefault();
        }
        else if($s.cmd_input.trim() == "" && keycode == 32) {
            $s.cmd_input = "$";
        }
    };

    $s.focusCmd = function() {
        el.cmd.focus();
    }

    /**
     * Send cmd request
     */
    function request(success_fn) {

        if(isUiCmd($s.cmd_input)) {
            ui_cmds[$s.cmd_input]();
            return;
        }

        //console.log($s.cmd_input);
        $http.post('index.php', {cmd: $s.cmd_input})
            .success(function (data) {
                if(success_fn) {
                    success_fn();
                }
                if(data.error) {
                    el.console.innerHTML = data.error;
                }
            })
        .error(function () {
            console.log("Cannot resolve: " + $s.cmd_input);
        });
    }

    /**
     * Check if command is a ui command
     * 
     * @param  string  cmd
     * @return boolean
     */
    function isUiCmd(cmd) {
        return ui_cmds[cmd] ? true : false;
    }

    function init() {

        document.body.addEventListener("dblclick",function() {
            console.log('dlclock');
            document.getElementById("cmd").focus();
        });

        var ignoreKey = false;
        var handler = function(e)
        {
            if (ignoreKey)
            {
                e.preventDefault();
                return;
            }
            if (e.keyCode == 38 || e.keyCode == 40) 
            {
                /*var pos = this.selectionStart;
                this.value = (e.keyCode == 38?1:-1)+parseInt(this.value,10);        
                this.selectionStart = pos; this.selectionEnd = pos;

                ignoreKey = true; setTimeout(function(){ignoreKey=false},1);*/
                e.preventDefault();
            }
        };

        el.cmd.addEventListener('keydown',handler,false);
        el.cmd.addEventListener('keypress',handler,false);
    }
    init();
}]);