<?php
/**
 * Created by PhpStorm.
 * User: bzm
 * Date: 02.01.15
 * Time: 12:46
 */

function redirect($url, $statusCode = 303)
{
    header('Location: ' . $url, true, $statusCode);
    exit();
}
