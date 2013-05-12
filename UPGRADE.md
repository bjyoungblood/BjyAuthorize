## Upgrade to [1.2.6](https://github.com/bjyoungblood/BjyAuthorize/issues?milestone=4&page=1&state=closed)

 * The column `default` in table `user_role` was renamed to `is_default` to avoid problems with the colliding
   SQL keyword `DEFAULT` [#113](https://github.com/bjyoungblood/BjyAuthorize/pull/113)
   [#104](https://github.com/bjyoungblood/BjyAuthorize/pull/104)
 * Methods `BjyAuthorize\Controller\Plugin\IsAllowed#getAuthorizeService`,
   `BjyAuthorize\Controller\Plugin\IsAllowed#setAuthorizeService`,
   `BjyAuthorize\View\Helper\IsAllowed#getAuthorizeService`,
   `BjyAuthorize\View\Helper\IsAllowed#setAuthorizeService` were removed
   [#127](https://github.com/bjyoungblood/BjyAuthorize/pull/127)
   [#94](https://github.com/bjyoungblood/BjyAuthorize/pull/94)
   
## Upgrade to [1.2.0](https://github.com/bjyoungblood/BjyAuthorize/issues?milestone=1&page=1&state=closed)

 * Zend Developer Tools integration
 * Better Doctrine ORM support
 * Compatibility with any Doctrine ObjectManager (MongoDB ODM, PHPCR ODM, OrientDB ODM, etc.)
 * The new `BjyAuthorize\View\RedirectionStrategy` to redirect on unauthorized access

Following changes are required to upgrade:

 * The `BjyAuthorize\Provider\Role\Doctrine` provider was removed, use the
   `BjyAuthorize\Provider\Role\ObjectRepositoryProvider` as described in the
   [documentation](https://github.com/bjyoungblood/BjyAuthorize/blob/master/docs/doctrine.md) instead
 * The `BjyAuthorize\Provider\Identity\AuthenticationDoctrineEntity` provider was removed, use the
   simpler `BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider` instead
 * The `BjyAuthorize\Provider\Identity\ZfcUserDoctrine` provider was removed, use the
   simpler `BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider` instead
 * Following methods were removed from the `BjyAuthorize\Provider\Identity\ProviderInterface`:
    * `getDefaultRole`
    * `setDefaultRole`

Users who cannot upgrade now should lock their version of BjyAuthorize to `1.1.*`.
