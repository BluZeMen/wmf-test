<?php
/**
 * Created by PhpStorm.
 * User: bzm
 * Date: 05.12.14
 * Time: 16:22
 */

require_once 'db.php';
require_once 'userpic.php';
require_once 'conf.php';


class User
{
    const TABLE_NAME = 'users';
    //public $id; // user id in db
    public $fname; // first name
    public $sname; // surname
    public $email; // user's email
    public $salt; //salt for user's password
    public $password; //hash of password
    public $avatar; //id of image associated as user's avatar
    public $locale; //name of user's locale
    public $params; //text field for storing user's preferences on service

    //const MAX_LEN_ID = 11;
    const MAX_LEN_FNAME = 30;
    const MAX_LEN_SNAME = 30;
    const MAX_LEN_EMAIL = 30;
    const MAX_LEN_SALT = 30;
    const MAX_LEN_PASSWORD = 30;
    const MAX_LEN_LOCALE = 5;
    const MAX_LEN_PARAMS = DB::LEN_MYSQL_MEDIUMTEXT;



    public function __construct()
    {
        $this->params = json_decode($this->params, true);
    }

    public static function getUser($email)
    {
        $q = "SELECT fname, sname, email, salt, password, avatar, locale, params
            FROM " . self::TABLE_NAME . " WHERE email='$email'";
        $sh = DB::getConnection()->query($q);
        $sh->setFetchMode(PDO::FETCH_CLASS, __CLASS__);

        return $sh->fetch();
    }

    static public function initTable(){
        if(DB::isTableExist(self::TABLE_NAME)){
            return true;
        }
        $q = "CREATE TABLE IF NOT EXISTS `users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `fname` varchar(30) COLLATE utf8_bin NOT NULL,
        `sname` varchar(30) COLLATE utf8_bin NOT NULL,
        `email` varchar(30) COLLATE utf8_bin NOT NULL,
        `salt` char(30) COLLATE utf8_bin NOT NULL,
        `password` char(60) COLLATE utf8_bin NOT NULL,
        `avatar` int(11) NOT NULL DEFAULT '1',
        `locale` varchar(5) COLLATE utf8_bin NOT NULL,
        `params` mediumtext COLLATE utf8_bin NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`),
        UNIQUE KEY `fname` (`fname`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
        $sh = DB::getConnection()->exec($q);
        return DB::isTableExist(self::TABLE_NAME);
    }

    public static function getUserIdByEmail($email)
    {
        $q = "SELECT id
            FROM  users
            WHERE email='$email'";
        $sh = DB::getConnection()->query($q);
        $sh->setFetchMode(PDO::FETCH_ASSOC);
        $user_data = $sh->fetch();
        return $user_data != null ? $user_data['id'] : false;
    }

    public static function getUserId($email, $passw_hash)
    {
        $q = "SELECT id
            FROM  users
            WHERE email='$email' AND password='$passw_hash'";
        $sh = DB::getConnection()->query($q);
        $sh->setFetchMode(PDO::FETCH_ASSOC);
        $user_data = $sh->fetch();
        return $user_data != null ? $user_data['id'] : false;
    }

    public static function isUserExists($email, $passw_hash){
        return self::getUser($email, $passw_hash) != null;
    }

    public static function hasEmail($email)
    {
        $q = "SELECT id
            FROM  users
            WHERE email='$email'";
        $sh = DB::getConnection()->query($q);
        $sh->setFetchMode(PDO::FETCH_ASSOC);
        $user_data = $sh->fetch();
        return $user_data != null ? $user_data['id'] : false;
    }

    public function getAvatar(){
        return UserPic::getById($this->avatar);
    }

    public static function isValidRegistrationData(array $data)
    {
        if(!isset($data['fname']) || strlen($data['fname']) > self::MAX_LEN_FNAME) return false;
        if(!isset($data['sname']) || strlen($data['sname']) > self::MAX_LEN_SNAME) return false;
        if(!isset($data['email']) || strlen($data['email']) > self::MAX_LEN_EMAIL) return false;
        if(!isset($data['password']) || strlen($data['password']) > self::MAX_LEN_PASSWORD) return false;
        if(!isset($data['password-check']) && $data['password-check'] == $data['password']) return false;
        if(!isset($data['locale']) || strlen($data['locale']) > self::MAX_LEN_LOCALE) return false;

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL))
            return false;

        return true;
    }

    public function getParam($pname)
    {
        return $this->params[$pname];
    }

    public function getPermissions()
    {
        return $this->getParam('permissions');
    }

    public function setParam($pname, $pvalue)
    {
        $this->params[$pname] = $pvalue;
    }

    public function hasPermission($pname)
    {
        return in_array($pname, $this->getPermissions());
    }

    public function addPermission($permission)
    {
        if($this->hasPermission($permission))
            return;
        array_push($this->getPermissions(), $permission);

    }

    public function removePermission($permission)
    {
        $i = array_search($permission, $this->getPermissions());
        if ($i === false)
            return;
        unset($this->params['permissions'][$i]);

        //normalizing array indexes
        $this->setParam('permissions', array_values($this->params['permissions']));
    }

    public function isGuest()
    {
        return $this->hasPermission('guest');
    }

    public function unsetGuest()
    {
        $this->removePermission('guest');
    }

    public static function isValidLoginData(array $data)
    {
        if(!isset($data['email']) || strlen($data['email']) > self::MAX_LEN_EMAIL)
            return false;

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL))
            return false;

        if(!isset($data['password']) || strlen($data['password']) > self::MAX_LEN_PASSWORD)
            return false;

        return true;
    }

    public function toString()
    {
        return 'User: '.$this->fname.
        ' '.$this->sname.
        ' '.$this->email.
        '  locale='.$this->locale.
        ' avatar: '.($this->avatar==1?'default':'custom');
    }

    public function save()
    {
        if (!is_string($this->params)) {
            $this->params = json_encode($this->params);
        }
        $ses = DB::getConnection()->prepare(
            "REPLACE INTO ".self::TABLE_NAME." (fname, sname, email, salt, password, avatar, locale, params)
            values (:fname, :sname, :email, :salt, :password, :avatar, :locale, :params)");
        return $ses->execute((array)$this);
    }
}