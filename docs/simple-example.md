I decided to use BjyAuthorize/ZfcUser as a way to control access to certain pages in an administration by virtue of the users status. This is what I used it for:

### Controlling what html a user can see in a view:

My view consisted of three menu options:

    <ul>
        <li>Menu 1</li>
        <li>Menu 2</li>
        <ii>Menu 3</li>
    </ul>

The admin can see all three menus, the affiliate menu 2 / 3, and the guest menu 3 only.

To get this to work I setup a resource in the config/autoload/bjyauthorize.global.php file.

    'resource_providers' => array(
        'BjyAuthorize\Provider\Resource\Config' => array(
            'menu' => array(),
        ),
    ),

I name the resource '**menu**' as it is specific to the menu items in my view.

Then, under 'rule providers' I setup the following. Notice how I have included the 'menu' resource from above:

   'rule_providers' => array(
        'BjyAuthorize\Provider\Rule\Config' => array(
            'allow' => array(
                array( array( 'administration' ), 'menu', array( 'menu_menu1 ) ),
                array( array( 'administration’,’affiliate’ ), 'menu', array( 'menu_menu2’ ) ),
                array( array( ‘administration’,’affiliate’,’guest’ ), 'menu', array( 'menu_menu3’ ) ),
            ),
        ),
    ),


Finally I used the **view helper**, provided by BjyAuthorize, to grant the required access:

    <ul>
	<?php if ($this->isAllowed( 'menu', 'menu_menu1’ )) { ?>
    		<li>Menu 1</li>
	<?php } ?>

	<?php if ($this->isAllowed( 'menu', ‘menu’_menu2 )) { ?>
    		<li>Menu 2</li>
	<?php } ?>

	<?php if ($this->isAllowed( 'menu', ‘menu’_menu2 )) { ?>
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
            array('controller' => array('Module\Controller\Menu1Controller'), 'roles' => array('admin','affiliate','guest')),
    ),

I hope the above is useful to you!