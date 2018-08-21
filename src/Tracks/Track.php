<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 11/10/17
 * Time: 13:10
 */

namespace MusicCast\Tracks;

/**
 * Representation of a track.
 * @author Damien Surot <damien@toxeek.com>
 */
class Track
{

    /**
     * @var string $title The name of the track.
     */
    protected $title = "";

    /**
     * @var string $artist The name of the artist of the track.
     */
    protected $artist = "";

    /**
     * @var string $album The name of the album of the track.
     */
    protected $album = "";

    /**
     * @var string $albumArt The full path to the album art for this track.
     */
    protected $albumArt = "";

    protected $input = "";

    /**
     * Track constructor.
     * @param $input
     * @param $title
     * @param $albumArt
     * @param null $artist
     * @param null $album
     */
    public function __construct($input, $title, $albumArt, $artist = null, $album = null)
    {
        $this->title = $title;
        $this->artist = $artist;
        $this->album = $album;
        $this->albumArt = stripcslashes($albumArt);
        $this->input = $input;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getArtist()
    {
        return $this->artist;
    }

    /**
     * @return string
     */
    public function getAlbum()
    {
        return $this->album;
    }

    /**
     * @return string
     */
    public function getAlbumArt()
    {
        return $this->albumArt;
    }

    /**
     * @return string
     */
    public function getInput()
    {
        return $this->input;
    }
}
