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
 
namespace MQPayments\Exception;

use InvalidArgumentException as StdInvalidArgumentException;

class InvalidArgumentException
    extends StdInvalidArgumentException
    implements ExceptionInterface
{
}