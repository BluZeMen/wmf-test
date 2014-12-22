<?php
/**
 * Created by PhpStorm.
 * User: bzm
 * Date: 13.12.14
 * Time: 23:26
 */

function getViewsPath(){
    return BASE_DIR.PROJ_CONF::VIEWS_PATH.DIRECTORY_SEPARATOR;
}


function render($view, array $params = null, $defaultLocale = PROJ_CONF::DEFAULT_LOCALE, $base = 'base')
{
    $__view_of_content = $view;
    if($params) {
        if(sizeof($params) != extract($params))
            new RuntimeException('Template error: can\'t import variables' );
    }

    include 'incl_view.php';
    setupView($view, $defaultLocale, $base);
    //include "view/$base.php";
    include getViewsPath().$base.'.php';

    exit;
}
