<?php

namespace BraspagCielo\API30\Ecommerce\Request;

use BraspagCielo\API30\Ecommerce\Sale;
use BraspagCielo\API30\Environment;
use BraspagCielo\API30\Merchant;
use Psr\Log\LoggerInterface;

/**
 * Class CreateSaleRequest
 *
 * @package Cielo\API30\Ecommerce\Request
 */
class CreateSaleRequest extends AbstractRequest
{

	/**
	 * CreateSaleRequest constructor.
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
     * @param $sale
     *
     * @return null
     * @throws \BraspagCielo\API30\Ecommerce\Request\CieloRequestException
     * @throws \RuntimeException
     */
    public function execute($sale)
    {
        $url = $this->environment->getApiUrl() . '1/sales/';

        return $this->sendRequest('POST', $url, $sale);
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
