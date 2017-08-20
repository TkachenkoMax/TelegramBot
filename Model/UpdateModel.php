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
            $data['user_id'],
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
    
    public static function saveUpdate(array $data){
        $connection = Database::connect();

        try {
            $stmt = $connection->prepare("INSERT INTO updates (user_id, message_id, text) VALUES (?, ?, ?)");
            $stmt->bindParam(1, $data['user_id']);
            $stmt->bindParam(2, $data['message_id']);
            $stmt->bindParam(3, $data['text']);

            $result = $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
        return $result;
    }
}