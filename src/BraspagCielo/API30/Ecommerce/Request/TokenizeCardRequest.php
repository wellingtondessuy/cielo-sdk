<?php

namespace BraspagCielo\API30\Ecommerce\Request;

use BraspagCielo\API30\Ecommerce\CreditCard;
use BraspagCielo\API30\Ecommerce\Environment;
use BraspagCielo\API30\Merchant;
use Psr\Log\LoggerInterface;

/**
 * Class CreateCardTokenRequestHandler
 *
 * @package AppBundle\Handler\Cielo
 */
class TokenizeCardRequest extends AbstractRequest
{
	/**
	 * CreateCardTokenRequestHandler constructor.
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
     * @inheritdoc
     */
    public function execute($param)
    {
        $url = $this->environment->getApiUrl() . '1/card/';

        return $this->sendRequest('POST', $url, $param);
    }

    /**
     * @inheritdoc
     */
    protected function unserialize($json)
    {
        return CreditCard::fromJson($json);
    }
}
