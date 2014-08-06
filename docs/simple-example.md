`BjyAuthorize` can be used as a way to control access to certain pages in an administration by virtue 
of the user status. Here is an example on how this can be done:

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
    'resource_providers' => [
        'BjyAuthorize\Provider\Resource\Config' => [
            'menu' => [],
        ],
    ],
];
```

The name for the resource is `'menu'`, as it is specific to the menu items in this view.

Then, under `'rule_providers'`, We setup following rules:

```php
    'rule_providers' => [
        'BjyAuthorize\Provider\Rule\Config' => [
            'allow' => array(
                [['administration'], 'menu', ['menu_menu1']],
                [['administration', 'affiliate'], 'menu', ['menu_menu2']],
                [[‘administration', 'affiliate', 'guest'], 'menu', ['menu_menu3']],
            ],
        ],
    ],
];
```

These rules grant access to `'menu_menu1'` to the `'administrator'` role, `'menu_menu2'` to the
`'affiliate'` as well as the `'administrator'` and `'menu_menu3'` to all 3 existing roles.


Finally I used the **view helper**, provided by BjyAuthorize, to grant the required access:

    <ul>
	<?php if ($this->isAllowed( 'menu', 'menu_menu1' )) { ?>
    		<li>Menu 1</li>
	<?php } ?>

	<?php if ($this->isAllowed( 'menu', ‘menu'_menu2 )) { ?>
    		<li>Menu 2</li>
	<?php } ?>

	<?php if ($this->isAllowed( 'menu', ‘menu'_menu2 )) { ?>
    		<li>Menu 3</li>
	<?php } ?>
    </ul>

So now if an admin is logged in he will see all the menu options, an affiliate will see option 2 and 3 while a guest the 3rd option.

### Disabling the page content associated to a route:

Obviously the above only disables the navigation and if someone knows the URL they can still access the information. BjyAuthorize has a useful thing called a 'guard' to deal with this. As each of my controllers are specific to a specific user role, I simply use a guard to control access to the pages (Actions) delivered by the controllers.

This is my guard:

    'guards' => array(
        'BjyAuthorize\Guard\Controller' => array(
            array('controller' => 'zfcuser', 'roles' => array()),
            array('controller' => array('Module\Controller\Menu1Controller'), 'roles' => array('admin')),
            array('controller' => array('Module\Controller\Menu2Controller'), 'roles' => array('admin','affiliate')),
            array('controller' => array('Module\Controller\Menu3Controller'), 'roles' => array('admin','affiliate','guest')),
    ),

I hope the above is useful to you!