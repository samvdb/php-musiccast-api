<?php
/**
 * @author Damien SUROT <damien@toxeek.com>
 */

namespace MusicCast\Tests\Api;

use MusicCast\Exception\ErrorException;

class ZoneTest extends TestCase
{

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

    public function testSetVolumeByStep()
    {
        $volume = ($this->client->api('zone')->getStatus('main'))['volume'];
        $this->client->api('zone')->setVolume('main', 'up', '1');
        self::assertTrue(($this->client->api('zone')->getStatus('main'))['volume'] == ($volume + 1));
        $this->client->api('zone')->setVolume('main', 'down', '1');
        self::assertTrue(($this->client->api('zone')->getStatus('main'))['volume'] == $volume);
    }

    public function testSetVolumeByLevel()
    {
        $volume = ($this->client->api('zone')->getStatus('main'))['volume'];
        $this->client->api('zone')->setVolume('main', $volume + 1);
        self::assertTrue(($this->client->api('zone')->getStatus('main'))['volume'] == ($volume + 1));
        $this->client->api('zone')->setVolume('main', $volume);
        self::assertTrue(($this->client->api('zone')->getStatus('main'))['volume'] == $volume);
    }

    public function setMute()
    {
        $mute = ($this->client->api('zone')->getStatus('main'))['mute'];
        $this->client->api('zone')->setMute('main', !$mute);
        self::assertTrue(($this->client->api('zone')->getStatus('main'))['mute'] == !$mute);
        $this->client->api('zone')->setMute('main', $mute);
        self::assertTrue(($this->client->api('zone')->getStatus('main'))['volume'] == $mute);
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
