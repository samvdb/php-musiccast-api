<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 17/10/17
 * Time: 18:13
 */

namespace MusicCastTests;

use MusicCast\Device;

class DeviceTest extends \MusicCastTests\MockTest
{
    /**
     * @var Device
     */
    protected $device;

    public function setUp()
    {
        parent::setUp();
        $this->device = $this->getDevice();
    }

    public function testCall()
    {
        self::assertEquals('ABCDEEFAA063', $this->device->call('system', 'getDeviceInfo')['device_id']);
    }

    public function testGetDeviceInfo()
    {
        $infokeys = ["model_name", "destination", "device_id", "system_id", "system_version", "api_version",
            "netmodule_version", "netmodule_checksum", "operation_mode",
            "update_error_code"];
        $info = $this->device->getDeviceInfo();
        foreach ($infokeys as $infokey) {
            self::assertArrayHasKey($infokey, $info);
        }
    }

    public function testGetLocationInfo()
    {
        $infokeys = ["id", "name", "zone_list"];
        $info = $this->device->getLocationInfo();
        foreach ($infokeys as $infokey) {
            self::assertArrayHasKey($infokey, $info);
        }
    }

    public function testGetMusicCastTreeInfo()
    {
        $infokeys = ["mode", "own_mac_idx", "mac_address_list", "ap_list", "hop_num"];
        $info = $this->device->getMusicCastTreeInfo();
        foreach ($infokeys as $infokey) {
            self::assertArrayHasKey($infokey, $info);
        }
    }

    public function testGetNetworkStatus()
    {
        $infokeys = ["network_name", "connection", "ip_address", "default_gateway", "wireless_lan"];
        $info = $this->device->getNetworkStatus();
        foreach ($infokeys as $infokey) {
            self::assertArrayHasKey($infokey, $info);
        }
    }

    public function testGetUuid()
    {
        self::assertEquals("ABCDEEFAA063", $this->device->getUuid());
    }
}
