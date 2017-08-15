<?php

/**
 * Created by PhpStorm.
 * User: max
 * Date: 05.08.17
 * Time: 9:28
 */
class UserModel extends Model
{

    /**
     * Initialize entity
     *
     * @param array $data
     * @return mixed
     */
    public static function init($data = array())
    {
        $user = new User(
            $data['id'],
            $data['telegram_id'],
            $data['first_name'],
            $data['last_name'],
            $data['telegram_language'],
            $data['date_of_birth'],
            $data['alias'],
            $data['city']
        );

        return $user;
    }

    /**
     * Get entity by id
     * 
     * @param $id
     * @return mixed|null
     * @throws Exception
     */
    public static function getById($id)
    {
        $connection = Database::connect();

        try {
            $stmt = $connection->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->bindParam(1, $id);

            $result = $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        if ($result) {
            $data = $stmt->fetch();
            $user = self::init($data);

            return $user;
        }
        
        return null;
    }

    /**
     * Get entity by some parameter and parameter`s value
     * 
     * @param $parameter
     * @param $value
     * @return array|null
     * @throws Exception
     */
    public static function getBy($parameter, $value){
        $connection = Database::connect();

        try {
            $stmt = $connection->prepare("SELECT * FROM users WHERE ? = ?");
            $stmt->bindParam(1, $parameter);
            $stmt->bindParam(2, $value);

            $result = $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        if ($result) {
            $data = $stmt->fetchAll();
            
            $users = array();
            
            foreach ($data as $user){
                array_push($users, self::init($user));     
            }

            return $users;
        }

        return null;
    }

    /**
     * Get all entities from database
     * 
     * @return array|null
     * @throws Exception
     */
    public static function all()
    {
        $connection = Database::connect();
        
        try {
            $result = $connection->query("SELECT * FROM users");
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
        
        if ($result) {
            $data = $result->fetchAll();

            $users = array();
            
            foreach ($data as $user) {
                array_push($users, self::init($user));
            }
            
            return $users;
        }
        
        return null;
    }

    public static function register($telegram_id, $first_name, $last_name){
        $connection = Database::connect();
        
        try {
            $stmt = $connection->prepare("INSERT INTO users (telegram_id, first_name, last_name) VALUES (?, ?, ?)");
            $stmt->bindParam(1, $telegram_id);
            $stmt->bindParam(2, $first_name);
            $stmt->bindParam(3, $last_name);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Bad registration", $e->getCode());
        }
    }

    public static function getUserLanguage($telegram_id){
        $connection = Database::connect();

        try{
            $stmt = $connection->prepare("SELECT language_name FROM users LEFT JOIN languges ON users.telegram_language = languages.id WHERE user.telegram_id = ?");
            $stmt->bindParam(1, $telegram_id);
            $result = $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        if ($result) {
            $data = $stmt->fetch();
            return $data['language_name'];
        }
    }
}