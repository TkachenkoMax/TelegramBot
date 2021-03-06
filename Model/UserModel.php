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
        $language = is_null($data['telegram_language']) ? null : LanguageModel::getById($data['telegram_language']);
        
        $is_admin = false;

        if(is_array(AdminModel::getByUserId($data['id']))) {
            $is_admin = true;
        }

        $city = unserialize($data['city']);

        $user = new User(
            $data['id'],
            $data['telegram_id'],
            $data['first_name'],
            $data['last_name'],
            $language,
            $is_admin,
            $data['created_at'],
            (is_null($data['date_of_birth']) ? null : new DateTime($data['date_of_birth'])),
            $data['alias'],
            $city
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
            $stmt = $connection->prepare("SELECT * FROM users WHERE $parameter = ?");
            $stmt->bindParam(1, $value);

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

    /**
     * Register new user
     * 
     * @param $telegram_id
     * @param $first_name
     * @param $last_name
     * @throws Exception
     */
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

    /**
     * Set user's language
     * 
     * @param $telegram_id
     * @param $language_id
     * @throws Exception
     */
    public static function setUserLanguage($telegram_id, $language_id){
        $connection = Database::connect();

        try{
            $stmt = $connection->prepare("UPDATE users SET telegram_language = ? WHERE telegram_id = ?");
            $stmt->bindParam(1, $language_id);
            $stmt->bindParam(2, $telegram_id);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Set user's alias
     *
     * @param $telegram_id
     * @param $alias
     * @throws Exception
     */
    public static function setUserAlias($telegram_id, $alias){
        $connection = Database::connect();

        try{
            $stmt = $connection->prepare("UPDATE users SET alias = ? WHERE telegram_id = ?");
            $stmt->bindParam(1, $alias);
            $stmt->bindParam(2, $telegram_id);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Set user's city
     *
     * @param $telegram_id
     * @param City $city
     * @throws Exception
     */
    public static function setCity($telegram_id, City $city){
        $city_ser = serialize($city);

        $connection = Database::connect();

        try{
            $stmt = $connection->prepare("UPDATE users SET city = ? WHERE telegram_id = ?");
            $stmt->bindParam(1, $city_ser);
            $stmt->bindParam(2, $telegram_id);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Set user's date of birth
     * 
     * @param $telegram_id
     * @param $date
     * @throws Exception
     */
    public static function setUserDateOfBirth($telegram_id, $date){
        $connection = Database::connect();

        try{
            $stmt = $connection->prepare("UPDATE users SET date_of_birth = ? WHERE telegram_id = ?");
            $stmt->bindParam(1, $date);
            $stmt->bindParam(2, $telegram_id);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Return array of users with birthday = $date
     * 
     * @param DateTime $date
     * @return array
     * @throws Exception
     */
    public static function getUsersWithBirthdayToday(DateTime $date){
        try {
            $users = self::all();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        $birthday_users = array();

        foreach ($users as $user) {
            if ($date->format("m-d") === $user->getDateOfBirth()->format("m-d"))
                array_push($birthday_users, $user);
        }

        return $birthday_users;
    } 
}