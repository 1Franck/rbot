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
 * Rbot api requests service
 */
app.service('rbotApiService', ['$http', function($http) {

    var request = function(postdata, s_fn, e_fn) {
        $http
            .post('index.php', postdata)
            .success(function (data) {
               if(typeof s_fn === 'function') s_fn(data);
            })
            .error(function () {
                if(typeof e_fn === 'function') e_fn();
            });
    }

    var error = function(fn) {};
    
    this.getConsoleHistory = function(s_fn, e_fn) {
        request({h: 1}, s_fn, e_fn);
    };

    this.getCommandHistory =  function(s_fn, e_fn) {
        request({ch: 1}, s_fn, e_fn);
    };

    this.commandRequest =  function(postdata, s_fn, e_fn) {
        request(postdata, s_fn, e_fn);
    };

    this.request = function(s_fn, e_fn) {
        request({ch: 1}, s_fn, e_fn);
    };

    this.post = function(postdata, s_fn, e_fn) {
        request(postdata, s_fn, e_fn);
    };

   
}]);

/**
 * Main controller
 */
app.controller('consoleController', ['$scope', 'rbotApiService', function($s, rapi) {

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

        rapi.getConsoleHistory(function(data) {
            if(data.length>0) {
                if(data.error) {
                    el.console.innerHTML = data.error;
                }
                else {
                    el.console.innerHTML += "\n" + data;
                    el.console.scrollTop = el.console.scrollHeight;
                }
            }
        }, 
        function() {
            console.log("Cannot retreive history");
        });
    }

    setInterval(function() {
        $s.getConsoleHistory();
    }, 1000);



    $s.getCommandHistory = function() {

        rapi.getCommandHistory(function(data) {
            if(data.length>0) {
                $s.cmd_history = data;
                $s.cmd_history_index = data.length;
            }
        }, function () {
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
                if($s.cmd_history[$s.cmd_history.length-1] !== undefined &&
                    $s.cmd_input !== $s.cmd_history[$s.cmd_history.length-1].command) {

                    $s.cmd_history.push({ command: $s.cmd_input});
                    $s.cmd_history_index = $s.cmd_history.length;
                }
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
            //console.log($s.cmd_history_index);
            
            $s.cmd_history_index--;
            if($s.cmd_history_index < 0) {
                $s.cmd_history_index = 0;
            }
            else if($s.cmd_history[$s.cmd_history_index] !== undefined) {
                $s.cmd_input = $s.cmd_history[$s.cmd_history_index].command.trim();
            }
            $event.preventDefault();
            //return false;
        }
        else if(keycode == 40) { //down
            //console.log($s.cmd_history_index);
            
            $s.cmd_history_index++;
            if($s.cmd_history_index > ($s.cmd_history.length-1)) {
                $s.cmd_history_index--;
                $s.cmd_input = '';
                //$s.cmd_history_index = $s.cmd_history.length-1;
            }
            else if($s.cmd_history[$s.cmd_history_index].command !== undefined) {
                $s.cmd_input = $s.cmd_history[$s.cmd_history_index].command.trim();
            }
            
            $event.preventDefault();
        }
        else if($s.cmd_input.trim() == "" && keycode == 32) {
            $s.cmd_input = "#";
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

        rapi.commandRequest({cmd: $s.cmd_input}, function(data) {
            if(success_fn) success_fn();
            if(data.error) {
                el.console.innerHTML = data.error;
            }
        }, function () {
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

        var handler = function(e) {
            if(ignoreKey) {
                e.preventDefault();
                return;
            }
            if (e.keyCode == 38 || e.keyCode == 40) {
                e.preventDefault();
            }
        };

        el.cmd.addEventListener('keydown',handler,false);
        el.cmd.addEventListener('keypress',handler,false);
    }
    init();
}]);