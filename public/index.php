<?php

namespace App\Commands;
define('NS_APP', __NAMESPACE__.'\\');

session_start('rbot-web');

require __DIR__.'/../rbot/loader.php';

use RBot\RBot;
use RBot\Exception\CommandNotFound;
use App\App;

?><!DOCTYPE html>
<html style="">
<head>
<style>
<!--
    * {
        font-family: "Source Code Pro";
    }
    body {
        background:#222;
        color:#999;
        overflow-x:hidden;
        padding-top:20px;
    }
    input {
        position:absolute;
        width:100%;
        top:0;
        left:0;
        height:30px;
        background:#444;
        color:#fff;
        border:0;
        outline:none;
        box-shadow:none;
        padding:0 5px;
        font-size: 16px;
    }
    button {
        display: none;
    }
    pre {
       font-size: 14px;
       line-height: 15px;
    }
-->
</style>
</head>
<body>
    
    <form action="" method="post">
        <input type="text" name="cmd" style="" autofocus spellcheck="false">
        <button type="submit">go</button>
    </form>
<pre>
<?php

try {

    $cmd = '';
    if(isset($_POST['cmd'])) {
        $cmd = filter_var($_POST['cmd'], FILTER_SANITIZE_STRING);
    }

    RBot::init(RBot::SANDBOX);

    $app = new app();
    $app->run(RBot::argv('rbot '.$cmd));


}
catch(CommandNotFound $e) {
    echo $e->getMessage();
}
?>
</pre>
</body>
</html>