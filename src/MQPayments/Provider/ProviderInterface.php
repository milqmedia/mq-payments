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

interface ProviderInterface
{
	public function setConfig(array $config);
	
	public function setPaymentConfig(\MQPayments\Service\ProviderConfig $paymentConfig);
	
	public function getPaymentConfig();
	
	public function createPayment($amount, $description, $redirectUrl);
}