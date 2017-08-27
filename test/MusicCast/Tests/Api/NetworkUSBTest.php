<?php
/**
 * @author Damien SUROT <damien@toxeek.com>
 */

namespace MusicCast\Tests\Api;

class NetworkUSBTest extends TestCase
{
    /**
     * @test
     */
    public function testGetPresetInfo()
    {
        self::assertArrayHasKey('preset_info', $this->client->api('netusb')->getPresetInfo());
    }

    /**
     * @test
     */
    public function testGetPlayInfo()
    {
        self::assertArrayHasKey('input', $this->client->api('netusb')->getPlayInfo());
    }

    /**
     * @test
     */
    public function testSetPlayback()
    {
        //self::assertArrayHasKey('???', $this->client->api('netusb')->setPlayback());
        $this->markTestSkipped('netusb/setPlayback method not implemented');
    }

    /**
     * @test
     */
    public function testToggleRepeat()
    {
        self::assertArrayHasKey('response_code', $this->client->api('netusb')->toggleRepeat());
    }

    /**
     * @test
     */
    public function testToggleShuffle()
    {
        self::assertArrayHasKey('response_code', $this->client->api('netusb')->toggleShuffle());
    }

    /**
     * @test
     */
    public function testGetListInfo()
    {
        self::assertArrayHasKey(
            'list_info',
            $this->client->api('netusb')->getListInfo('bluetooth', 5)
        );
    }

    /**
     * @test
     */
    public function testSetListControl()
    {
        //self::assertArrayHasKey('???', $this->client->api('netusb')->setListControl());
        $this->markTestSkipped('netusb/setListControl method not implemented');
    }

    /**
     * @test
     */
    public function testSetSearchString()
    {
        //self::assertArrayHasKey('???', $this->client->api('netusb')->setSearchString());
        $this->markTestSkipped('netusb/setSearchString method not implemented');
    }

    /**
     * @test
     */
    public function testRecallPreset()
    {
        //self::assertArrayHasKey('???', $this->client->api('netusb')->recallPreset());
        $this->markTestSkipped('netusb/recallPreset method not implemented');
    }

    /**
     * @test
     */
    public function testStorePreset()
    {
        //self::assertArrayHasKey('???', $this->client->api('netusb')->storePreset());
        $this->markTestSkipped('netusb/storePreset method not implemented');
    }

    /**
     * @test
     */
    public function testGetAccountStatus()
    {
        self::assertArrayHasKey('service_list', $this->client->api('netusb')->getAccountStatus());
    }

    /**
     * @test
     */
    public function testSwitchAccount()
    {
        //self::assertArrayHasKey('???', $this->client->api('netusb')->switchAccount());
        $this->markTestSkipped('netusb/switchAccount method not implemented');
    }

    /**
     * @test
     */
    public function testGetServiceInfo()
    {
        //self::assertArrayHasKey('???', $this->client->api('netusb')->getServiceInfo());
        $this->markTestSkipped('netusb/getServiceInfo method not implemented');
    }

    /**
     * @test
     */
    public function testGetMcPlaylistName()
    {
        self::assertArrayHasKey('name_list', $this->client->api('netusb')->getMcPlaylistName());
    }

    /**
     * @test
     */
    public function testGetPlayQueue()
    {
        self::assertArrayHasKey('type', $this->client->api('netusb')->getPlayQueue());
    }

    /**
     * @test
     */
    public function testGetRecentInfo()
    {
        self::assertArrayHasKey('recent_info', $this->client->api('netusb')->getRecentInfo());
    }

    /**
     * @test
     */
    public function testSetYmapUri()
    {
        //self::assertArrayHasKey('???', $this->client->api('netusb')->setYmapUri());
        $this->markTestSkipped('netusb/setYmapUri method not implemented');
    }
}
