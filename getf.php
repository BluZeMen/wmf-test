<?php
/**
 * Created by PhpStorm.
 * User: bzm
 * Date: 13.12.14
 * Time: 21:50
 */

require_once 'engine/incl_all.php';
require_once 'engine/dao/userpic.php';

if(isset($_GET['up'])){
    $up = UserPic::getById($_GET['up']);
    if(!$up){
        header('HTTP/1.0 404 Not Found');
        exit;
    }

    header("Content-Type: ". $up->filetype);
    header("Content-Length: " . $up->filesize);
    echo $up->bin_data;
}