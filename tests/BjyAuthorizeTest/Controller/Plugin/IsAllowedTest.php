<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\Controller\Plugin;

use PHPUnit_Framework_TestCase;
use BjyAuthorize\Controller\Plugin\IsAllowed;

/**
 * IsAllowed controller plugin test
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class IsAllowedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \BjyAuthorize\Controller\Plugin\IsAllowed
     */
    public function testIsAllowed()
    {
        $authorize = $this->getMock('BjyAuthorize\\Service\\Authorize', array(), array(), '', false);
        $authorize
            ->expects($this->once())
            ->method('isAllowed')
            ->with('test', 'privilege')
            ->will($this->returnValue(true));

        $plugin = new IsAllowed($authorize);
        $this->assertTrue($plugin->__invoke('test', 'privilege'));

        $authorize2 = $this->getMock('BjyAuthorize\\Service\\Authorize', array(), array(), '', false);
        $authorize2
            ->expects($this->once())
            ->method('isAllowed')
            ->with('test2', 'privilege2')
            ->will($this->returnValue(false));

        $plugin = new IsAllowed($authorize2);

        $this->assertFalse($plugin->__invoke('test2', 'privilege2'));
    }
}
