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

class SpeakerTest extends MockTest
{
    protected $device;

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
        self::assertEquals("localhost", $this->speaker->getGroup());
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
}
