<?php
/**
 * Created by PhpStorm.
 * User: bzm
 * Date: 15.12.14
 * Time: 19:57
 */

class Localization {
    const TABLE_NAME = 'localization';
    public $locale = null; // short name of locale
    public $page = null; // name of page translations
    public $strings = null; // json document of dictionary

    const MAX_LEN_LANG = 5;
    const MAX_LEN_PAGE = 30;
    const MAX_LEN_STRINGS = DB::LEN_MYSQL_MEDIUMTEXT;


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
        $loc->locale = $data['locale'];
        $loc->page = $data['page'];
        $loc->strings = $data['strings'];

        return $loc;
    }

    public static function getLocalization($loc, $page)
    {
        $q = "SELECT locale, page, strings
            FROM " . self::TABLE_NAME . " WHERE locale='$loc' AND page='$page'";
        $sh = DB::getConnection()->query($q);
        $sh->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
        $s = $sh->fetch();
        return $s;
    }

    public static function getStrings($loc, $page)
    {
        $q = "SELECT strings
            FROM " . self::TABLE_NAME .
            " WHERE locale='$loc' AND page='$page'";
        $sh = DB::getConnection()->query($q);
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

    public static function clear(){
        $ses = DB::getConnection()->prepare(
            "TRUNCATE TABLE ".self::TABLE_NAME);
        return $ses->execute();;
    }

    public function save()
    {
        if (!is_string($this->strings)) {
            $this->strings = json_encode($this->strings, JSON_UNESCAPED_UNICODE);
        }
        $ses = DB::getConnection()->prepare(
            "REPLACE INTO ".self::TABLE_NAME." (locale, page, strings)
            VALUES (:locale, :page, :strings)");

        return $ses->execute((array)$this);;
    }
}