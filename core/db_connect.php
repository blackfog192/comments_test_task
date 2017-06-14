<?php

/**
*  Singleton pattern
*  Connection to database
*/

class DataBase {
    private static $mysqli;

    final private function __construct() {}

    public static function getInstance() {
      if (!is_object(self::$mysqli)) {
        $pathParams = ROOT .'/config/database.php';
        $params = include($pathParams);
        self::$mysqli = new mysqli($params['host'], $params['user'], $params['password'], $params['dbname']);
      }
      return self::$mysqli;
    }

    private function __destruct() {
      if (self::$mysqli) self::$mysqli->close();
    }

    private function __clone() {}
}

?>