<?php

/**
 * Created by PhpStorm.
 * User: max
 * Date: 20.08.17
 * Time: 15:17
 */
class Update
{
    private $id;
    private $id_user;
    private $message_id;
    private $text_of_message;
    private $created_at;

    /**
     * Update constructor.
     * @param $id
     * @param $id_user
     * @param $message_id
     * @param $text_of_message
     * @param $created_at
     */
    public function __construct($id, $id_user, $message_id, $text_of_message, $created_at)
    {
        $this->id = $id;
        $this->id_user = $id_user;
        $this->message_id = $message_id;
        $this->text_of_message = $text_of_message;
        $this->created_at = $created_at;
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
    public function getIdUser()
    {
        return $this->id_user;
    }

    /**
     * @param mixed $id_user
     */
    public function setIdUser($id_user)
    {
        $this->id_user = $id_user;
    }

    /**
     * @return mixed
     */
    public function getMessageId()
    {
        return $this->message_id;
    }

    /**
     * @param mixed $message_id
     */
    public function setMessageId($message_id)
    {
        $this->message_id = $message_id;
    }

    /**
     * @return mixed
     */
    public function getTextOfMessage()
    {
        return $this->text_of_message;
    }

    /**
     * @param mixed $text_of_message
     */
    public function setTextOfMessage($text_of_message)
    {
        $this->text_of_message = $text_of_message;
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

    
}