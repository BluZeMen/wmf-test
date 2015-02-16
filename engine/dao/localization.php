<?php
/**
 * Created by PhpStorm.
 * User: bzm
 * Date: 15.12.14
 * Time: 19:57
 */
require_once 'lib/json.php';
require_once 'dao/db.php';
require_once 'log.php';

class Localization {
    const TABLE_NAME = 'localization';
    public $page = null; // name_of_page.name_of_locale
    public $strings = null; // json document of dictionary

    const MAX_LEN_PAGE = 50;
    const MAX_LEN_STRINGS = DB::LEN_MYSQL_MEDIUMTEXT;

    static public function initTable(){
        if(DB::isTableExist(self::TABLE_NAME)){
            return true;
        }

        $pl = self::MAX_LEN_PAGE;
        $q = "CREATE TABLE IF NOT EXISTS `localization` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `page` varchar($pl) COLLATE utf8_bin NOT NULL,
        `strings` mediumtext COLLATE utf8_bin NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
        $sh = DB::getConnection()->exec($q);
        return DB::isTableExist(self::TABLE_NAME);
    }

    static function fromData(array $data)
    {
        if($data['type'] == 'single') {
            $loc = self::buildFromData($data);
            return $loc->save() ? array($loc) : null;

        }elseif($data['type'] == 'multi') {
            $locs = array();
            foreach($data['translates'] as $t){
                array_push($locs,self::buildFromData($t));
            }
            if(empty($locs)) return null;

            $all_ok = true;
            foreach($locs as $l){
                if(!$l->save()) $all_ok = false;
            }

            return  $all_ok ? $locs : null;
        }
        return null;
    }

    private static function buildFromData(array $data){
        $loc = new Localization();
        $loc->page = $data['page'];
        $loc->strings = $data['strings'];

        return $loc;
    }

    public static function getLocalization($loc, $page)
    {
        $page = $page.'.'.$loc;
        $q = "SELECT page, strings
            FROM " . self::TABLE_NAME . " WHERE page='$page'";
        $sh = DB::getConnection()->query($q);
        $sh->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
        $s = $sh->fetch();
        return $s;
    }

    public static function getStrings($loc, $page)
    {
        $page = $page.'.'.$loc;
        $q = "SELECT strings
            FROM " . self::TABLE_NAME .
            " WHERE page='$page'";

        try {
            $sh = DB::getConnection()->query($q);
        }catch (Exception $e){
            //echo var_dump($e);
            Log::get('db')->error(var_dump($e));
        }
        $sh->setFetchMode(PDO::FETCH_ASSOC);
        $s = $sh->fetch();
        $s = json_decode($s['strings'], true);
        return $s;
    }

    public function getString($sname)
    {
        if (is_string($this->strings)) {
            $this->strings = json_decode($this->strings, true);
        }
        return $this->strings[$sname];
    }

    public function save()
    {
        if (!is_string($this->strings)) {
            $this->strings = json_encode_u($this->strings);
        }
        $ses = DB::getConnection()->prepare(
            "REPLACE INTO ".self::TABLE_NAME." (page, strings)
            VALUES (:page, :strings)");

        return $ses->execute((array)$this);;
    }
}