<?php
/**
 * @author Damien SUROT <damien@toxeek.com>
 */

namespace MusicCastTests\Api;

use MusicCast\Exception\ErrorException;

class ZoneLiveTest extends \MusicCastTests\LiveTest
{
    protected function setUp()
    {
        parent::setUp();
    }
    /**
     * @test
     */
    public function testGetStatus()
    {
        self::assertArrayHasKey('power', $this->client->api('zone')->getStatus('main'));
    }

    /**
     * @test
     */
    public function testGetStatusThrowErrorExceptionIfBadZoneName()
    {
        $this->expectException(ErrorException::class);
        $this->client->api('zone')->getStatus('fakeZone');
    }

    public function testGetSoundProgramList()
    {
        self::assertArrayHasKey('sound_program_list', $this->client->api('zone')->getSoundProgramList('main'));
    }

    public function testSetPower()
    {
        $power = ($this->client->api('zone')->getStatus('main'))['power'];
        $this->client->api('zone')->setPower('main', $power);
    }

    public function setSleep()
    {
        $sleep = ($this->client->api('zone')->getStatus('main'))['sleep'];
        $this->client->api('zone')->setSleep('main', $sleep);
    }

    public function testAdjustVolume()
    {
        $volume = ($this->client->api('zone')->getStatus('main'))['volume'];
        $this->client->api('zone')->setVolume('main', 'up', '1');
        sleep(1);
        self::assertEquals(($volume + 1), ($this->client->api('zone')->getStatus('main'))['volume']);
        $this->client->api('zone')->setVolume('main', 'down', '1');
        sleep(1);
        self::assertEquals($volume, ($this->client->api('zone')->getStatus('main'))['volume']);
    }

    public function testSetVolume()
    {
        $volume = ($this->client->api('zone')->getStatus('main'))['volume'];
        $this->client->api('zone')->setVolume('main', $volume + 1);
        sleep(1);
        self::assertEquals(($volume + 1), ($this->client->api('zone')->getStatus('main'))['volume']);
        $this->client->api('zone')->setVolume('main', $volume);
        sleep(1);
        self::assertEquals($volume, ($this->client->api('zone')->getStatus('main'))['volume']);
    }

    public function setMute()
    {
        $mute = ($this->client->api('zone')->getStatus('main'))['mute'];
        $this->client->api('zone')->setMute('main', !$mute);
        sleep(1);
        self::assertEquals(!$mute, ($this->client->api('zone')->getStatus('main'))['mute']);
        $this->client->api('zone')->setMute('main', $mute);
        sleep(1);
        self::assertEquals($mute, ($this->client->api('zone')->getStatus('main'))['volume']);
    }

    public function testPrepareInputChange()
    {
        $input = ($this->client->api('zone')->getStatus('main'))['input'];
        $this->client->api('zone')->prepareInputChange('main', $input);
    }

    public function testSetInput()
    {
        $input = ($this->client->api('zone')->getStatus('main'))['input'];
        $this->client->api('zone')->setInput('main', $input);
    }
}
