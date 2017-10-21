<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 10/10/17
 * Time: 18:54
 */

namespace MusicCast;

/**
 * Provides an interface for managing playlists on the current network.
 * @author Damien Surot <damien@toxeek.com>
 */
class Playlist
{
    /**
     * @var string|null $name The name of the playlist.
     */
    protected $name;
    protected $id;
    private $controller;


    /**
     * Create an instance of the Playlist class.
     *
     * @param int $bank
     * @param string $name
     * @param Controller $controller A controller instance on the playlist's network
     */
    public function __construct($bank, $name, Controller $controller)
    {
        $this->id = $bank;
        $this->name = $name;
        $this->controller = $controller;
    }


    /**
     * Get the id of the playlist.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Get the name of the playlist.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function play($index = 0)
    {
        $this->controller->call('netusb', 'manageMcPlaylist', [$this->id, 'play', $index]);
    }
}
