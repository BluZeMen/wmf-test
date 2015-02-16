<?php

require_once 'conf.php';
require_once 'dao/user.php';
require_once 'email.php';

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
    const LIMIT_IS_EXCEEDED = 20;
    const DEFAULT_AVATAR = 1;

    const LIMIT_OF_REQUESTS_CNT_PER_SESSION = 5;
    const LIMIT_OF_REGISTRATIONS_CNT_PER_SESSION = 3;

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
        $u->params = json_encode(array('permissions' => array('guest')));
        $u->locale = filter_var($data['locale'], FILTER_SANITIZE_STRING);
        $u->avatar = $a;

        if(!$u->save())
            return self::DB_ERROR;

        $rs = self::requestRegistrationConfirmation($u);

        return $rs;
    }

    public static function login(array $data)
    {
        if(!User::isValidLoginData($data))
            return self::BAD_REGISTRATION_DATA;

        $u = User::getUser($data['email']);
        if(!$u)
            return self::NO_SUCH_USER;

        if($u->isGuest())
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

    public static function isLogged()
    {
        return self::getLoggedUser() != null;
    }

    public static function getLoggedUser()
    {
        if (!self::initSession()) {
            return null;
        }

        $u = isset($_SESSION['user']) ? $_SESSION['user'] : null;
        if($u instanceof User){
            return $u;
        }
        if($u == null) {
            return null;
        }
        $_SESSION['user'] = User::getUser($u);
        return $u;
    }

    private static function unsetOneTimeAccessToken()
    {
        if(!self::initSession())
            return self::INTERNAL_ERROR;
        unset($_SESSION['ota-token']);
        unset($_SESSION['ota-user']);
        unset($_SESSION['ota-are-set']);
        unset($_SESSION['ota-are-set-per-session']);
    }

    private static function registerOneTimeAccessToken($user, $token)
    {
        if(!self::initSession())
            return self::INTERNAL_ERROR;

        $_SESSION['ota-token'] = $token;
        $_SESSION['ota-user'] = $user->email;
        if(isset($_SESSION['ota-are-set'])){
            if(!isset($_SESSION['ota-set-per-session'])){
                $_SESSION['ota-set-per-session'] = 0;
            }
            $_SESSION['ota-set-per-session']++;
        }else{
            $_SESSION['ota-are-set'] = true;
        }
    }

    private static function isLimitOfRecoverRequestsExceed()
    {
        return self::getLoginByOTATAttempts() > self::LIMIT_OF_REQUESTS_CNT_PER_SESSION;
    }

    private static function isLimitOfRegistrationRequestsExceed()
    {
        return self::getLoginByOTATAttempts() > self::LIMIT_OF_REGISTRATIONS_CNT_PER_SESSION;
    }

    private static function getLoginByOTATAttempts()
    {
        if(!isset($_SESSION['ota-set-per-session'])){
            $_SESSION['ota-set-per-session'] = 0;
        }
        return $_SESSION['ota-set-per-session'];
    }

    private static function getRegisterRequestsOTAT()
    {
        return self::getLoginByOTATAttempts();
    }

    public static function loginByOTAT($otat)
    {
        $otat = filter_var($otat, FILTER_SANITIZE_STRING);
        if(!self::initSession())
            return self::INTERNAL_ERROR;

        if($_SESSION['ota-token'] !== $otat)
            return self::NO_SUCH_USER;

        $u = User::getUser($_SESSION['ota-user']);
        self::setLoggedUser($u);
        if($u->isGuest()){
            $u->unsetGuest();
            $u->save();
        }
        self::unsetOneTimeAccessToken();
        return self::OK;
    }

    public static function requestRegistrationConfirmation($user)
    {
        if(!self::initSession()){
            return self::INTERNAL_ERROR;
        }

        if(self::isLimitOfRegistrationRequestsExceed()){
            Log::get('register')->info('Register requests count is exceeding limit '
                .self::LIMIT_OF_REQUESTS_CNT_PER_SESSION.'. times='
                .self::getRegisterRequestsOTAT());
            return self::LIMIT_IS_EXCEEDED;
        }

        if (!filter_var($user->email, FILTER_VALIDATE_EMAIL))
            return self::BAD_LOGIN_DATA;

        $token = self::cycleMD5(self::cycleMD5($user->email, 2) + mt_rand());
        $send = Email::sendConfirmRegistration($user, $token);
        if(!$send)
            return self::INTERNAL_ERROR;

        self::registerOneTimeAccessToken($user, $token);

        return self::OK;
    }

    public static function requestRecoverPassword($userData)
    {
        if(!self::initSession()){
            return self::INTERNAL_ERROR;
        }

        if(self::isLimitOfRecoverRequestsExceed()){
            Log::get('login')->info('Login by OTAT is exceeding limit '
                .self::LIMIT_OF_REQUESTS_CNT_PER_SESSION.'. times='
                .self::getLoginByOTATAttempts());
            return self::LIMIT_IS_EXCEEDED;
        }

        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL))
            return self::BAD_LOGIN_DATA;

        $u = User::getUser($userData['email']);
        if(!$u)
            return self::NO_SUCH_USER;

        $token = self::cycleMD5($u->email);
        $send = Email::sendRecoverPassword($u, $token);
        if(!$send)
            return self::INTERNAL_ERROR;

        self::registerOneTimeAccessToken($u, $token);

        return self::OK;
    }

    public static function setNewPassword($user, $rawNewPassword)
    {
        $user->salt = self::generateSalt();
        $user->password = self::makeHash($user->salt, $rawNewPassword);
    }

    private static function initSession()
    {
        if ( self::isSessionOpened()) return true;

        ini_set('session.cookie_lifetime', PROJ_CONF::SESSION_LIFETIME);
        ini_set('session.gc_maxlifetime', PROJ_CONF::SESSION_LIFETIME);
        //ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] .'../sessions');

        if(!session_start())
            return false;
        $c = setcookie(session_name(), session_id(), time()+PROJ_CONF::SESSION_LIFETIME);
        if(!$c)
            return false;

        $_SESSION['is_opened'] = true;

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
        session_unset();
        return session_destroy();
    }

    private static function cycleMD5($src, $cycles = 64)
    {
        for($i = 0; $i < $cycles; $i++){
            $src = md5($src);
        }
        return $src;
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