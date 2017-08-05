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
    public function seeding(PDO $connection){
        $connection->query("INSERT IGNORE INTO languges (language_name) VALUES 
                                        ('Russian'),
                                        ('English')
                                       ");
        $connection->query("INSERT IGNORE INTO commands (command_name) VALUES 
                                        ('Undefined'),
                                        ('start'), 
                                        ('help'),
                                        ('random'),
                                        ('migrate_up'),
                                        ('migrate_down'),
                                        ('seed'),
                                        ('users')
                                       ");
    }
}