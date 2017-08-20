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
        $language = LanguageModel::getById($data['telegram_language']);
        
        $is_admin = false;

        test_file(AdminModel::getByUserId($data['id']));

        if(!is_null(AdminModel::getByUserId($data['id']))) {
            $is_admin = true;
        }
        
        $user = new User(
            $data['id'],
            $data['telegram_id'],
            $data['first_name'],
            $data['last_name'],
            $language,
            $is_admin,
            $data['created_at'],
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
     * Get user language
     * 
     * @param $telegram_id
     * @return null|mixed
     * @throws Exception
     */
    public static function getUserLanguage($telegram_id){
        $connection = Database::connect();

        try{
            $stmt = $connection->prepare("SELECT language_name FROM users LEFT JOIN languages ON users.telegram_language = languages.id WHERE users.telegram_id = ?");
            $stmt->bindParam(1, $telegram_id);
            $result = $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        if ($result) {
            $data = $stmt->fetch();
            return $data['language_name'];
        }
        
        return null;
    }
}