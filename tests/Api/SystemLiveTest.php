<?php
/**
 * @author Damien SUROT <damien@toxeek.com>
 */

namespace MusicCastTests\Api;

class SystemLiveTest extends \MusicCastTests\LiveTest
{
    protected function setUp()
    {
        parent::setUp();
    }
    /**
     * @test
     */
    public function testGetDeviceInfo()
    {
        self::assertArrayHasKey('model_name', $this->client->api('system')->getDeviceInfo());
    }

    /**
     * @test
     */
    public function testGetFeatures()
    {
        self::assertArrayHasKey('system', $this->client->api('system')->getFeatures());
        self::assertArrayHasKey('func_list', ($this->client->api('system')->getFeatures())['system']);
    }

    /**
     * @test
     */
    public function testGetNetworkStatus()
    {
        self::assertArrayHasKey('network_name', $this->client->api('system')->getNetworkStatus());
    }

    /**
     * @test
     */
    public function testGetFuncStatus()
    {
        $this->client->api('system')->getFuncStatus();
    }

    /**
     * @test
     */
    public function testSetAutoPowerStandby()
    {
        $funcStatus = $this->client->api('system')->getFuncStatus();
        if (array_key_exists('auto_power_standby', $funcStatus)) {
            $previous = $funcStatus['auto_power_standby'];
            $this->client->api('system')->setAutoPowerStandby(!$previous);
            sleep(1);
            self::assertTrue($this->client->api('system')->getFuncStatus()['auto_power_standby'] != $previous);
            $this->client->api('system')->setAutoPowerStandby($previous);
            sleep(1);
            self::assertTrue($this->client->api('system')->getFuncStatus()['auto_power_standby'] == $previous);
            return;
        }
        echo 'Can\'t test setAutoPowerStandby on this device';
    }

    /**
     * @test
     */
    public function testGetLocationInfo()
    {
        self::assertArrayHasKey('name', $this->client->api('system')->getLocationInfo());
    }


    /**
     * @test
     */
    public function testSendIrCode()
    {
        $this->client->api('system')->sendIrCode('00000000');
    }

    /**
     * @test
     */
    public function testGetNameText()
    {
        self::assertArrayHasKey('zone_list', $this->client->api('system')->getNameText());
    }

    /**
     * @test
     */
    public function testIsNewFirmwareAvailable()
    {
        self::assertArrayHasKey('available', $this->client->api('system')->isNewFirmwareAvailable());
    }

    /**
     * @test
     */
    public function testGetTag()
    {
        self::assertArrayHasKey('zone_list', $this->client->api('system')->getTag());
    }

    /**
     * @test
     */
    public function testGetDisklavierSettings()
    {
        //self::assertArrayHasKey('enable', $this->client->api('system')->getDisklavierSettings());
        $this->markTestSkipped('system/getDisklavierSettings method not implemented');
    }


    /**
     * @test
     */
    public function testGetMusicCastTreeInfo()
    {
        self::assertArrayHasKey('mode', $this->client->api('system')->getMusicCastTreeInfo());
    }
}
