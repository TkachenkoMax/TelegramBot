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

        $connection->query("INSERT IGNORE INTO users (telegram_id, first_name, last_name) VALUES 
                                        (382994855, 'Max', 'Tkachenko'),
                                        (294541056, 'Kostya', 'Dyachenko')
                                       ");

        $connection->query("INSERT IGNORE INTO admins (id_user) VALUES 
                                        (2)
                                       ");
    }
}