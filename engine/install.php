<?php
/**
 * Created by PhpStorm.
 * User: bzm
 * Date: 26.01.15
 * Time: 17:24
 */


include_once 'incl_all.php';
require_once 'dao/localization.php';
require_once 'dao/userpic.php';
require_once 'dao/user.php';

function installSite()
{
    if (Localization::initTable()) {
        $cont = json_decode(file_get_contents(basePath('default_localization.json'), true));
        Localization::clear();//todo: make correct REPLACE, to remove this
        $suc = Localization::fromData($cont) != null;
        if (!$suc) {
            echo 'Fail: can\'t load  localization<br>';
            return false;
        }
    } else {
        echo 'Fail: table localization wasn\'t init<br>';
        return false;
    }

    if (!UserPic::initTable()){
        echo 'Fail: table user_pics wasn\'t init<br>';
        return false;
    }
    if (!User::initTable()){
        echo 'Fail: table users wasn\'t init<br>';
        return false;
    }
    return true;
}