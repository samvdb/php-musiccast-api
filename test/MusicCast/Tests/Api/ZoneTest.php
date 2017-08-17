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
        assert(($this->client->api('system')->getDeviceInfo())['response_code'] === 0);
    }

    public function testGetFeatures()
    {
        assert(($this->client->api('system')->getFeatures())['response_code'] === 0);
    }

    public function testGetNetworkStatus()
    {
        assert(($this->client->api('system')->getNetworkStatus())['response_code'] === 0);
    }

    public function testGetFuncStatus()
    {
        assert(($this->client->api('system')->getFuncStatus())['response_code'] === 0);
    }

    public function testSetAutoPowerStandby()
    {
        $funcStatus = $this->client->api('system')->getFuncStatus();
        if (array_key_exists('auto_power_standby', $funcStatus)) {
            $previous = $funcStatus['auto_power_standby'];
            $this->client->api('system')->setAutoPowerStandby(!$previous);
            assert($this->client->api('system')->getFuncStatus()['auto_power_standby'] != $previous);
            $this->client->api('system')->setAutoPowerStandby($previous);
            assert($this->client->api('system')->getFuncStatus()['auto_power_standby'] === $previous);
        }
        else {
            echo 'Can\'t test setAutoPowerStandby on this device';
        }

    }

    public function testGetLocationInfo()
    {
        assert(($this->client->api('system')->getLocationInfo())['response_code'] === 0);
    }

    public function testSendIrCode()
    {
        assert(($this->client->api('system')->sendIrCode('00000000'))['response_code'] === 0);
    }
}