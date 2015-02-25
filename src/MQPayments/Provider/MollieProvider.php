<?php
/**
 * MQPayments
 * Copyright (c) 2015 Milq Media.
 *
 * @author      Johan Kuijt <johan@milq.nl>
 * @copyright   2015 Milq Media.
 * @license     http://www.opensource.org/licenses/mit-license.php  MIT License
 * @link        http://milq.nl
 */
 
namespace MQPayments\Provider;

use Mollie_API_Client;
use MQPayments\Exception\RuntimeException;

class MollieProvider implements ProviderInterface
{
	private $apiKey;
	
	private $paymentConfig;
	
	public function setConfig(array $config, \MQPayments\Service\ProviderConfig $paymentConfig) {
		
		while(list($key, $val) = each($config)) {
			
			if(property_exists($this, $key))
				$this->{$key} = $val;
		}
	
		$this->paymentConfig = $paymentConfig;	
	}
	
	public function getPaymentConfig() {
		return $this->paymentConfig;
	}
	
	public function getMollie() {
		
		if(!$this->apiKey)
			throw new RuntimeException('Mollie API Key is missing');
			
		$mollie = new Mollie_API_Client;
		$mollie->setApiKey($this->apiKey);
		
		return $mollie;
	}
	
	public function getIdealIssuers() {
		
		$mollie = $this->getMollie();
	
		$issuers = $mollie->issuers->all();

		return $issuers;
	}
}