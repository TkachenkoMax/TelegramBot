<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 05.08.17
 * Time: 9:26
 */

class Database {

    /**
     * Connect to Heroku database function using configuration file
     * 
     * @return PDO connection to database
     * @throws Exception 
     */
    public static function connect()
    {
        $config = include __ROOT__ . "/Config/database.php";
        $dsn = "mysql:host=" . $config['host'] . ";dbname=" . $config['dbname'] . ";charset=" . $config['charset'] . "";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $connection = new PDO($dsn, $config['username'], $config['password'], $opt);
        } catch (PDOException $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode());
        }
        
        return $connection;
    }
}