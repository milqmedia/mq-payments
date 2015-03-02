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

class AbstractProvider 
{
	public function getCache() {

		$cache   = \Zend\Cache\StorageFactory::factory(array(
		    'adapter' => array(
		        'name' => 'filesystem'
		    ),
		    'plugins' => array(
		        // Don't throw exceptions on cache errors
		        'exception_handler' => array(
		            'throw_exceptions' => false
		        ),
		    )
		));
		
		return $cache;
	}
}