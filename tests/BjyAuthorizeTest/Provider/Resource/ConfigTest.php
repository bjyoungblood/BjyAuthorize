<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\Provider\Resource;

use PHPUnit_Framework_TestCase;
use BjyAuthorize\Provider\Resource\Config;

/**
 * Config resource provider test
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \BjyAuthorize\Provider\Resource\Config::__construct
     * @covers \BjyAuthorize\Provider\Resource\Config::getResources
     */
    public function testGetResources()
    {
        $config = new Config(array('resource1', 'resource2',));

        $resources = $config->getResources();

        $this->assertCount(2, $resources);
        $this->assertContains('resource1', $resources);
        $this->assertContains('resource2', $resources);
    }
}
