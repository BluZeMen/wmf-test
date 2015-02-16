<?php

require_once 'engine/incl_all.php';

$TESTING = false;
//$TESTING = true;

if(DEBUG::ENABLED){
    if($TESTING) {
        include 'test.php';
        exit;
    }
    include 'do.php';

}else{
    include 'do.php';
}

