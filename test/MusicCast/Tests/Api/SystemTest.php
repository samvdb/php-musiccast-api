<?php
/**
 * @author Damien SUROT <damien@toxeek.com>
 */

namespace MusicCast\Tests\Api;

class SystemTest extends TestCase
{

    /**
     * @test
     */
    public function testGetDeviceInfo()
    {
        self::assertArrayHasKey('model_name', $this->client->api('system')->getDeviceInfo());
    }

    public function testGetFeatures()
    {
        self::assertArrayHasKey('system', $this->client->api('system')->getFeatures());
        self::assertArrayHasKey('func_list', ($this->client->api('system')->getFeatures())['system']);
    }

    public function testGetNetworkStatus()
    {
        self::assertArrayHasKey('network_name', $this->client->api('system')->getNetworkStatus());
    }

    public function testGetFuncStatus()
    {
        $this->client->api('system')->getFuncStatus();
    }

    public function testSetAutoPowerStandby()
    {
        $funcStatus = $this->client->api('system')->getFuncStatus();
        if (array_key_exists('auto_power_standby', $funcStatus)) {
            $previous = $funcStatus['auto_power_standby'];
            $this->client->api('system')->setAutoPowerStandby(!$previous);
            self::assertTrue($this->client->api('system')->getFuncStatus()['auto_power_standby'] != $previous);
            $this->client->api('system')->setAutoPowerStandby($previous);
            self::assertTrue($this->client->api('system')->getFuncStatus()['auto_power_standby'] == $previous);
            return;
        }
        echo 'Can\'t test setAutoPowerStandby on this device';
    }

    public function testGetLocationInfo()
    {
        self::assertArrayHasKey('name', $this->client->api('system')->getLocationInfo());
    }

    public function testSendIrCode()
    {
        $this->client->api('system')->sendIrCode('00000000');
    }
}
