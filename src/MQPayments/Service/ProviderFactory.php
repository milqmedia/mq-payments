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
 
namespace MQPayments\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ProviderFactory implements FactoryInterface
{
	private $paymentProvider;
	
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config 		= $serviceLocator->get('config');
        $config 		= $config['mq-payments'];
        $providerConfig = new ProviderConfig;

        if (array_key_exists('default_method', $config)) {
            $providerConfig->setDefaultMethod($config['default_method']);
        }
        if (array_key_exists('methods', $config)) {
            $providerConfig->setPaymentMethods($config['methods']);
        }
        if (array_key_exists('order_description', $config)) {
            $providerConfig->setOrderDescription($config['order_description']);
        }
		
		$providerId = $config['provider'];
		
		if($this->paymentProvider != null)
			$providerId = $this->paymentProvider;
					
        $providerManager = $serviceLocator->get('MQPayments\Provider\ProviderManager');
        $provider = $providerManager->get($providerId);

        $provider->setConfig($config['provider_config']);
        $provider->setPaymentConfig($providerConfig);  
        
        return $provider;
    }
    
    public function setPaymentProvider($providerId) {
	    
	    $this->paymentProvider = $providerId;
    }
}