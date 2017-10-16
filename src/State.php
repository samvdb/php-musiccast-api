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
     * @var int $queueNumber The zero-based number of the track in the queue.
     */
    public $queueNumber = 0;

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
     * @param Device $device The device.
     * @param Controller $controller A controller instance on the playlist's network
     *
     * @return  static
     */
    public static function createFromJson(Controller $controller)
    {
        $data = $controller->getDevice()->getClient()->api('netusb')->getPlayInfo();
        $track = parent::createFromJson($controller);
        $track->duration = $data['total_time'];
        $track->position = $data['play_time'];
        $data = $controller->getDevice()->getClient()->api('netusb')->getPlayQueue();
        $track->queueNumber = $data['playing_index'];
        return $track;
    }
}
