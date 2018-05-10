<?php
define('DB_CHAR', 'utf8');
Define('DB_HOST', 'localhost');
Define('DB_NAME', 'parsers');
Define('DB_USER', 'root');
Define('DB_PASS', 'root');

class Db {

    protected static $instance = null;

    private function __construct() {
        //
    }

    private function __clone() {
        //
    }

    public static function getInstance() {
        if (self::$instance === null) {
            $opt = array(
                #PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHAR,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => TRUE,
            );
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHAR;
            self::$instance = new PDO($dsn, DB_USER, DB_PASS, $opt);
        }
        return self::$instance;
    }
}