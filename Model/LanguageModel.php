<?php

/**
 * Created by PhpStorm.
 * User: max
 * Date: 20.08.17
 * Time: 11:58
 */
class LanguageModel extends Model
{

    /**
     * Initialize language
     *
     * @param array $data
     * @return mixed
     */
    public static function init($data = array())
    {
        $language = new Language(
            $data['id'],
            $data['language_name']
        );

        return $language;
    }

    /**
     * Find language by id
     * 
     * @param $id
     * @return mixed|null
     * @throws Exception
     */
    public static function getById($id)
    {
        $connection = Database::connect();

        try {
            $stmt = $connection->prepare("SELECT * FROM languages WHERE id = ?");
            $stmt->bindParam(1, $id);

            $result = $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        if ($result) {
            $data = $stmt->fetch();
            $language = self::init($data);

            return $language;
        }

        return null;
    }

    /**
     * Get all languages from database
     * 
     * @return array|null
     * @throws Exception
     */
    public static function all()
    {
        $connection = Database::connect();

        try {
            $result = $connection->query("SELECT * FROM languages");
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        if ($result) {
            $data = $result->fetchAll();

            $languages = array();

            foreach ($data as $language) {
                array_push($languages, self::init($language));
            }

            return $languages;
        }

        return null;
    }
}