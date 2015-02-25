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
        
        $providerManager = $serviceLocator->get('MQPayments\Provider\ProviderManager');
        $provider = $providerManager->get($config['provider']);

        $provider->setConfig($config['provider_config'], $providerConfig);
          
        return $provider;
    }
}