<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 13/10/17
 * Time: 19:25
 */

namespace MusicCastTests;

use Mockery;
use MusicCast\Speaker;

class SpeakerTest extends \MusicCastTests\MockTest
{
    /**
     * @var Speaker
     */
    protected $speaker;

    public function setUp()
    {
        parent::setUp();
        $this->speaker = $this->getSpeaker();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testGetModel()
    {
        self::assertEquals("RX-V481D", $this->speaker->getModel());
    }

    public function testGetName()
    {
        self::assertEquals("BedRoom", $this->speaker->getName());
    }

    public function testGetGroup()
    {
        self::assertEquals(Speaker::NO_GROUP, $this->speaker->getGroup());
    }

    public function testIsCoordinator()
    {
        self::assertTrue($this->speaker->isCoordinator());
    }

    public function testGetUuid()
    {
        self::assertEquals("ABCDEEFAA063", $this->speaker->getUuid());
    }

    public function testGetVolume()
    {
        self::assertEquals("77", $this->speaker->getVolume());
    }

    public function isMuted()
    {
        self::assertFalse($this->speaker->isMuted());
    }

    public function testInput()
    {
        self::assertEquals("tuner", $this->speaker->getInput());
    }

    public function testIsPowerOn()
    {
        self::assertFalse($this->speaker->isPowerOn());
    }

    public function testPowerOn()
    {
        self::assertNotNull($this->speaker->powerOn());
    }
}
