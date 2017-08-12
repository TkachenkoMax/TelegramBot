<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 02.08.17
 * Time: 14:26
 */
class Migrations{
    /**
     * Create basic tables in database
     * 
     * @param PDO $connection
     */
    public static function up(PDO $connection)
    {
        $connection->query("CREATE TABLE IF NOT EXISTS languages (
                                            id INT(11) NOT NULL AUTO_INCREMENT,
                                            language_name VARCHAR (50),
                                            PRIMARY KEY (id)
                                        ) ENGINE=INNODB
                                        CHARACTER SET utf8 COLLATE utf8_general_ci");
        
        $connection->query("CREATE TABLE IF NOT EXISTS users (
                                            id INT(11) NOT NULL AUTO_INCREMENT,
                                            telegram_id VARCHAR(30) NOT NULL,
                                            first_name VARCHAR(100),
                                            last_name VARCHAR (100),
                                            telegram_language INT(11),
                                            date_of_birth DATETIME,
                                            alias VARCHAR(100),
                                            city VARCHAR(100),
                                            PRIMARY KEY (id),
                                            /*FOREIGN KEY (telegram_language) REFERENCES Languages(id) 
                                            ON DELETE SET NULL */
                                        ) ENGINE=INNODB,
                                        CHARACTER SET utf8 COLLATE utf8_general_ci");
        
        $connection->query("CREATE TABLE IF NOT EXISTS commands (
                                            id INT(11) NOT NULL AUTO_INCREMENT
                                            command_name VARCHAR(50),
                                            PRIMARY KEY (id)
                                        ) ENGINE=INNODB
                                        CHARACTER SET utf8 COLLATE utf8_general_ci");
        
        $connection->query("CREATE TABLE IF NOT EXISTS commands_users (
                                            id INT(11) NOT NULL AUTO_INCREMENT
                                            id_user INT(11),
                                            id_command INT(11),
                                            command_parameters JSON NULLABLE, 
                                            created_at TIMESTAMP,
                                            PRIMARY KEY (id),
                                           /* FOREIGN KEY (id_user) REFERENCES users (id)
                                            ON DELETE SET NULL,
                                            FOREIGN KEY (id_comand) REFERENCES commands (id)
                                            ON DELETE SET NULL*/
                                        ) ENGINE=INNODB
                                        CHARACTER SET utf8 COLLATE uft8_general_ci");
    }

    /**
     * Delete basic tables from database
     * 
     * @param PDO $connection
     */
    public static function down(PDO $connection)
    {
        /*$connection->query("ALTER TABLE users
                                      DROP FOREIGN KEY fk_users_languages_telegram_language");
        $connection->query("ALTER TABLE commands_users
                                      DROP FOREIGN KEY fk_commands_users_users_id_user");
        $connection->query("ALTER TABLE commands_users
                                      DROP FOREIGN KEY fk_commands_users_commands_id_command");*/
        $connection->query("DROP TABLE IF EXISTS users");
        $connection->query("DROP TABLE IF EXISTS languages");
        $connection->query("DROP TABLE IF EXISTS commands_users");
        $connection->query("DROP TABLE IF EXISTS commands");
    }
}