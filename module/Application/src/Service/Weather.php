<?php

namespace Volley\Services;

use Zend\Http\Client;
use Zend\Json\Json;
use Zend\Json\Decoder;

/**
*
*/
class Weather
{
    protected $_key  = null;
    protected $_long = null;
    protected $_lat  = null;
    protected $_date = null;

    public function __construct($apiKey)
    {
        $this->_key = $apiKey;
    }

    public function get()
    {
        $client   = new Client();
        $url = 'https://api.forecast.io/forecast/' . $this->_key . '/' . $this->_lat . ',' . $this->_long;
        if ($this->_date) $url .= ',' . $this->_date;

        $client->setUri($url);
        $client->setMethod('GET');
        $client->setParameterGet([
            'units' => 'ca',
            'lang'  => 'fr',
        ]);
        $result   = $client->send();
        $response = Decoder::decode($result->getBody(), Json::TYPE_OBJECT);

        return $response;
    }

    public function setLong($long)
    {
        $this->_long = $long;
    }

    public function setLat($lat)
    {
        $this->_lat = $lat;
    }

    public function setDate($date)
    {
        $this->_date = $date;
    }
}
