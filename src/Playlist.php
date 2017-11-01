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
    private $speaker;


    /**
     * Create an instance of the Playlist class.
     *
     * @param int $bank
     * @param string $name
     * @param Speaker $controller A speaker instance on the playlist's network
     */
    public function __construct($bank, $name, Speaker $controller)
    {
        $this->id = $bank;
        $this->name = $name;
        $this->speaker = $controller;
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
        $this->speaker->call('netusb', 'manageMcPlaylist', [$this->id, 'play', $index]);
    }
}
