<?php

return array(
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
		),
		'default_method'	=> 'ideal',
		'provider'			=> 'mollie',
		'provider_config'	=>	array(
			'api_key'	=> '',	
		),
	),
);