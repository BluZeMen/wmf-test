<?php
/**
 * Created by PhpStorm.
 * User: bzm
 * Date: 15.12.14
 * Time: 22:07
 */

require_once 'dao/localization.php';

class View
{
    private static $name = null;
    private static $base = null;
    private static $locale = null;
    private static $dict = null;

    public static function setup($name, $lang='ru-ru', $base='base')
    {
        self::$name = $name;
        self::$base = $base;
        self::$locale = $lang;

        self::$dict = Localization::getStrings($lang, $name);
        $bdict = Localization::getStrings($lang, $base);
        if(self::$dict && $bdict) {
            self::$dict += $bdict;
        }elseif(!self::$dict && $bdict){
            self::$dict = $bdict;
        }
    }

    public static function getLocalizedString($sname)
    {
        return isset(self::$dict[$sname]) ? self::$dict[$sname] : "{$sname}";
    }

    public static function isSetup()
    {
        return self::$locale != null;
    }

    public static function getRequestedLocale()
    {
        return isset($_GET['loc']) ? filter_var($_GET['loc'], FILTER_SANITIZE_STRING) : null;
    }

    public static function getUsedLocale()
    {
        return self::$locale;
    }

    public static function getLanguage(){
        if(!self::$locale)
            return substr(PROJ_CONF::DEFAULT_LOCALE, 0, 2);
        return substr(self::$locale, 0, 2);
    }

    public static function getRequestedLanguage()
    {
        return isset($_GET['loc']) ? substr(filter_var($_GET['loc'], FILTER_SANITIZE_STRING), 0, 2) : null;
    }
}