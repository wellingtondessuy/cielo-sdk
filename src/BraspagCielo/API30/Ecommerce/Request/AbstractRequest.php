<?php

namespace BraspagCielo\API30\Ecommerce\Request;

use BraspagCielo\API30\Environment;
use BraspagCielo\API30\Merchant;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractSaleRequest
 *
 * @package Cielo\API30\Ecommerce\Request
 */
abstract class AbstractRequest
{

    protected $merchant;
    protected $environment;
    protected $logger;

	/**
	 * AbstractSaleRequest constructor.
	 *
	 * @param Merchant $merchant
     * @param Environment $environment
	 * @param LoggerInterface|null $logger
	 */
    public function __construct(Merchant $merchant, Environment $environment, LoggerInterface $logger = null)
    {
        $this->merchant    = $merchant;
        $this->environment = $environment;
        $this->logger      = $logger;
    }

    /**
     * @param $param
     *
     * @return mixed
     */
    public abstract function execute($param);

    private function braspagAuthToken()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->environment->getBraspagOauth2ServerURL() . 'oauth2/token',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic ' . base64_encode($this->merchant->getId() . ':' . $this->merchant->getKey()),
            'Content-Type: application/x-www-form-urlencoded'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $responseArr = [];

        if (!empty($response)) {
            $responseArr = json_decode($response, true);
        }

        if (empty($responseArr)) {
            return '';
        }

        return $responseArr['access_token'];
    }

    /**
     * @param                        $method
     * @param                        $url
     * @param \JsonSerializable|null $content
     *
     * @return mixed
     *
     * @throws \BraspagCielo\API30\Ecommerce\Request\CieloRequestException
     * @throws \RuntimeException
     */
    protected function sendRequest($method, $url, \JsonSerializable $content = null)
    {
        $braspagAuthToken = $this->braspagAuthToken();

        $headers = [
            'Accept: application/json',
            'Accept-Encoding: gzip',
            'User-Agent: CieloEcommerce/3.0 PHP SDK',
            // 'MerchantId: ' . $this->merchant->getId(),
            // 'MerchantKey: ' . $this->merchant->getKey(),
            'RequestId: ' . uniqid(),
            'Authorization: Bearer ' . $braspagAuthToken
        ];

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

        switch ($method) {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, true);
                break;
            default:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        }

        if ($content !== null) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($content));

            $headers[] = 'Content-Type: application/json';
        } else {
            $headers[] = 'Content-Length: 0';
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        if ($this->logger !== null) {
            $this->logger->debug('Requisição', [
                    sprintf('%s %s', $method, $url),
                    $headers,
                    json_decode(preg_replace('/("cardnumber"):"([^"]{6})[^"]+([^"]{4})"/i', '$1:"$2******$3"', json_encode($content)))
                ]
            );
        }

        $response   = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($this->logger !== null) {
            $this->logger->debug('Resposta', [
                sprintf('Código de status: %s', $statusCode),
                json_decode($response)
            ]);
        }

        if (curl_errno($curl)) {
            $message = sprintf('cURL error[%s]: %s', curl_errno($curl), curl_error($curl));

            $this->logger->error($message);

            throw new \RuntimeException($message);
        }

        curl_close($curl);

        return $this->readResponse($statusCode, $response);
    }

    /**
     * @param $statusCode
     * @param $responseBody
     *
     * @return mixed
     *
     * @throws CieloRequestException
     */
    protected function readResponse($statusCode, $responseBody)
    {
        $unserialized = null;

        switch ($statusCode) {
            case 200:
            case 201:
                $unserialized = $this->unserialize($responseBody);
                break;
            case 400:
                $exception = null;
                $response  = json_decode($responseBody);

                foreach ($response as $error) {
                    $cieloError = new CieloError($error->Message, $error->Code);
                    $exception  = new CieloRequestException('Request Error', $statusCode, $exception);
                    $exception->setCieloError($cieloError);
                }

                throw $exception;
            case 404:
                throw new CieloRequestException('Resource not found', 404, null);
            default:
                throw new CieloRequestException('Unknown status', $statusCode);
        }

        return $unserialized;
    }

    /**
     * @param $json
     *
     * @return mixed
     */
    protected abstract function unserialize($json);
}
