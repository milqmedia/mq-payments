<?php
/**
 * MQPayments
 * Copyright (c) 2015 Milq Media.
 *
 * @author	  Johan Kuijt <johan@milq.nl>
 * @copyright   2015 Milq Media.
 * @license	 http://www.opensource.org/licenses/mit-license.php  MIT License
 * @link		http://milq.nl
 */

namespace MQPayments\Provider;

use Mollie_API_Client;
use Mollie_API_Object_Method;
use MQPayments\Exception\RuntimeException;

class MollieProvider extends AbstractProvider implements ProviderInterface
{
	private $apiKey;

	private $paymentConfig;

	private $paymentMethod;
	private $idealIssuer;
	private $metadata = array();
	private $locale = 'nl';
	private $webhook;

	public function setConfig(array $config) {

		while(list($key, $val) = each($config)) {

			if(property_exists($this, $key))
				$this->{$key} = $val;
		}
	}

	public function setPaymentConfig(\MQPayments\Service\ProviderConfig $paymentConfig) {

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

	public function getPaymentMethodId($method) {

		switch($method) {

			case 'ideal':
				return Mollie_API_Object_Method::IDEAL;
			break;
			case 'creditcard':
				return Mollie_API_Object_Method::CREDITCARD;
			break;
			case 'paypal':
				return Mollie_API_Object_Method::PAYPAL;
			break;
			case 'mistercash':
				return Mollie_API_Object_Method::MISTERCASH;
			break;
			case 'sofort':
				return Mollie_API_Object_Method::SOFORT;
			break;
			default:
				return Mollie_API_Object_Method::CREDITCARD;
		}
	}

	public function getIdealIssuers() {

		$cache = $this->getCache();

		$key	= 'ideal-issuers-' . $this->apiKey;
		$issuers = $cache->getItem($key, $success);

		if (!$success || empty($issuers)) {

			$mollie = $this->getMollie();
			$issuers = $mollie->issuers->all();

			$cache->setItem($key, serialize($issuers));
		} else {

			$issuers = unserialize($issuers);
		}

		return $issuers;
	}

	public function createPayment($amount, $description, $redirectUrl) {

		$mollie = $this->getMollie();

		$data = array(
			"amount"	  	=> $amount,
			"description" 	=> $description,
			"redirectUrl" 	=> $redirectUrl,
			'recurringType' => 'first',	   // important
			"method"		=> $this->paymentMethod,
			"metadata"		=> $this->metadata,
			"webhookUrl"	=> $this->webhook,
			"locale"		=> $this->locale,
		);

		if($this->paymentMethod == Mollie_API_Object_Method::IDEAL)
			$data['issuer'] = $this->idealIssuer;

		$payment = $mollie->payments->create($data);

		return (object) array('id' => $payment->id, 'url' => $payment->getPaymentUrl(), 'provider' => 'mollie');
	}

	public function createCustomer($email){

		$mollie = $this->getMollie();

		$customer = $mollie->customers->create([
			"email" => $email,
		]);

		return $customer;

	}

	public function hasValidMandate($customerId){

		$mollie = $this->getMollie();

		$mandates = $mollie->customers_mandates->withParentId($customerId)->all();

		foreach ($mandates->data as $mandate) {
			if ($mandate->status == 'valid') {
				return true;
			}
		}

		return false;
	}

	public function createRecurringPayment($customerId, $amount, $description, $redirectUrl, $startdate = null) {

		$mollie = $this->getMollie();

		if(!$this->hasValidMandate($customerId)){
			$data = array(
				'amount'	  	=> $amount,
				'customerId'	=> $customerId,
				'description' 	=> $description,
				'redirectUrl' 	=> $redirectUrl,
				'recurringType' => 'first',	   // important
				'metadata'		=> $this->metadata,
				'webhookUrl'	=> 'http://mollie.florian.ultrahook.com/',
				'locale'		=> $this->locale,
			);

			$payment = $mollie->payments->create($data);

			return (object) array('id' => $payment->id, 'url' => $payment->getPaymentUrl(), 'provider' => 'mollie');

		} else{
			$data = array(
				'amount'	  	=> $amount,
				'customerId'	=> $customerId,
				'description' 	=> $description,
				'redirectUrl' 	=> $redirectUrl,
				'recurringType' => 'recurring',	   // important
				'metadata'		=> $this->metadata,
				'webhookUrl'	=> 'http://mollie.florian.ultrahook.com/',
				'locale'		=> $this->locale,
			);

			$payment = $mollie->payments->create($data);

			var_dump($payment);

			return (object) array('id' => $payment->id, 'url' => $redirectUrl, 'provider' => 'mollie');
		}

	}


	public function getPayment($id) {

		$mollie = $this->getMollie();

		$payment = $mollie->payments->get($id);

		return $payment;
	}
}