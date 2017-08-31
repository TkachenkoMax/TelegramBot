<?php

/**
 * Created by PhpStorm.
 * User: max
 * Date: 20.08.17
 * Time: 15:20
 */
class UpdateModel extends Model
{

    /**
     * Initialize entity
     *
     * @param array $data
     * @return mixed
     */
    public static function init($data = array())
    {
        $update = new Update(
            $data['id'],
            $data['$id_user'],
            $data['message_id'],
            $data['text_of_message'],
            $data['created_at']
        );

        return $update;
    }

    /**
     * Find update by id
     * 
     * @param $id
     * @return mixed|null
     * @throws Exception
     */
    public static function getById($id)
    {
        $connection = Database::connect();

        try {
            $stmt = $connection->prepare("SELECT * FROM updates WHERE id = ?");
            $stmt->bindParam(1, $id);

            $result = $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        if ($result) {
            $data = $stmt->fetch();
            $update = self::init($data);

            return $update;
        }

        return null;
    }

    /**
     * Get all updates from database
     * 
     * @return array|null
     * @throws Exception
     */
    public static function all()
    {
        $connection = Database::connect();

        try {
            $result = $connection->query("SELECT * FROM updates");
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        if ($result) {
            $data = $result->fetchAll();

            $updates = array();

            foreach ($data as $update) {
                array_push($updates, self::init($update));
            }

            return $updates;
        }

        return null;
    }

    /**
     * Save to database every incoming update to bot from users
     *
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public static function saveUpdate(array $data){
        $connection = Database::connect();

        try {
            $stmt = $connection->prepare("INSERT INTO updates (id_user, message_id, text_of_message) VALUES (?, ?, ?)");
            $stmt->bindParam(1, $data['id_user']);
            $stmt->bindParam(2, $data['message_id']);
            $stmt->bindParam(3, $data['text_of_message']);

            $result = $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
        return $result;
    }

    public static function getUpdatesWithUsers(){
        $connection = Database::connect();

        try {
            $result = $connection->query("SELECT users.telegram_id, users.first_name, users.last_name, updates.message_id, updates.text_of_message, updates.created_at
                                            FROM updates INNER JOIN users ON users.id=updates.id_user ORDER BY updates.created_at DESC");
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        return $result->fetchAll();
    }
}