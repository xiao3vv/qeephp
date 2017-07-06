<?php

$app_config = require(dirname((dirname(__FILE__))) . '/config/boot.php');
require $app_config['QEEPHP_DIR'] . '/library/q.php';
require $app_config['APP_DIR'] . '/myapp.php';

$ret = MyApp::instance($app_config)->dispatching();
if(!PHP_CLI)
{
    if (is_string($ret)) echo $ret;
    if(is_array($ret))
    {
        header('Content-type: application/json');
        echo json($ret);
    }

    return $ret;
}

