<?php

namespace BraspagCielo\API30\Ecommerce\Request;

use BraspagCielo\API30\Ecommerce\Sale;
use BraspagCielo\API30\Environment;
use BraspagCielo\API30\Merchant;
use Psr\Log\LoggerInterface;

/**
 * Class QuerySaleRequest
 *
 * @package Cielo\API30\Ecommerce\Request
 */
class QuerySaleRequest extends AbstractRequest
{

	/**
	 * QuerySaleRequest constructor.
	 *
	 * @param Merchant $merchant
	 * @param Environment $environment
	 * @param LoggerInterface|null $logger
	 */
    public function __construct(Merchant $merchant, Environment $environment, LoggerInterface $logger = null)
    {
        parent::__construct($merchant, $environment, $logger);
    }

    /**
     * @param $paymentId
     *
     * @return null
     * @throws \BraspagCielo\API30\Ecommerce\Request\CieloRequestException
     * @throws \RuntimeException
     */
    public function execute($paymentId)
    {
        $url = $this->environment->getApiQueryURL() . '1/sales/' . $paymentId;

        return $this->sendRequest('GET', $url);
    }

    /**
     * @param $json
     *
     * @return Sale
     */
    protected function unserialize($json)
    {
        return Sale::fromJson($json);
    }
}
