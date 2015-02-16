<?php
/**
 * Created by PhpStorm.
 * User: bzm
 * Date: 27.12.14
 * Time: 16:18
 */

class Time
{
    static function getLocal() {
        return date_create('now', new DateTimeZone(PROJ_CONF::TIME_ZONE));
    }

    static function getLocalFromString($time, $format = PROJ_CONF::TIME_FORMAT)
    {
        return date_create_from_format($format, $time, new DateTimeZone(PROJ_CONF::TIME_ZONE));
    }

    static function getUserTime($userTimeZone)
    {
        // set timezone to user timezone
        date_default_timezone_set($userTimeZone);

        $date = new DateTime('now');
        $date->setTimezone(new DateTimeZone(PROJ_CONF::TIME_ZONE));
        $str_server_now = $date->format(PROJ_CONF::TIME_FORMAT);

        // return timezone to server default
        date_default_timezone_set(PROJ_CONF::TIME_ZONE);

        return $str_server_now;
    }
}