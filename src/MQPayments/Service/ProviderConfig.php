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

class ProviderConfig
{	
	private $paymentMethods = array(
		'ideal' 		=> true, 
		'creditcard'	=> true, 
		'paypal'		=> false, 
		'sofort' 		=> false,
		'mistercash'	=> false,
		'paypal_rest'	=> true, 
	);
	
    /**
     * Default locale
     *
     * @var string
     */
    protected $defaultMethod = 'ideal';
    
    protected $orderDescription;
    

    public function getDefaultMethod()
    {
        return $this->defaultMethod;
    }

    public function setDefaultMethod($defaultMethod)
    {
        $this->defaultMethod = $defaultMethod;
        return $this;
    }
    
    public function setPaymentMethods(array $methods)
    {
		while(list($methodName, $methodValue) = each($methods)) {
			if(isset($this->paymentMethods[$methodName]))
				$this->paymentMethods[$methodName] = $methodValue;
		}
    }
    
    public function getPaymentMethods()
    {
        return $this->paymentMethods;
    }
    
    public function setOrderDescription($description) {
	    
	    $this->orderDescription = $description;
    }
    
    public function getOrderDescription() {
	    
	    return $this->orderDescription;
    }
    
    public function hasPaymentMethod($name)
    {
	    if(!isset($this->paymentMethods[$name]))
	    	return false;
	    		    	
        return ($this->paymentMethods[$name] === true);
    }    
}