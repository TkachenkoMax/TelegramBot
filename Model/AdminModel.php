<?php

/**
 * Created by PhpStorm.
 * User: max
 * Date: 20.08.17
 * Time: 15:22
 */
class AdminModel extends Model
{

    /**
     * Initialize entity
     *
     * @param array $data
     * @return mixed
     */
    public static function init($data = array())
    {
        return $data;
    }

    /**
     * Find admin by id
     * 
     * @param $id
     * @return mixed|null
     * @throws Exception
     */
    public static function getById($id)
    {
        $connection = Database::connect();

        try {
            $stmt = $connection->prepare("SELECT * FROM admins WHERE id = ?");
            $stmt->bindParam(1, $id);

            $result = $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        if ($result) {
            $data = $stmt->fetch();
            $admin = self::init($data);

            return $admin;
        }

        return null;
    }

    /**
     * Find admin by user id
     *
     * @param $id
     * @return mixed|null
     * @throws Exception
     */
    public static function getByUserId($id)
    {
        $connection = Database::connect();

        try {
            $stmt = $connection->prepare("SELECT * FROM admins WHERE id_user = ?");
            $stmt->bindParam(1, $id);

            $result = $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        if ($result) {
            $data = $stmt->fetch();
            $admin = self::init($data);

            return $admin;
        }

        return null;
    }

    /**
     * Get all admins' ids from database
     * 
     * @return array|null
     * @throws Exception
     */
    public static function all()
    {
        $connection = Database::connect();

        try {
            $result = $connection->query("SELECT * FROM admins");
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        if ($result) {
            $data = $result->fetchAll();

            $admins = array();

            foreach ($data as $admin) {
                array_push($admins, self::init($admin));
            }

            return $admins;
        }

        return null;
    }

    /**
     * Get all admins with information about them
     * 
     * @return array
     * @throws Exception
     */
    public static function getAdminsWithUsers(){
        $connection = Database::connect();

        try {
            $result = $connection->query("SELECT users.telegram_id, users.first_name, users.last_name, admins.created_at, admins.deleted_at
                                            FROM admins INNER JOIN users ON users.id=admins.id_user");
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        return $result->fetchAll();
    }

    /**
     * Get id of admin by telegram_id if he isn't deleted
     *
     * @param $telegram_id
     * @return array
     * @throws Exception
     */
    public static function getNotDeletedAdminById($telegram_id) {
        $connection = Database::connect();

        try {
            $result = $connection->prepare("SELECT admins.id, admins.deleted_at FROM admins INNER JOIN users ON users.id = admins.id_user 
                                            WHERE users.telegram_id=? AND isnull(admins.deleted_at)");
            $result->bindParam(1, $telegram_id);
            $result->execute();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        return $result->fetchAll();
    }

    /**
     * Delete admin
     *
     * @param $id
     * @throws Exception
     */
    public static function deleteAdmin($id){
        $connection = Database::connect();

        try {
            $result = $connection->prepare("UPDATE admins SET deleted_at = current_timestamp() WHERE id = ?");
            $result->bindParam(1, $id);
            $result->execute();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}