`BjyAuthorize` can be used as a way to control access to certain pages in an administration by 
virtue of the user status. Here is an example on how this can be done:

### Controlling what html a user can see in a view:

The view to be modified consisted of three menu options:

```php
<ul>
    <li>Menu 1</li>
    <li>Menu 2</li>
    <ii>Menu 3</li>
</ul>
```

The admin can see all three menus, the affiliate can see menu #2 and #3, and the guest #3 only.

To get this to work, we setup a resource in our `config/autoload/bjyauthorize.global.php` file.

```php
return [
    'bjyauthorize' => [
        'resource_providers' => [
            \BjyAuthorize\Provider\Resource\Config::class => [
                'menu' => [],
            ],
        ],
    ],
];
```

The name for the resource is `'menu'`, as it is specific to the menu items in this view.

Then, under `'rule_providers'`, We setup following rules (in 
`config/autoload/bjyauthorize.global.php` again):

```php
return [
    'bjyauthorize' => [
        'rule_providers' => [
            \BjyAuthorize\Provider\Rule\Config::class => [
                'allow' => [
                    [['administration'], 'menu', ['menu_menu1']],
                    [['administration', 'affiliate'], 'menu', ['menu_menu2']],
                    [['administration', 'affiliate', 'guest'], 'menu', ['menu_menu3']],
                ],
            ],
        ],
    ],
];
```

These rules grant access to `'menu_menu1'` to the `'administrator'` role, `'menu_menu2'` to the
`'affiliate'` as well as the `'administrator'` and `'menu_menu3'` to all 3 existing roles.


Finally we use the `isAllowed` **view helper**, provided by BjyAuthorize, to limit access to menu 
items:

```php
<ul>
<?php if ($this->isAllowed('menu', 'menu_menu1')) { ?>
    <li>Menu 1</li>
<?php } ?>

<?php if ($this->isAllowed('menu', 'menu_menu2')) { ?>
    <li>Menu 2</li>
<?php } ?>

<?php if ($this->isAllowed('menu', 'menu_menu3')) { ?>
    <li>Menu 3</li>
<?php } ?>
</ul>
```

This will hide or show the items in our menu according to our configured ACL rules and the 
current logged in user.

### Disabling the page content associated to a route:

Obviously, what provided so far only **disables** the links to those sections of our site, 
so we **MUST** prevent direct access to those features of the application to unauthorized
users.

BjyAuthorize's main feature is exactly this: "guarding" against unauthorized acces, exactly
like a firewall.

In order to do that, we have to configure the "guards" provided by the module, which is usually
done via configuration (again in `config/autoload/bjyauthorize.global.php`):

```php
return [
    'bjyauthorize' => [
        'guards' => [
            \BjyAuthorize\Guard\Controller::class => [
                ['controller' => 'zfcuser', 'roles' => []],
                ['controller' => ['Module\Controller\Menu1Controller'], 'roles' => ['admin']],
                ['controller' => ['Module\Controller\Menu2Controller'], 'roles' => ['admin','affiliate']],
                ['controller' => ['Module\Controller\Menu3Controller'], 'roles' => ['admin','affiliate','guest']],
            ],
        ],
    ],
];
```
