<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Guard;

use Zend\EventManager\ListenerAggregateInterface;

/**
 * Interface for generic guard listeners
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
interface GuardInterface extends ListenerAggregateInterface
{
}
