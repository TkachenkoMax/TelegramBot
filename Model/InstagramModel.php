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

            foreach ($data as $instagram_account) {
                array_push($instagram_accounts, self::init($instagram_account));
            }

            return $instagram_accounts;
        }

        return null;
    }

    /**
     * Set Instagram account login
     * 
     * @param $login
     * @param $id
     * @throws Exception
     */
    public static function setLogin($login, $id) {
        $connection = Database::connect();

        try {
            $stmt = $connection->prepare("SELECT * FROM instagram_accounts WHERE id_user = ?");
            $stmt->bindParam(1, $id);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        /*try {
            $stmt = $connection->prepare("SELECT * FROM instagram_accounts WHERE id_user = ?");
            $stmt->bindParam(1, $id);
            $stmt->execute();
            
            if(count($stmt->fetchAll()) == 0) {
                $query = $connection->prepare("INSERT INTO instagram_accounts (id_user, login) VALUES (?, ?)");
                $query->bindParam(1, $id);
                $query->bindParam(1, $login);
                $query->execute();
            } else {
                $query = $connection->prepare("UPDATE instagram_accounts SET login = ? WHERE id_user = ?");
                $query->bindParam(1, $login);
                $query->bindParam(2, $id);
                $query->execute();
            }
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }*/
    }

    /**
     * Set Instagram account password
     *
     * @param $password
     * @param $id
     * @throws Exception
     */
    public static function setPassword($password, $id) {
        $connection = Database::connect();

        try {
            $stmt = $connection->prepare("SELECT * FROM instagram_accounts WHERE id_user = ?");
            $stmt->bindParam(1, $id);
            $stmt->execute();

            if(count($stmt->fetchAll()) == 0) {
                $query = $connection->prepare("INSERT INTO instagram_accounts (id_user, password) VALUES (?, ?)");
                $query->bindParam(1, $id);
                $query->bindParam(1, $password);
                $query->execute();
            } else {
                $query = $connection->prepare("UPDATE instagram_accounts SET password = ? WHERE id_user = ?");
                $query->bindParam(1, $password);
                $query->bindParam(2, $id);
                $query->execute();
            }
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}