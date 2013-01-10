<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\View\Helper;

use PHPUnit_Framework_TestCase;
use BjyAuthorize\View\Helper\IsAllowed;

/**
 * IsAllowed view helper test
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class IsAllowedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \BjyAuthorize\View\Helper\IsAllowed
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
        $this->assertSame($authorize, $plugin->getAuthorizeService());

        $authorize2 = $this->getMock('BjyAuthorize\\Service\\Authorize', array(), array(), '', false);
        $authorize2
            ->expects($this->once())
            ->method('isAllowed')
            ->with('test2', 'privilege2')
            ->will($this->returnValue(false));

        $plugin->setAuthorizeService($authorize2);
        $this->assertSame($authorize2, $plugin->getAuthorizeService());
        $this->assertFalse($plugin->__invoke('test2', 'privilege2'));
    }
}
