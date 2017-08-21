<?php

/**
 * Created by PhpStorm.
 * User: max
 * Date: 21.08.17
 * Time: 19:52
 */
class City
{
    private $city;
    private $country;
    private $longitude;
    private $latitude;

    /**
     * City constructor.
     * @param $city
     * @param $country
     * @param $longitude
     * @param $latitude
     */
    public function __construct($city, $country, $longitude, $latitude)
    {
        $this->city = $city;
        $this->country = $country;
        $this->longitude = $longitude;
        $this->latitude = $latitude;
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

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }
}