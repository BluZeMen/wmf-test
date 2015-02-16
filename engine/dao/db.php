<?php
/**
 * Created by PhpStorm.
 * User: bzm
 * Date: 05.12.14
 * Time: 16:39
 */

require_once 'conf.php';

class DB
{
    // Connection to DB
    private static $conn = null;

    static function connect()
    {
        if (self::$conn) return self::$conn;
        try {
            $host = DB_CONF::HOST;
            $dbname = DB_CONF::NAME;
            $user = DB_CONF::USER_NAME;
            $pass = DB_CONF::USER_PASS;
            $options = array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            );

            self::$conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass, $options);

            //self::$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            //self::$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        } catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }
        return self::$conn;
    }

    static function close()
    {
        $conn = null;
    }

    static function getConnection()
    {
        if(self::$conn == null){
            self::connect();
        }
        return self::$conn;
    }

    static function isTableExist($tableName)
    {

        return self::getConnection()->exec("SHOW TABLES LIKE '$tableName'") > 0;
    }

    const LEN_MYSQL_MEDIUMTEXT = 16777215;

}
