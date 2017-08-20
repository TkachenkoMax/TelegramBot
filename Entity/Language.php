<?php

/**
 * Created by PhpStorm.
 * User: max
 * Date: 20.08.17
 * Time: 11:41
 */
class Language
{
    private $id;
    private $language_name;
    
    public function __construct($id, $language_name)
    {
        $this->id = $id;
        $this->language_name = $language_name;
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
    public function getLanguageName()
    {
        return $this->language_name;
    }

    /**
     * @param mixed $language_name
     */
    public function setLanguageName($language_name)
    {
        $this->language_name = $language_name;
    }
    
    
}