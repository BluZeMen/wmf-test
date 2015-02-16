<?php
/**
 * Created by PhpStorm.
 * User: bzm
 * Date: 15.12.14
 * Time: 21:44
 */

require_once 'view.php';
require_once 'auth.php';
require_once 'lib/fs.php';

function lstr($str, $alt = null)
{
    $ret = View::getLocalizedString($str);
    if($alt !== null && $ret === $str)
        return $alt;
    return $ret;
}

function urlTo($viewName, array $params = null){
    $loc = View::getUsedLocale() ? '&loc='.View::getUsedLocale() : '';
    $p = '';
    if($params)
        foreach($params as $pname => $pval){
            $p .= '&'.$pname.'='.$pval;
        }
    return 'do.php?a='.$viewName.$loc.$p;
}

function putViewStyle($viewName)
{
    if(file_exists(basePath(PROJ_CONF::STYLES_PATH, $viewName.'.css')))
        echo "<link rel=\"stylesheet\" href=\"styles/$viewName.css\">";
}

function setupView($viewName, $defaultLocale = PROJ_CONF::DEFAULT_LOCALE, $baseName = 'base')
{
    if(View::isSetup())
        return;

    $u = Auth::getLoggedUser();
    if($u){
        View::setup($viewName, $u->locale, $baseName);
    }else{
        $locale = View::getRequestedLocale();
        if(!$locale) $locale = $defaultLocale;
        View::setup($viewName, $locale, $baseName);
    }
}