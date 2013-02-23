Description
----------------
If you wish to use doctrine entities (ORM) or documents (ODM), user and role can now do it using the following steps

Firstly you need to be using ZfcUserDoctrineORM or ZfcUserDoctrineMongoODM depending on your database.

### Creating the entities ###

Next you will need to create your User & Role classes in your project entity/document folder.

Your User class must implement `BjyAuthorize\Provider\Role\ProviderInterface` and `ZfcUser\Entity\UserInterface`

Your Role class must implement `BjyAuthorize\Acl\HierarchicalRoleInterface`

Examples are provided as `BjyAuthorize/data/User.php.dist` and `BjyAuthorized/data/Role.php.dist`

For ORM, once you have created your entities you can set up the database with the following command

    ./vendor/bin/doctrine-module orm:schema-tool:create

### Configuring ZfcUser and BjyAuthorize ###

Next in your ZfcUser configuration specify your user class for the **user_entity_class** setting

Then in you bjyauthorize settings include the following 2 options:

```php
// Set to use the Doctrine identity provider, works when the auth identity
// is an entity or document as explained above
'identity_provider'     => 'BjyAuthorize\Provider\Identity\AuthenticationDoctrine',

// Set to use the Doctrine role provider
'role_providers'        => array(
    'BjyAuthorize\Provider\Role\Doctrine' => array(
        'role_entity_class' => 'FQCN TO YOUR ROLE ENTITY/DOCUMENT CLASS',
     ),
),
```

And you're configured and ready to go. All you need to do is make sure your default role is added to the database.
