<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 11/10/17
 * Time: 19:44
 */

namespace MusicCastTests;

use MusicCast\Controller;
use MusicCast\Speaker;

class ControllerLiveTest extends \MusicCastTests\LiveTest
{
    /**
     * @var Controller
     */
    protected $controller;


    protected function setUp()
    {
        parent::setUp();
        $this->controller = $this->network->getControllerByIp($this->options['host']);
        $this->controller->powerOn();
        $this->controller->getPlaylistById(1)->play();
        time_nanosleep(0, 500 * 1000000);//500ms
    }

    public function testConstructor1()
    {
        foreach ($this->network->getSpeakers() as $speaker) {
            if ($speaker->isCoordinator()) {
                $controller = new Controller($speaker, $this->network, 0);
                $this->assertSame($speaker->getIp(), $controller->getIp());
                return;
            }
        }

        throw new \Exception("No speakers found that are the coordinator of their group");
    }


    public function testConstructor2()
    {
        $this->expectException("InvalidArgumentException");

        foreach ($this->network->getSpeakers() as $speaker) {
            if (!$speaker->isCoordinator()) {
                new Controller($speaker, $this->network, 0);
                return;
            }
        }

        $this->markTestSkipped("No speakers found that are not the coordinator of their group");
    }


    public function testIsCoordinator()
    {
        $this->assertTrue($this->controller->isCoordinator());
    }


    public function testGetStateName()
    {
        $states = ["play", "stop", "pause", "play_pause", "previous", "next",
            "fast_reverse_start", "fast_reverse_end", "fast_forward_start",
            "fast_forward_end"];
        foreach ($this->network->getControllers() as $controller) {
            $this->assertContains($controller->getStateName(), $states);
        }
    }


    public function testGetState()
    {
        $states = [Controller::STATE_STOPPED, Controller::STATE_PLAYING, Controller::STATE_PAUSED, Controller::STATE_TRANSITIONING];
        foreach ($this->network->getControllers() as $controller) {
            $this->assertContains($controller->getState(), $states);
        }
    }


    public function testGetStateDetails()
    {
        $track_keys = ["input", "title", "artist", "album", "albumArt"];
        $state_keys = ["duration", "position"];
        $state = $this->controller->getStateDetails();
        foreach ($state_keys as $key) {
            $this->assertObjectHasAttribute($key, $state);
        }
        foreach ($track_keys as $key) {
            $this->assertObjectHasAttribute($key, $state->track);
        }
    }


    public function testNext()
    {
        $controller = $this->controller;
        $number = $controller->getQueue()->getPlayingIndex();
        $controller->next();
        $this->assertSame($controller->getQueue()->getPlayingIndex(), $number + 1);
    }


    public function testPrevious()
    {
        $controller = $this->controller;
        $number = $controller->getQueue()->getPlayingIndex();
        $controller->previous();
        $this->assertSame($controller->getQueue()->getPlayingIndex(), $number);
    }


    public function testGetSpeakers()
    {
        $speakers = $this->controller->getSpeakers();
        $this->assertContainsOnlyInstancesOf(Speaker::class, $speakers);
    }


    public function testSetVolume()
    {
        $controller = $this->controller;
        $volume = $controller->getVolume();
        $controller->setVolume($volume - 3);
        sleep(1);
        foreach ($controller->getSpeakers() as $speaker) {
            $this->assertSame($volume - 3, $speaker->getVolume());
        }
    }


    public function testAdjustVolume1()
    {
        $controller = $this->controller;
        $volume = $controller->getVolume();
        $controller->setVolume($volume - 3);
        sleep(1);
        $controller->adjustVolume(3);
        sleep(1);
        foreach ($controller->getSpeakers() as $speaker) {
            $this->assertSame($volume, $speaker->getVolume());
        }
    }


    public function testAdjustVolume2()
    {
        $controller = $this->controller;
        $volume = $controller->getVolume();
        $controller->setVolume($volume + 3);
        sleep(1);
        $controller->adjustVolume(3 * -1);
        sleep(1);
        foreach ($controller->getSpeakers() as $speaker) {
            $this->assertSame($volume, $speaker->getVolume());
        }
    }

    public function testGetPlaylists()
    {
        $playlists = $this->controller->getPlaylists();
        self::assertNotNull($playlists);
    }

    public function testGetPlaylistBy()
    {
        $playlists = $this->controller->getPlaylists();
        $seek = reset($playlists);
        $playlist = $this->controller->getPlaylistById($seek->getId());
        self::assertEquals($seek, $playlist);
        $playlist = $this->controller->getPlaylistByName($seek->getName());
        self::assertEquals($seek, $playlist);
    }

    public function testGetFavorites()
    {
        $playlists = $this->controller->getFavorites();
        self::assertNotNull($playlists);
    }

    public function testGetFavoriteBy()
    {
        $favorites = $this->controller->getFavorites();
        $seek = reset($favorites);
        $favorite = $this->controller->getFavoriteById($seek->getId());
        self::assertEquals($seek, $favorite);
        $favorite = $this->controller->getFavoriteByName($seek->getName());
        self::assertEquals($seek, $favorite);
    }

    public function testAddSpeaker()
    {
        foreach ($this->network->getSpeakers() as $speaker) {
            if (!$speaker->isCoordinator() && $speaker->getGroup() == Speaker::NO_GROUP) {
                $this->controller->addSpeaker($speaker);
                $this->assertSame($speaker->getGroup(), $this->controller->getGroup());
                return;
            }
        }
    }

    public function testRemoveSpeaker()
    {
        foreach ($this->controller->getSpeakers() as $speaker) {
            $this->controller->removeSpeaker($speaker);
            $this->assertNotSame($speaker->getGroup(), $this->controller->getGroup());
            return;
        }
    }

    public function testPowerOn()
    {
        $this->controller->powerOn();
    }
}
