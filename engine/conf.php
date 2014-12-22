<?php


header("Content-Type: text/html; charset=utf-8");
define('BASE_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR);

class PROJ_CONF
{
    const PROJ_NAME = 'Сайт агенства "Такие вот дела"';
    const PROJ_DOMAIN = '';
    const TIME_STORE_FORMAT = 'H:i:s j.m.Y e';
    const TIME_ZONE = 'Europe/Moscow';
    const EMAIL_ADMIN = 'vladistian@gmail.com';
    const DEFAULT_LOCALE = 'ru-ru';
    const VIEWS_PATH = '/view';
    const STYLES_PATH = '/styles';
}

class DB_CONF
{
    const USER_NAME = 'wmf-test';
    const USER_PASS = 'hello-wmf!';
    const HOST = 'localhost';
    const NAME = 'wmf-test';
}

class LOG_CONF
{
    const MODE = 'DEBUG';
    const SIZE_LIMIT = 2;// k_bytes
    const CRITICAL_LOG = 'critical.log';
    const ERROR_LOG = 'errors.log';
    const WARNING_LOG = 'warning.log';
    const ALL_LOG = 'all.log';
}