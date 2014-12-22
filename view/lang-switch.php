<?php
/**
 * Created by PhpStorm.
 * User: bzm
 * Date: 18.12.14
 * Time: 1:07
 */

require_once 'auth.php';

$action = (string)(isset($_GET['a']) ? $_GET['a'] : $__view_of_content);
$otherparams = '';

foreach($_GET as $pname => $pval){
    if($pname == 'a' || $pname == 'loc' ) continue;
    $otherparams = $otherparams."&$pname=$pval";
}

if(Auth::isLogged()) {
    if (Auth::getLoggedUser()->locale != 'en-us') {
        echo '<a href="do.php?a='.$action.$otherparams.'&loc=en-us">in english</a><br>';
    }

    if (Auth::getLoggedUser()->locale != 'ru-ru') {
        echo '<a href="do.php?a='.$action.$otherparams.'&loc=ru-ru">по русски</a><br>';
    }
}else{
    echo '<a href="do.php?a='.$action.$otherparams.'&loc=en-us">in english</a><br>';
    echo '<a href="do.php?a='.$action.$otherparams.'&loc=ru-ru">по русски</a><br>';
}

?>

