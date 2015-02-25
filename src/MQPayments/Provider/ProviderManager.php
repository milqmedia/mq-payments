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

use Zend\ServiceManager\AbstractPluginManager;

class ProviderManager extends AbstractPluginManager
{
    protected $invokableClasses = array(
       'mollie'    => 'MQPayments\Provider\MollieProvider',
    );

    public function validatePlugin($plugin)
    {
        if ($plugin instanceof ProviderInterface) {
            return;
        }

        throw new MQPayments\Exception\InvalidProviderException(sprintf(
            'Provider of type %s is invalid; ',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}