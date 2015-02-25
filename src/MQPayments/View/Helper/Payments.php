<?php
  
namespace MQPayments\View\Helper;  

use Zend\View\Helper\AbstractHelper;  
use Zend\ServiceManager\ServiceLocatorAwareInterface;  
use Zend\ServiceManager\ServiceLocatorInterface;  

class Payments extends AbstractHelper implements ServiceLocatorAwareInterface  
{  
	private $serviceLocator;
	
	public function __invoke() {
		
		return $this;
	}
	
	public function hasPaymentMethod($method) {

		$provider = $this->getServiceLocator()->getServiceLocator()->get('MQPayments\Service\Provider');
		$paymentConfig = $provider->getPaymentConfig();
		
		return $paymentConfig->hasPaymentMethod($method);
	}
	
	/** 
     * Set the service locator. 
     * 
     * @param ServiceLocatorInterface $serviceLocator 
     * @return CustomHelper 
     */  
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)  
    {  
        $this->serviceLocator = $serviceLocator;  
        return $this;  
    }  

    /** 
     * Get the service locator. 
     * 
     * @return \Zend\ServiceManager\ServiceLocatorInterface 
     */  
    public function getServiceLocator()  
    {  
        return $this->serviceLocator;  
    }  

}