<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 11/10/17
 * Time: 12:53
 */

namespace MusicCast;

class Favorite
{
    /**
     * @var string|null $name The name of the playlist.
     */
    protected $name;
    protected $input;
    protected $id;
    protected $controller;


    /**
     * Create an instance of the Playlist class.
     *
     * @param int $bank
     * @param array $data
     * @param Controller $controller A controller instance on the playlist's network
     */
    public function __construct($index, $data, Controller $controller)
    {
        $this->id = $index;
        $this->name = $data['text'];
        $this->input = $data['input'];
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

    /**
     * @return mixed
     */
    public function getInput()
    {
        return $this->input;
    }


    public function play()
    {
        $this->controller->getDevice()->getClient()->api('netusb')->recallPreset('main', $this->id);
    }
}
