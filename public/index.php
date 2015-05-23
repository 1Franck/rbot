<?php

namespace App\Commands;
define('NS_APP', __NAMESPACE__.'\\');

session_start('rbot-web');

require __DIR__.'/../rbot/loader.php';

use RBot\RBot;
use RBot\Exception\CommandNotFound;
use App\App;
use Exception;

?><!DOCTYPE html>
<html style="">
<head>
    <link rel="stylesheet" href="assets/css/rbot.css">
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
catch(Exception $e) {
    echo '<span class="red">'.$e->getMessage().'</span>';
}
?>
</pre>
<script src="assets/js/libs.js"></script>
<script src="assets/js/rbot.min.js"></script>
</body>
</html>