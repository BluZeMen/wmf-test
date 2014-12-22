<?php
/**
 * Created by PhpStorm.
 * User: bzm
 * Date: 06.12.14
 * Time: 17:49
 */

require_once 'db.php';

class UserPic
{
    const TABLE_NAME = 'user_pics';
    public $description = null;
    public $bin_data = null;
    public $filename = null;
    public $filesize = null;
    public $filetype = null;

    const MAX_LEN_DESCRIPTION = 50;
    const MAX_LEN_FILENAME = 50;
    const MAX_LEN_FILETYPE = 80;
    const MAX_FILESIZE = 16777215;

    static function fromHTTPFile($filename)
    {
        if (empty($_FILES[$filename]['name'])) return false;
        // Проверяем, что при загрузке не произошло ошибок
        if ($_FILES[$filename]['error'] == 0) return false;
        // Если файл загружен успешно, то проверяем - графический ли он
        $type = $_FILES[$filename]['type'];
        if ($type != 'image/jpeg' && $type != 'image/jpeg' && $type != 'image/gif') return false;

        $up = new UserPic();
        $up->description = 'no description';
        $up->bin_data = file_get_contents($_FILES[$filename]['tmp_name']);
        $up->filename = $_FILES[$filename]['name'];
        $up->filesize = $_FILES[$filename]['size'];
        $up->filetype = $_FILES[$filename]['type'];

        return $up->save() ? $up : null;
    }

    public static function isValidNewData(array $data)
    {
        //if(!isset($data['description']) || strlen($data['description']) > self::MAX_LEN_DESCRIPTION) return false;
        if(!isset($data['filename']) || strlen($data['filename']) > self::MAX_LEN_FILENAME) return false;
        if(!isset($data['filesize']) || $data['filesize'] === 0 || $data['filesize'] > self::MAX_FILESIZE) return false;
        if(!isset($data['filetype']) || strlen($data['filetype']) > self::MAX_LEN_FILETYPE) return false;
        return true;
    }

    public static function getById($id)
    {
        $id = (int)$id;
        $q = "SELECT description, bin_data, filename, filesize, filetype
            FROM " . self::TABLE_NAME . " WHERE id='$id'";
        $sh = DB::getConnection()->query($q);
        $sh->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
        $s = $sh->fetch();
        return $s;
    }

    public static function getLastId(){
        return (int)DB::getConnection()->lastInsertId();
    }

    public function save()
    {
        $ses = DB::getConnection()->prepare(
            "REPLACE INTO ".self::TABLE_NAME." (description, bin_data, filename, filesize, filetype)
            VALUES (:description, :bin_data, :filename, :filesize, :filetype)");

        return $ses->execute((array)$this);;
    }

}