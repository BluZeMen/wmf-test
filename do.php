<?php
/**
 * Created by PhpStorm.
 * User: bzm
 * Date: 13.12.14
 * Time: 22:31
 */

require_once 'engine/incl_all.php';
require_once 'engine/auth.php';

require_once 'engine/utils.php';
require_once 'engine/render.php';
require_once 'engine/view.php';


$viewForRender = null;
$urlRedirect = null;
$renderParams = null;
$renderBase = 'base';
$statusCode = 200;
$locale = View::getRequestedLocale();
if(!$locale) $locale = PROJ_CONF::DEFAULT_LOCALE;

if (Auth::isLogged()) {
    if($locale != Auth::getLoggedUser()->locale){
        Auth::getLoggedUser()->locale = $locale;
        Auth::getLoggedUser()->save();
    }
}


function isFormPosted(){
    return isset($_POST['email']) ? $_POST['email'] != null : false;
}

function setRedirect($url, $status = 200){
    global $urlRedirect, $statusCode;
    $urlRedirect = $url;
    $statusCode = $status;
}

function setToRender($view, $params = null, $base = 'base'){
    global $renderParams, $renderBase, $viewForRender;
    $viewForRender = $view;
    if($renderParams === null){
        $renderParams = $params;
    }else{
        $renderParams = array_merge($renderParams, $params);
    }

    $renderBase = $base;
}

if(isset($_GET['a'])) {
    switch ($_GET['a']) {
        case 'logout': {
            if (Auth::isLogged()) {
                Auth::logout();
            }
            setRedirect('do.php?a=login');
            break;
        }
        case 'login':{
            if (Auth::isLogged()) {
                setRedirect('do.php?a=profile');
                break;
            }elseif(isFormPosted()){
                $l = Auth::login($_POST);
                setRedirect($l == Auth::OK ? 'do.php?a=profile' : 'do.php?a=login&error='.$l);
                break;
            }
            setToRender('login');
            break;
        }

        case 'register':{
            if (Auth::isLogged()) {
                setRedirect('do.php?a=profile');
                break;
            }elseif(isFormPosted()){
                //getting avatar
                $a = isset($_FILES['avatar']['tmp_name']) ? $_FILES['avatar'] : null;

                //if it was send, let's get the it's data
                if($a){
                    $a = array(
                        'description' => null,
                        'filename' => (string)$a['name'],
                        'filetype' => (string)$a['type'],
                        'filesize' => (int)$a['size'],
                        'bin_data' => $a['tmp_name'] != null ? file_get_contents($a['tmp_name']) : null
                    );
                }

                $data = $_POST;
                $data += array('avatar' => $a);
                $reg = Auth::registerNew($data);
                if($reg == Auth::OK ) {
                    $l = Auth::login($data);
                    setRedirect($l == Auth::OK ? 'do.php?a=profile' : 'do.php?a=login&error='.$l);
                }else{
                    setToRender('register', array('error_code' => $reg));
                }
                break;
            }

            setToRender('register');
            break;
        }

        case 'profile':{
            if (Auth::isLogged()) {
                setToRender('profile');

            }else {
                setRedirect('do.php?a=login');
            }
            break;
        }

        case 'editor':{
            if (Auth::isLogged()) {
                setToRender('editor');

            }else {
                setRedirect('do.php?a=login');
            }
            break;
        }

        case 'reader':{
            $reqDoc =  isset($_GET['doc']) ? filter_var($_GET['doc'], FILTER_SANITIZE_STRING) : 'doc-not-found';
            $fullPath = BASE_DIR.DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR.
                $reqDoc.'-'.View::getRequestedLanguage().'.txt';
            //echo View::getLanguage();
            setToRender('reader', array('doc_full_path' => $fullPath, 'alt_page_title' => $reqDoc));
            break;
        }

    }
}

if($urlRedirect) {
    if($locale != PROJ_CONF::DEFAULT_LOCALE) {
        $pref = strpos($urlRedirect, '?') ? '&':'?';
        $urlRedirect = $urlRedirect.$pref.'loc='.$locale;
    }
    redirect($urlRedirect);
}

if($viewForRender)
    render($viewForRender, $renderParams, $locale, $renderBase);

//by default
redirect('do.php?a=login&loc='.$locale);


?>