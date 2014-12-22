<?php
/**
 * Created by PhpStorm.
 * User: bzm
 * Date: 21.12.14
 * Time: 15:48
 */

require_once 'dao/localization.php';

class Edit
{
    public static function setTranslationsFromHttpFile($httpFile)
    {
        if (empty($_FILES[$httpFile]['name'])) return null; // such file uploaded
        if ($_FILES[$httpFile]['error'] != 0) return null; // no upload errors
        if ($_FILES[$httpFile]['type'] != 'application/json') return null; // file has a right type

        $cont = json_decode(file_get_contents($_FILES[$httpFile]['tmp_name']),true);
        Localization::clear();//todo: make correct REPLACE, to remove this
        return Localization::fromData($cont) != null;
    }
}