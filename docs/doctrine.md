# Using BjyAuthorize with Doctrine

If you wish to use Doctrine 2 ORM entities (ORM) or MongoDB ODM Documents (ODM), all you will need to do is
having your authentication identity implement either `Zend\Permissions\Acl\Role\RoleInterface` or
`BjyAuthorize\Provider\Role\ProviderInterface`.

## BjyAuthorize, ZfcUser and ZfcUserDoctrineORM

Here's some simple steps to do this specifically with `ZfcUserDoctrineORM`, though any authentication service
will work too:


### Installation

Install and enable `ZfcUser` and `ZfcUserDoctrineORM`:

```sh
php composer.phar require zf-commons/zfc-user-doctrine-orm:0.1.*
```

You will obviously need to enable all the involved modules

### Implement a `MyNamespace\User` and a `MyNamespace\Role` entities

Implement a `MyNamespace\User` and a `MyNamespace\Role` entity.
You can use the [`User.php.dist`](https://github.com/bjyoungblood/BjyAuthorize/blob/master/data/User.php.dist)
and [`Role.php.dist`](https://github.com/bjyoungblood/BjyAuthorize/blob/master/data/Role.php.dist) files as blueprint.

### Configuration

You will need to override the settings of `ZfcUserDoctrineORM` to use the entities you defined:

```php
return array(
    'doctrine' => array(
        'driver' => array(
            // overriding zfc-user-doctrine-orm's config
            'zfcuser_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => 'path/to/your/entities/dir',
            ),

            'orm_default' => array(
                'drivers' => array(
                    'MyNamespace' => 'zfcuser_entity',
                ),
            ),
        ),
    ),

    'zfcuser' => array(
        // telling ZfcUser to use our own class
        'user_entity_class'       => 'MyNamespace\User',
        // telling ZfcUserDoctrineORM to skip the entities it defines
        'enable_default_entities' => false,
    ),

    'bjyauthorize' => array(
        // Using the authentication identity provider, which basically reads the roles from the auth service's identity
        'identity_provider' => 'BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider',

        'role_providers'        => array(
            // using an object repository (entity repository) to load all roles into our ACL
            'BjyAuthorize\Provider\Role\ObjectRepositoryProvider' => array(
                'object_manager'    => 'doctrine.entity_manager.orm_default',
                'role_entity_class' => 'MyNamespace\Role',
             ),
        ),
    ),
);
```

This setup will simply check the current identity: if none is set, it will use the default role configured in the
`BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider`, otherwise it will try to extract the roles from
your user object.