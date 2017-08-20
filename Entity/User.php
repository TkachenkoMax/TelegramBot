<?php

/**
 * Created by PhpStorm.
 * User: max
 * Date: 05.08.17
 * Time: 9:27
 */
class User
{
    private $id;
    private $telegram_id;
    private $first_name;
    private $last_name;
    private $date_of_birth;
    private $telegram_language;
    private $alias;
    private $city;
    private $created_at;
    private $is_admin;

    /**
     * User constructor.
     * @param $id
     * @param $telegram_id
     * @param $first_name
     * @param $last_name
     * @param $telegram_language
     * @param $is_admin
     * @param $created_at
     * @param null $date_of_birth
     * @param null $alias
     * @param null $city
     */
    public function __construct($id, $telegram_id, $first_name, $last_name, $telegram_language, $is_admin, $created_at, $date_of_birth = null, $alias = null, $city = null)
    {
        $this->id = $id;
        $this->telegram_id = $telegram_id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->language = $telegram_language;
        $this->date_of_birth = $date_of_birth;
        $this->alias = $alias;
        $this->city = $city;
        $this->created_at = $created_at;
        $this->is_admin = $is_admin;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return mixed
     */
    public function getIsAdmin()
    {
        return $this->is_admin;
    }

    /**
     * @param mixed $is_admin
     */
    public function setIsAdmin($is_admin)
    {
        $this->is_admin = $is_admin;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTelegramId()
    {
        return $this->telegram_id;
    }

    /**
     * @param mixed $telegram_id
     */
    public function setTelegramId($telegram_id)
    {
        $this->telegram_id = $telegram_id;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param mixed $first_name
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param mixed $last_name
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
    }

    /**
     * @return mixed
     */
    public function getDateOfBirth()
    {
        return $this->date_of_birth;
    }

    /**
     * @param mixed $date_of_birth
     */
    public function setDateOfBirth($date_of_birth)
    {
        $this->date_of_birth = $date_of_birth;
    }

    /**
     * @return mixed
     */
    public function getTelegramLanguage()
    {
        return $this->telegram_language;
    }

    /**
     * @param mixed $telegram_language
     */
    public function setLanguage($telegram_language)
    {
        $this->telegram_language = $telegram_language;
    }

    /**
     * @return mixed
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param mixed $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }
}