<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 11/10/17
 * Time: 19:44
 */

namespace MusicCastTests;

use MusicCast\Controller;

class ControllerLiveTest extends \MusicCastTests\LiveTest
{
    /**
     * @var Controller
     */
    protected $controller;

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

    public function testGetState()
    {
        $state = $this->controller->getState();
        self::assertNotEquals(Controller::STATE_UNKNOWN, $state);
    }

    public function testGetStateDetails()
    {
        $state = $this->controller->getStateDetails();
        self::assertNotNull($state);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->controller = $this->network->getController();
    }
}
