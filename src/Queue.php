<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 16/10/17
 * Time: 18:59
 */

namespace MusicCast;

use MusicCast\Tracks\Track;

class Queue implements \Countable
{
    /**
     * @var Track[] $tracks
     */
    protected $tracks;

    /**
     * @var
     */
    protected $count;

    /**
     * @var
     */
    protected $playing_index;


    /**
     * Create an instance of the Queue class.
     *
     * @param array $queueInfo
     */
    public function __construct($queueInfo)
    {
        $this->count = $queueInfo['max_line'];
        $this->playing_index = $queueInfo['playing_index'];
        foreach ($queueInfo['track_info'] as $track_info) {
            $this->tracks[] = new Track($track_info['input'], $track_info['text'], $track_info['thumbnail']);
        }
    }

    /**
     * The number of tracks in the queue.
     *
     * @return int
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * @return Track[]
     */
    public function getTracks(): array
    {
        return $this->tracks;
    }

    /**
     * @return mixed
     */
    public function getPlayingIndex()
    {
        return $this->playing_index;
    }
}
