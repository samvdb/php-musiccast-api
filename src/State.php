<?php

namespace MusicCast;

/**
 * Representation of the current state of a controller.
 */
class State extends \MusicCast\Tracks\Track
{
    /**
     * @var string $duration The duration of the currently active track (hh:mm:ss).
     */
    public $duration = "";

    /**
     * @var string $position The position of the currently active track (hh:mm:ss).
     */
    public $position = "";

    /**
     * Create a Track object.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Update the track properties using an xml element.
     *
     * @param array $json The json element representing the track meta data.
     * @param Controller $controller A controller instance on the playlist's network
     *
     * @return  static
     */
    public static function createFromJson($json, Controller $controller)
    {
        $track = parent::createFromJson($json, $controller);
        $track->duration = "02:30";
        $track->position = '';
        return $track;
    }
}
