<?php

return array(
	'view_helpers' => array(  
        'invokables' => array(  
            'payments' => 'MQPayments\View\Helper\Payments',
        ),
    ),
	'service_manager' => array(
	    'invokables' => array(
            'MQPayments\Provider\ProviderManager' => 'MQPayments\Provider\ProviderManager',
        ),
        'factories'  => array(
            'MQPayments\Service\Provider' => 'MQPayments\Service\ProviderFactory',
        ),
    ),
	'mq-payments' => array(
		'methods' => array(
			'ideal'			=> true,
			'creditcard' 	=> true,	
			'paypal' 		=> false,
			'sofort' 		=> false,
			'mistercash'	=> false,
		),
		'default_method'	=> 'ideal',
		'provider'			=> 'mollie',
		'order_description'	=> 'order #%d',
		'provider_config'	=>	array(
			'apiKey'	=> '',	
		),
	),
);