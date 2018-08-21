<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 11/10/17
 * Time: 12:53
 */

namespace MusicCast;

/**
 * Class Favorite
 * @package MusicCast
 * @author Damien Surot <damien@toxeek.com>
 */
class Favorite
{
    /**
     * @var string|null $name The name of the playlist.
     */
    protected $name;
    protected $input;
    protected $id;
    protected $speaker;


    /**
     * Create an instance of the Playlist class.
     *
     * @param int $index
     * @param array $data
     * @param Speaker $speaker A controller instance on the playlist's network
     */
    public function __construct($index, $data, Speaker $speaker)
    {
        $this->id = $index;
        $this->name = $data['text'];
        $this->input = $data['input'];
        $this->speaker = $speaker;
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

    /**
     * @return mixed
     */
    public function getInput()
    {
        return $this->input;
    }


    public function play()
    {
        $this->speaker->call('netusb', 'recallPreset', ['main', $this->id]);
    }
}
