<?php

include_once 'conf.php';

set_include_path(realpath(dirname(__FILE__)));

if(DEBUG::ENABLED) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

include_once 'lib/fs.php';

