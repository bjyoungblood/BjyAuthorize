<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Service;

/**
 * Factory responsible of a set of {@see \BjyAuthorize\Provider\Role\ProviderInterface}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class RoleProvidersServiceFactory extends BaseProvidersServiceFactory
{
    const PROVIDER_SETTING = 'role_providers';
}
