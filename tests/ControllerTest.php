<?php
/**
 * Created by PhpStorm.
 * User: dsurot
 * Date: 18/10/2017
 * Time: 13:32
 */

namespace MusicCastTests;

use MusicCast\Controller;
use MusicCast\Tracks\Track;

class ControllerTest extends \MusicCastTests\MockTest
{
    /**
     * @var Controller
     */
    protected $controller;

    public function setUp()
    {
        parent::setUp();
        $this->controller = $this->getController();
    }

    public function getState()
    {
        self::assertEquals(Controller::STATE_PLAYING, $this->controller->getState());
    }

    public function testGetStateDetails()
    {
        $state = $this->controller->getStateDetails();
        self::assertEquals(
            new Track(
                "server",
                "Voulez-Vous",
                "http://localhost:80/YamahaRemoteControl/AlbumART/AlbumART1115.jpg",
                "ABBA",
                "Gold: Greatest Hits"
            ),
            $state->track
        );
        self::assertEquals("0", $state->duration);
        self::assertEquals("190", $state->position);
    }


    public function testGetPlaylists()
    {
        $playlists = $this->controller->getPlaylists();
        $keys = ["WakeUp",
            "Rock",
            "Lounge",
            "John",
            "Jane"];
        foreach ($keys as $key) {
            self::assertArrayHasKey($key, $playlists);
        }
        self::assertTrue(sizeof($playlists) == sizeof($keys));
    }

    public function getPlaylistByName()
    {
        $playlist = $this->controller->getPlaylistByName("WakeUp");
        self::assertEquals("WakeUp", $playlist->getName());
    }

    public function getPlaylistByNameIgnoreCase()
    {
        $playlist = $this->controller->getPlaylistByName("wAkEuP");
        self::assertEquals("WakeUp", $playlist->getName());
    }

    public function testGetPlaylistById()
    {
        $playlist = $this->controller->getPlaylistById(1);
        self::assertEquals("WakeUp", $playlist->getName());
    }

    public function testGetFavorites()
    {
        $favorites = $this->controller->getFavorites();
        $keys = ["OÜI FM Classic Rock",
            "4U Funky Classics"];
        foreach ($keys as $key) {
            self::assertArrayHasKey($key, $favorites);
        }
        self::assertTrue(sizeof($favorites) == sizeof($keys));
    }

    public function getFavoriteByName()
    {
        $favorite = $this->controller->getFavoriteByName("WakeUp");
        self::assertEquals("OÜI FM Classic Rock", $favorite->getName());
    }

    public function getFavoriteByNameIgnoreCase()
    {
        $favorite = $this->controller->getFavoriteByName("wAkEuP");
        self::assertEquals("OÜI FM Classic Rock", $favorite->getName());
    }

    public function testGetFavoriteById()
    {
        $favorite = $this->controller->getFavoriteById(1);
        self::assertEquals("OÜI FM Classic Rock", $favorite->getName());
    }

    public function testHasFavorite()
    {
        self::assertTrue($this->controller->hasFavorite("OÜI FM Classic Rock"));
        self::assertTrue($this->controller->hasFavorite("OÜI fm clASSic rOCk"));
        self::assertFalse($this->controller->hasFavorite("Non existing favorite"));
    }

    public function testGetQueue()
    {
        $queue = $this->controller->getQueue();
        self::assertEquals(0, $queue->getPlayingIndex());
        self::assertEquals(4, $queue->count());
        self::assertEquals(new Track(
            "server",
            "Fernando",
            "http:\/\/192.168.86.4:50002\/transcoder\/jpegtnscaler.cgi\/ebdart\/73993.jpg"
        ), $queue->getTracks()[0]);
    }
}
