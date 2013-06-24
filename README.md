# BjyAuthorize - Acl security for ZF2

[![Travis-CI Build Status](https://api.travis-ci.org/bjyoungblood/BjyAuthorize.png?branch=master)](https://travis-ci.org/bjyoungblood/BjyAuthorize) [![Coverage Status](https://coveralls.io/repos/bjyoungblood/BjyAuthorize/badge.png)](https://coveralls.io/r/bjyoungblood/BjyAuthorize) [![Dependency Status](https://www.versioneye.com/package/php--bjyoungblood--bjy-authorize/badge.png)](https://www.versioneye.com/package/php--bjyoungblood--bjy-authorize)

This module is designed provide a facade for `Zend\Permissions\Acl` that will
ease its usage with modules and applications. By default, it provides simple
setup via config files or by using `Zend\Db` or Doctrine ORM/ODM (via ZfcUserDoctrineORM).

## What does BjyAuthorize do?

BjyAuthorize adds event listeners to your application so that you have a "security" or "firewall" that disallows
unauthorized access to your controllers or routes.

This is what a normal `Zend\Mvc` application workflow would look like:

![Zend Mvc Application workflow](http://yuml.me/diagram/plain;/activity/%28start%29-%3E%28route%29%2C%20%28route%29-%3E%28get%20controller%29%2C%20%28get%20controller%29-%3E%28dispatch%29%2C%20%28dispatch%29-%3E%28end%29)

And here's how it would look like with BjyAuthorize enabled:

![Zend Mvc Application workflow with BjyAuthorize](http://yuml.me/diagram/plain;/activity/%28start%29-%3E%28route%29%2C%20%28route%29-%3E%3Ca%3E-no%20route%20guard%3E%28get%20controller%29%2C%20%3Ca%3E-%3E%28route%20guard%29%2C%20%28route%20guard%29-%3E%3Cb%3E-authorized%3E%28get%20controller%29%2C%20%3Cb%3Eunauthorized-%3E%28error%29%2C%20%28get%20controller%29-%3E%3Cc%3E-no%20controller%20guard%3E%28dispatch%29%2C%20%3Cc%3E-%3E%28controller%20guard%29%2C%20%28controller%20guard%29-%3E%3Cd%3E-authorized%3E%28dispatch%29%2C%20%3Cd%3Eunauthorized-%3E%28error%29%2C%20%28error%29-%3E%28end%29%2C%20%28dispatch%29-%3E%28end%29)

## Requirements

 * [Zend Framework 2](https://github.com/zendframework/zf2)
 * [ZfcUser](https://github.com/ZF-Commons/ZfcUser) (optional)
 * [ZfcUserDoctrineORM](https://github.com/ZF-Commons/ZfcUserDoctrineORM) (optional)

## Installation

### Composer

The suggested installation method is via [composer](http://getcomposer.org/):

```sh
php composer.phar require bjyoungblood/bjy-authorize:1.2.*
php composer.phar require zf-commons/zfc-user:0.1.*
```

### Git Submodule

 1. Install [ZfcUser](https://github.com/ZF-Commons/ZfcUser) (follow its installation docs)
 2. Clone this project into your `./vendor/` directory

    ```sh
    cd vendor
    git clone git://github.com/bjyoungblood/BjyAuthorize.git
    ```

## Configuration

Following steps apply if you want to use `ZfcUser` with `Zend\Db`. If you want to use Doctrine ORM/ODM, you should
also check the [doctrine documentation](https://github.com/bjyoungblood/BjyAuthorize/blob/master/docs/doctrine.md).

 1. Ensure that following modules are enabled in your `application.config.php` file in the this order:
     * `ZfcBase`
     * `ZfcUser`
     * `BjyAuthorize`
 3. Import the SQL schema located in `./vendor/BjyAuthorize/data/schema.sql`.
 4. Create a `./config/autoload/bjyauthorize.global.php` file and fill it with
    configuration variable values as described in the following annotated example.

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

        /* If you only have a default role and an authenticated role, you can
         * use the 'AuthenticationIdentityProvider' to allow/restrict access
         * with the guards based on the state 'logged in' and 'not logged in'.
         *
         * 'default_role'       => 'guest',         // not authenticated
         * 'authenticated_role' => 'user',          // authenticated
         * 'identity_provider'  => 'BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider',
         */

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

            // this will load roles from the 'BjyAuthorize\Provider\Role\Doctrine'
            // service
            'BjyAuthorize\Provider\Role\Doctrine' => array(),
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
         *
         * Consider enabling either the controller or the route guard depending on your needs.
         */
        'guards' => array(
            /* If this guard is specified here (i.e. it is enabled), it will block
             * access to all controllers and actions unless they are specified here.
             * You may omit the 'action' index to allow access to the entire controller
             */
            'BjyAuthorize\Guard\Controller' => array(
                array('controller' => 'index', 'action' => 'index', 'roles' => array('guest','user')),
                array('controller' => 'index', 'action' => 'stuff', 'roles' => array('user')),
                // You can also specify an array of actions or an array of controllers (or both)
                // allow "guest" and "admin" to access actions "list" and "manage" on these "index",
                // "static" and "console" controllers
                array(
                    'controller' => array('index', 'static', 'console'),
                    'action' => array('list', 'manage'),
                    'roles' => array('guest', 'admin')
                ),
                array(
                    'controller' => array('search', 'administration'),
                    'roles' => array('staffer', 'admin')
                ),
                array('controller' => 'zfcuser', 'roles' => array()),
                // Below is the default index action used by the ZendSkeletonApplication
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
                // Below is the default index action used by the ZendSkeletonApplication
                array('route' => 'home', 'roles' => array('guest', 'user')),
            ),
        ),
    ),
);
```

## Helpers and Plugins

There are view helpers and controller plugins registered for this module.
In either a controller or a view script, you can call
```$this->isAllowed($resource[, $privilege])```, which will query the ACL
using the currently authenticated (or default) user's roles.

Whenever you need to stop processing your action you can throw an UnAuthorizedException and users will see you message on a 403 page.

```php
function cafeAction() {
    if (!$this->isAllowed('alcohol', 'consume')) {
        throw new \BjyAuthorize\Exception\UnAuthorizedException('Grow a beard first!');
    }

    // party on ...
}
```

## License
Released under the MIT License. See file [LICENSE](https://github.com/bjyoungblood/BjyAuthorize/blob/master/LICENSE)
included with the source code for this project for a copy of the licensing terms.
