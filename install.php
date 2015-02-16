<?php
/**
 * Created by PhpStorm.
 * User: bzm
 * Date: 26.01.15
 * Time: 17:42
 */

include_once 'engine/incl_all.php';

require_once 'engine/install.php';
echo "Installation begins, please wait<br><br>";

if(installSite()) {
    echo "installation is ok<br> Go to <a href='do.php'>main page</a>";
}else{
    echo "installation failed.<br>Please contact to site administrator.";
}