<?php
require_once 'conf.php';


function makePath($root, $child/*, many string args*/)
{
    $res = $root.DIRECTORY_SEPARATOR.$child;
    $an = func_num_args();
    for($i = 2 ; $i < $an; $i++) {
        $res .= DIRECTORY_SEPARATOR . func_get_arg($i);
    }
    return $res;
}

function basePath($child/*, many string args*/)
{
    $res = BASE_DIR.DIRECTORY_SEPARATOR.$child;
    $an = func_num_args();
    for($i = 1 ; $i < $an; $i++) {
        $res .= DIRECTORY_SEPARATOR . func_get_arg($i);
    }
    return $res;
}