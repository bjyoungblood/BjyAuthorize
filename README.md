BjyAuthorize
============

Composer/Packagist Users
========================

Please note the name of this project's package has changed to bjyoungblood/bjy-authorize
in order to match composer/packagist's new naming conventions. Please update your composer.json
to use the new package name.

Goal
----
This module is designed provide a facade for Zend\Acl that will ease
its usage with modules and applications. By default, it provides simple
setup via config files or by using Zend\Db. This module also comes with
out-of-the-box support for and integration with ZfcUser.

Requirements
------------
* [Zend Framework 2](https://github.com/zendframework/zf2) (latest master)
* [ZfcBase](https://github.com/ZF-Commons/ZfcBase) (latest master)
* [ZfcUser](https://github.com/ZF-Commons/ZfcUser) (latest master)

Installation
------------
### (1) Installation

Choose one of the two available installation methods:

#### Composer

Currently this medthod of installation is not working.

#### Git Submodule

1. Follow the [ZfcUser](https://github.com/ZF-Commons/ZfcUser) installation instructions to install that module and it's dependencies.

2. Clone this project into your `./vendor/` directory
```
cd vendor;
git clone git://github.com/bjyoungblood/BjyAuthorize.git;
```

###  (2) Configuration

1. Ensure that this module and it's dependencies are enabled in your `application.config.php` file in the following order:
    * ZfcBase
    * ZfcUser
    * BjyAuthorize
3. Import the SQL schema located in `./vendor/BjyAuthorize/data/schema.sql`.
4. Copy `./vendor/BjyAuthorize/config/module.config.php` to
   `./config/autoload/module.bjyauthorize.global.php`.
5. Fill in the required configuration variable values in  `./config/autoload/module.bjyauthorize.global.php`

Here is an annotated sample configuration file:
```php
<?php

return array(
    'bjyauthorize' => array(

        // set the 'guest' role as default (must be defined in a role provider)
        'default_role' => 'guest',

        /* this module uses a meta-role that inherits from any roles that should
         * be applied to the active user. the identity provider tells us which
         * roles the "identity role" should inherit from.
         *
         * for ZfcUser, this will be your default identity provider
         */
        'identity_provider' => 'BjyAuthorize\Provider\Identity\ZfcUserZendDb',

        /* role providers simply provide a list of roles that should be inserted
         * into the Zend\Acl instance. the module comes with two providers, one
         * to specify roles in a config file and one to load roles using a
         * Zend\Db adapter.
         */
        'role_providers' => array(

            /* here, 'guest' and 'user are defined as top-level roles, with
             * 'admin' inheriting from user
             */
            'BjyAuthorize\Provider\Role\Config' => array(
                'guest' => array(),
                'user'  => array('children' => array(
                    'admin' => array(),
                )),
            ),

            // this will load roles from the user_role table in a database
            // format: user_role(role_id(varchar), parent(varchar))
            'BjyAuthorize\Provider\Role\ZendDb' => array(
                'table'             => 'user_role',
                'role_id_field'     => 'role_id',
                'parent_role_field' => 'parent',
            ),
        ),

        // resource providers provide a list of resources that will be tracked
        // in the ACL. like roles, they can be hierarchical
        'resource_providers' => array(
            'BjyAuthorize\Provider\Resource\Config' => array(
                'pants' => array(),
            ),
        ),

        /* rules can be specified here with the format:
         * array(roles (array), resource, [privilege (array|string), assertion])
         * assertions will be loaded using the service manager and must implement
         * Zend\Acl\Assertion\AssertionInterface.
         * *if you use assertions, define them using the service manager!*
         */
        'rule_providers' => array(
            'BjyAuthorize\Provider\Rule\Config' => array(
                'allow' => array(
                    // allow guests and users (and admins, through inheritance)
                    // the "wear" privilege on the resource "pants"
                    array(array('guest', 'user'), 'pants', 'wear')
                ),

                // Don't mix allow/deny rules if you are using role inheritance.
                // There are some weird bugs.
                'deny' => array(
                    // ...
                ),
            ),
        ),

        /* Currently, only controller and route guards exist
         */
        'guards' => array(
            /* If this guard is specified here (i.e. it is enabled), it will block
             * access to all controllers and actions unless they are specified here.
             * You may omit the 'action' index to allow access to the entire controller
             */
            'BjyAuthorize\Guard\Controller' => array(
                array('controller' => 'index', 'action' => 'index', 'roles' => array('guest','user')),
                array('controller' => 'index', 'action' => 'stuff', 'roles' => array('user')),
                array('controller' => 'zfcuser', 'roles' => array()),
                // Below is the default index action used by the [ZendSkeletonApplication](https://github.com/zendframework/ZendSkeletonApplication)
                // array('controller' => 'Application\Controller\Index', 'roles' => array('guest', 'user')),
            ),

            /* If this guard is specified here (i.e. it is enabled), it will block
             * access to all routes unless they are specified here.
             */
            'BjyAuthorize\Guard\Route' => array(
                array('route' => 'zfcuser', 'roles' => array('user')),
                array('route' => 'zfcuser/logout', 'roles' => array('user')),
                array('route' => 'zfcuser/login', 'roles' => array('guest')),
                array('route' => 'zfcuser/register', 'roles' => array('guest')),
                // Below is the default index action used by the [ZendSkeletonApplication](https://github.com/zendframework/ZendSkeletonApplication)
                array('route' => 'home', 'roles' => array('guest', 'user')),
            ),
        ),
    ),
);
```

Helpers and Plugins
-------------------
There are view helpers and controller plugins registered for this module.
In either a controller or a view script, you can call
```$this->isAllowed($resource[, $privilege])```, which will query the ACL
using the currently authenticated (or default) user's roles.

License
-------
Released under the MIT License.  See file LICENSE included with the source
code for this project for a copy of the licensing terms.
