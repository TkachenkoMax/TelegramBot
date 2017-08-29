<?php

/**
 * Created by PhpStorm.
 * User: max
 * Date: 05.08.17
 * Time: 12:36
 */
class Seeds
{
    /**
     * Insert basic values in tables
     *
     * @param PDO $connection
     */
    public static function seeding(PDO $connection){
        $connection->query("INSERT IGNORE INTO languages (language_name) VALUES 
                                        ('Русский'),
                                        ('English')
                                       ");
    }
}