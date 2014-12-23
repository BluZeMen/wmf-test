<?php

require_once 'conf.php';
require_once 'dao/user.php';


class Auth
{
    const OK = 0;
    const EMAIL_ALREADY_EXIST = 1;
    const NO_SUCH_USER = 2;
    const BAD_REGISTRATION_DATA = 3;
    const BAD_REGISTRATION_IMAGE = 4;
    const BAD_LOGIN_DATA = 5;
    const INTERNAL_ERROR = 6;
    const SESSION_ERROR = 7;
    const DB_ERROR = 8;
    const DEFAULT_AVATAR = 1;

    static function registerNew(array $data)
    {
        if(User::hasEmail($data['email']))
            return self::EMAIL_ALREADY_EXIST;

        if(!User::isValidRegistrationData($data))
            return self::BAD_REGISTRATION_DATA;

        $a = self::DEFAULT_AVATAR;
        if($data['avatar']) {
            $a = $data['avatar'];
            if(!UserPic::isValidNewData($a))
                return self::BAD_REGISTRATION_IMAGE;

            $p = new UserPic();
            $p->description = 'avatar';
            $p->bin_data = $a['bin_data'];
            $p->filename = $a['filename'];
            $p->filesize = $a['filesize'];
            $p->filetype = $a['filetype'];
            if($p->save()){
                $a = UserPic::getLastId();
            }
        }

        $u = new User();
        $u->fname = filter_var($data['fname'], FILTER_SANITIZE_STRING);
        $u->sname = filter_var($data['sname'], FILTER_SANITIZE_STRING);
        $u->email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
        $u->salt = self::generateSalt();
        $u->password = self::makeHash($u->salt, $data['password']);
        $u->params = json_encode(array('permissions' => array('default')));
        $u->locale = filter_var($data['locale'], FILTER_SANITIZE_STRING);
        $u->avatar = $a;

        return $u->save() ? self::OK : self::DB_ERROR;
    }

    public static function login(array $data)
    {
        if(!User::isValidLoginData($data))
            return self::BAD_REGISTRATION_DATA;

        $u = User::getUser($data['email']);
        if(!$u)
            return self::NO_SUCH_USER;

        $lp = self::makeHash($u->salt, $data['password']);
        if($u->password != $lp)
            return self::NO_SUCH_USER;

        if(!self::initSession())
            return self::INTERNAL_ERROR;

        self::setLoggedUser($u);

        return self::OK;
    }

    public static function logout()
    {
        if(self::isLogged() && !self::closeSession())
            return Auth::SESSION_ERROR;

        return Auth::OK;
    }

    public static function isLogged(){
        return self::getLoggedUser() != null;
    }

    public static function getLoggedUser()
    {
        if (!self::initSession()) {
            return null;
        }
        return isset($_SESSION['user']) ? $_SESSION['user'] : null;
    }

    private static function initSession()
    {
        if (!self::isSessionOpened()) {
            if(!session_start())
                return false;
            $_SESSION['is_opened'] = true;
        }
        return true;
    }

    private static function isSessionOpened()
    {
        return isset($_SESSION['is_opened']) && $_SESSION['is_opened'];
    }

    private static function setLoggedUser(User $user)
    {
        $_SESSION['user'] = $user;
    }

    private static function closeSession()
    {
        if(!self::isSessionOpened())
            return true;
        unset($_SESSION['is_opened']);
        return session_destroy();
    }

    private static function makeHash($salt, $pass_orig)
    {
        return crypt($pass_orig, $salt);
    }

    private static function generateSalt()
    {
        return '$2a$10$'.substr(str_replace('+', '.', base64_encode(pack('N4', mt_rand(), mt_rand(), mt_rand(),mt_rand()))), 0, 22) . '$';
    }

}