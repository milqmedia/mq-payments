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

use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;
use MQPayments\Exception\RuntimeException;

class PaypalProvider extends AbstractProvider implements ProviderInterface
{
	private $payPalClientId;
	private $payPalClientSecret;
	
	private $paymentConfig;
	
	private $paymentMethod;
	private $idealIssuer;
	private $metadata = array();
	private $locale = 'nl';
	private $webhook;
	private $developmentMode;
	
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
	
	public function getPayPal() {
		
		if(!$this->payPalClientId)
			throw new RuntimeException('Paypal clientId is missing');
			
		if(!$this->payPalClientSecret)
			throw new RuntimeException('Paypal clientSecret is missing');
			
		$apiContext = new ApiContext(
	        new OAuthTokenCredential(
	            $this->payPalClientId,
	            $this->payPalClientSecret
	        )
	    );

	    $apiContext->setConfig(
	        array(
	            'mode' => ($this->developmentMode) ? 'sandbox' : 'live',
	            'log.LogEnabled' => true,
	            'log.FileName' => '/tmp/PayPal.log',
	            'log.LogLevel' => ($this->developmentMode) ? 'DEBUG' : 'FINE',
	            'cache.enabled' => true,
	            'cache.FileName' => 'data/paypal.cache'
	        )
	    );

	    return $apiContext;
   	}
   	
   	public function getPaymentMethodId($paymentMethod) {
   		
   		switch($paymentMethod) {
			
			default:
			case 'paypal_rest':
				return 'paypal';
			break;
		}
   	}
	
	public function createPayment($amount, $description, $redirectUrl) {
		
		$apiContext = $this->getPayPal();
		
		$payer = new Payer();
		$payer->setPaymentMethod("paypal");
		
		$amountObject = new Amount();
		$amountObject->setCurrency("USD")
		    	->setTotal($amount);
		
		$transaction = new Transaction();
		$transaction->setAmount($amountObject)
		    ->setDescription($description);

		$redirectUrls = new RedirectUrls();
		$redirectUrls->setReturnUrl($redirectUrl . "?success=true")
		    ->setCancelUrl($redirectUrl . "?success=false");
		
		$payment = new Payment();
		$payment->setIntent("sale")
		    ->setPayer($payer)
		    ->setRedirectUrls($redirectUrls)
		    ->setTransactions(array($transaction));
		
		$data = array(
	        "amount"      	=> $amount,
			"description" 	=> $description,
			"redirectUrl" 	=> $redirectUrl,
			"method"		=> $this->paymentMethod,
			"metadata"		=> $this->metadata,
			"webhookUrl"	=> $this->webhook,
			"locale"		=> $this->locale,
		);
		
		$payment->create($apiContext);
		
		$approvalUrl = $payment->getApprovalLink();

		return (object) array('id' => $payment->getId(), 'url' => $approvalUrl, 'provider' => 'paypal');
	}
	
	public function getPayment($paymentId, $payerId) {
		
		$apiContext = $this->getPayPal();
		
    	$payment = Payment::get($paymentId, $apiContext);

		$execution = new PaymentExecution();
		$execution->setPayerId($payerId);
		
		try {
	        $result = $payment->execute($execution, $apiContext);
			$payment = Payment::get($paymentId, $apiContext);
						
			return $payment;
			
		} catch (Exception $ex) {
			
			return false;	
		}		 
	}
}
