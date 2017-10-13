<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 11/10/17
 * Time: 13:10
 */

namespace MusicCast\Tracks;

use MusicCast\Controller;

/**
 * Representation of a track.
 */
class Track
{

    /**
     * @var string $title The name of the track.
     */
    public $title = "";

    /**
     * @var string $artist The name of the artist of the track.
     */
    public $artist = "";

    /**
     * @var string $album The name of the album of the track.
     */
    public $album = "";

    /**
     * @var string $albumArt The full path to the album art for this track.
     */
    public $albumArt = "";

    public $input = "";

    /**
     * Create a Track object.
     */
    public function __construct()
    {
    }

    public static function createFromJson($json, Controller $controller)
    {
        $track = new Track();
        $track->title = $json['track'];
        $track->input = $json['input'];

        $track->artist = $json['artist'];
        $track->album = $json['album'];


        if ($art = $json['albumart_url']) {
            if (substr($art, 0, 4) !== "http") {
                $art = ltrim($art, "/");
                $art = sprintf("http://%s:80/%s", $controller->getIp(), $art);
            }
            $track->albumArt = $art;
        }


        return $track;
    }
}
