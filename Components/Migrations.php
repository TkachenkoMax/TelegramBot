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
                                            city BLOB,
                                            created_at TIMESTAMP,
                                            PRIMARY KEY (id),
                                            FOREIGN KEY (telegram_language) REFERENCES languages(id) 
                                            ON DELETE SET NULL 
                                        ) ENGINE=INNODB,
                                        CHARACTER SET utf8 COLLATE utf8_general_ci");
        
        $connection->query("CREATE TABLE IF NOT EXISTS updates (
                                            id INT(11) NOT NULL AUTO_INCREMENT,
                                            id_user INT(11),
                                            message_id INT(50),
                                            text_of_message TEXT,
                                            created_at TIMESTAMP,
                                            PRIMARY KEY (id),
											foreign key (id_user) references users (id)
											on delete set null
                                        ) ENGINE=INNODB,
                                        CHARACTER SET utf8 COLLATE utf8_general_ci");
        
        $connection->query("CREATE TABLE IF NOT EXISTS admins (
                                            id INT(11) NOT NULL AUTO_INCREMENT,
                                            id_user INT(11),
                                            created_at TIMESTAMP,
                                            deleted_at DATETIME NULL,
                                            PRIMARY KEY (id),
                                            foreign key (id_user) references users (id)
                                            on delete set null
                                        ) ENGINE=INNODB
                                        CHARACTER SET utf8 COLLATE utf8_general_ci");

        $connection->query("CREATE TABLE IF NOT EXISTS instagram_accounts (
                                            id INT(11) NOT NULL AUTO_INCREMENT,
                                            id_user INT (11),
                                            login VARCHAR(255),
                                            password VARCHAR (255),
                                            PRIMARY KEY (id), 
                                            FOREIGN KEY (id_user) REFERENCES users(id) 
                                            ON DELETE SET NULL
                                        ) ENGINE=INNODB
                                        CHARACTER SET utf8 COLLATE utf8_general_ci");
    }

    /**
     * Delete basic tables from database
     * 
     * @param PDO $connection
     */
    public static function down(PDO $connection)
    {
        $connection->query("ALTER TABLE users
                                      DROP FOREIGN KEY users_ibfk_1");
        $connection->query("ALTER TABLE admins
                                      DROP FOREIGN KEY admins_ibfk_1");
        $connection->query("ALTER TABLE updates
                                      DROP FOREIGN KEY updates_ibfk_1");
        $connection->query("SET FOREIGN_KEY_CHECKS = 0");
        $connection->query("DROP TABLE IF EXISTS users");
        $connection->query("DROP TABLE IF EXISTS languages");
        $connection->query("DROP TABLE IF EXISTS updates");
        $connection->query("DROP TABLE IF EXISTS admins");
        $connection->query("SET FOREIGN_KEY_CHECKS = 1");
    }
}