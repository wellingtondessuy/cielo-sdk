<?php

namespace BraspagCielo\API30\Ecommerce;

/**
 * Class Environment
 *
 * @package Cielo\API30\Ecommerce
 */
class Environment implements \BraspagCielo\API30\Environment
{
    private $api;

    private $apiQuery;

    private $apiSplit;

    private $braspagOauth2Server;

    /**
     * Environment constructor.
     *
     * @param $api
     * @param $apiQuery
     */
    private function __construct($api, $apiQuery, $apiSplit, $braspagOauth2Server)
    {
        $this->api                 = $api;
        $this->apiQuery            = $apiQuery;
        $this->apiSplit            = $apiSplit;
        $this->braspagOauth2Server = $braspagOauth2Server;
    }

    /**
     * @return Environment
     */
    public static function sandbox()
    {
        $api                 = 'https://apisandbox.cieloecommerce.cielo.com.br/';
        $apiQuery            = 'https://apiquerysandbox.cieloecommerce.cielo.com.br/';
        $apiSplit            = 'https://splitsandbox.braspag.com.br/';
        $braspagOauth2Server = 'https://authsandbox.braspag.com.br/';

        return new Environment($api, $apiQuery, $apiSplit,$braspagOauth2Server);
    }

    /**
     * @return Environment
     */
    public static function production()
    {
        $api                 = 'https://api.cieloecommerce.cielo.com.br/';
        $apiQuery            = 'https://apiquery.cieloecommerce.cielo.com.br/';
        $apiSplit            = 'https://split.braspag.com.br/';
        $braspagOauth2Server = 'https://auth.braspag.com.br/';

        return new Environment($api, $apiQuery, $apiSplit,$braspagOauth2Server);
    }

    /**
     * Gets the environment's Api URL
     *
     * @return string the Api URL
     */
    public function getApiUrl()
    {
        return $this->api;
    }

    /**
     * Gets the environment's Api Query URL
     *
     * @return string Api Query URL
     */
    public function getApiQueryURL()
    {
        return $this->apiQuery;
    }

    /**
     * Gets the environment's Api Split URL
     *
     * @return string Api Split URL
     */
    public function getApiSplitURL()
    {
        return $this->apiSplit;
    }

    /**
     * Gets the environment's Api BraspagOauth2Server URL
     *
     * @return string Api BraspagOauth2Server URL
     */
    public function getBraspagOauth2ServerURL()
    {
        return $this->braspagOauth2Server;
    }
}
