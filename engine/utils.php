<?php
require_once 'conf.php';

class time
{
    static function getLocal()
    {
        return date_create('now', new DateTimeZone(PROJ_CONF::TIME_ZONE));
    }
}

function redirect($url, $statusCode = 303)
{
    header('Location: ' . $url, true, $statusCode);
    exit();
}