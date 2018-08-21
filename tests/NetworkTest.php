<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 13/10/17
 * Time: 14:28
 */

namespace MusicCastTests;

use MusicCast\Network;

class NetworkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Network
     */
    protected $network;

    public function setUp()
    {
        $this->network = new Network;
    }

    public function testDefaultValues()
    {
        $this->assertSame("devices_NULL__239.255.255.250", $this->getCacheKey());
    }

    protected function getCacheKey()
    {
        $class = new \ReflectionClass($this->network);
        $method = $class->getMethod("getCacheKey");
        $method->setAccessible(true);
        return $method->invoke($this->network);
    }

    public function testSetMulticastAddress()
    {
        $this->network->setMulticastAddress("127.0.0.1");
        $this->assertSame("devices_NULL__127.0.0.1", $this->getCacheKey());
    }

    public function testGetNetworkInterface()
    {
        $this->assertNull($this->network->getNetworkInterface());
    }

    public function testSetNetworkInterfaceString()
    {
        $this->network->setNetworkInterface("eth0");
        $this->assertSame("eth0", $this->network->getNetworkInterface());
        $this->assertSame("devices_string_eth0_239.255.255.250", $this->getCacheKey());
    }

    public function testSetNetworkInterfaceInteger()
    {
        $this->network->setNetworkInterface(0);
        $this->assertSame(0, $this->network->getNetworkInterface());
        $this->assertSame("devices_integer_0_239.255.255.250", $this->getCacheKey());
    }

    public function testSetNetworkInterfaceEmptyString()
    {
        $this->network->setNetworkInterface("");
        $this->assertSame("", $this->network->getNetworkInterface());
        $this->assertSame("devices_string__239.255.255.250", $this->getCacheKey());
    }
}
