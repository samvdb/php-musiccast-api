<?php

namespace MusicCast;

use MusicCast\Tracks\Track;

/**
 * Representation of the current state of a controller.
 * @author Damien Surot <damien@toxeek.com>
 */
class State
{
    /**
     * @var string $duration The duration of the currently active track (hh:mm:ss).
     */
    public $duration = "";

    /**
     * @var string $position The position of the currently active track (hh:mm:ss).
     */
    public $position = "";

    public $track;

    /**
     * Create a Track object.
     */
    public function __construct()
    {
    }

    /**
     * Update the track properties using an xml element.
     *
     * @param $playInfo
     * @param $ip
     * @return static
     */
    public static function buildState($playInfo, $ip)
    {
        $state = new State();
        if ($art = $playInfo['albumart_url']) {
            if (substr($art, 0, 4) !== "http") {
                $art = ltrim($art, "/");
                $art = sprintf("http://%s:80/%s", $ip, $art);
            }
        }
        $state->track = new Track(
            $playInfo['input'],
            $playInfo['track'],
            $art,
            $playInfo['artist'],
            $playInfo['album']
        );
        $state->duration = $playInfo['total_time'];
        $state->position = $playInfo['play_time'];
        return $state;
    }
}
