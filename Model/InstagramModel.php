<?php

/**
 * Created by PhpStorm.
 * User: max
 * Date: 27.08.17
 * Time: 11:28
 */
class InstagramModel extends Model
{

    /**
     * Initialize entity
     *
     * @param array $data
     * @return mixed
     */
    public static function init($data = array())
    {
        $instagram_account = new InstagramAccount(
            $data['login'],
            $data['password']
        );

        return $instagram_account;
    }

    /**
     * Find entity by id
     *
     * @param $id
     * @throws Exception
     * @return mixed|null
     */
    public static function getById($id)
    {
        $connection = Database::connect();

        try {
            $stmt = $connection->prepare("SELECT * FROM instagram_accounts WHERE id = ?");
            $stmt->bindParam(1, $id);

            $result = $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        if ($result) {
            $data = $stmt->fetch();
            $instagram_account = self::init($data);

            return $instagram_account;
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
            $result = $connection->query("SELECT * FROM instagram_accounts");
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        if ($result) {
            $data = $result->fetchAll();

            $instagram_accounts = array();

            foreach ($instagram_accounts as $instagram_account) {
                array_push($instagram_accounts, self::init($instagram_account));
            }

            return $instagram_accounts;
        }

        return null;
    }
}