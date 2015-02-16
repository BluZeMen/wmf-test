<?php
/**
 * Created by PhpStorm.
 * User: bzm
 * Date: 21.12.14
 * Time: 15:48
 */

require_once 'dao/localization.php';
require_once 'log.php';
require_once 'auth.php';

class Edit
{
    const BAD_TOKEN = 1;
    public static function setTranslationsFromHttpFile($httpFile)
    {
        if (empty($_FILES[$httpFile]['name'])) return null; // such file uploaded
        if ($_FILES[$httpFile]['error'] != 0) return null; // no upload errors
        if ($_FILES[$httpFile]['type'] != 'application/json') return null; // file has a right type

        $cont = json_decode(file_get_contents($_FILES[$httpFile]['tmp_name']),true);
        //Localization::clear();//todo: make correct REPLACE, to remove this
        $suc = Localization::fromData($cont) != null;
        if($suc){
            Log::get('editor')->info('New localization is successfully installed by'.PHP_EOL.Auth::getLoggedUser()->toString());
        }else{
            Log::get('editor')->error('Localization installation fail. Operator:'.PHP_EOL.Auth::getLoggedUser()->toString());
        }
        return $suc;
    }

    public static function setNewPassword($rawPassword)
    {
        filter_var($rawPassword, FILTER_SANITIZE_STRING);
        $u = Auth::getLoggedUser();
        Auth::setNewPassword(Auth::getLoggedUser(), $rawPassword);
        return $u->save();
    }
}