<?php
require_once(dirname(__FILE__) . '/../setup.php');
lmb_require('src/BuildmanApplication.class.php');
lmb_require('limb/web_app/src/controller/*.class.php');

$application = new BuildmanApplication();
$application->process();

?>
