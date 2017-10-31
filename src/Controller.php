<?php

namespace MusicCast;

/**
 * Allows interaction with the groups of speakers.
 *
 * Although sometimes a Controller is synonymous with a Speaker, when speakers are grouped together
 * only the coordinator can receive events (play/pause/etc)
 * @author Damien Surot <damien@toxeek.com>
 */
class Controller extends Speaker
{
    /**
     * No music playing, but not paused.
     *
     */
    const STATE_STOPPED = 201;

    /**
     * Currently plating music.
     */
    const STATE_PLAYING = 202;

    /**
     * Music is currently paused.
     */
    const STATE_PAUSED = 203;

    /**
     * The speaker is currently working on either playing or pausing.
     *
     * Check it's state again in a second or two
     */
    const STATE_TRANSITIONING = 204;

    /**
     * The speaker is in an unknown state.
     *
     * This should only happen if Sonos introduce a new state that this code has not been updated to handle.
     */
    const STATE_UNKNOWN = 205;


    /**
     * @var Network $network The network instance this Controller is part of.
     */
    protected $network;

    /**
     * @var Speaker[]
     */
    private $speakers;
    /**
     * @var
     */
    private $playlists;

    /**
     * @var
     */
    private $favorites;

    /**
     * @var
     */
    private $distribution_id;

    /**
     * Create a Controller instance from a speaker.
     *
     * The speaker must be a coordinator.
     *
     * @param Speaker $speaker
     * @param Network $network
     * @param int $distribution_id
     */
    public function __construct(Speaker $speaker, Network $network, $distribution_id)
    {
        parent::__construct($speaker->device);
        if (!$speaker->isCoordinator()) {
            throw new \InvalidArgumentException("You cannot create a Controller instance from a Speaker that is 
            not the coordinator of it's group");
        }
        $this->network = $network;
        $this->speakers = $this->getSpeakers();
        $this->distribution_id = $distribution_id;
    }

    /**
     * Get the speakers that are in the group of this controller.
     *
     * @return Speaker[]
     */
    public function getSpeakers()
    {
        if (is_array($this->speakers)) {
            return $this->speakers;
        }
        $group = [];
        $speakers = $this->network->getSpeakers();
        foreach ($speakers as $speaker) {
            if ($speaker->getUuid() == $this->getUuid()) {
                $group[] = $speaker;
            }
            if ($speaker->getGroup() === $this->getGroup() &&
                !(is_numeric($speaker->getGroup()) || intval($speaker->getGroup()) == 0)) {
                $group[] = $speaker;
            }
        }
        return $this->speakers = $group;
    }

    /**
     * Check if this speaker is the coordinator of it's current group.
     *
     * This method is only here to override the method from the Speaker class.
     * A Controller instance is always the coordinator of it's group.
     *
     * @return bool
     */
    public function isCoordinator()
    {
        return true;
    }

    /**
     * Get the current state of the group of speakers.
     *
     * @return int One of the class STATE_ constants
     */
    public function getState()
    {
        $name = $this->getStateName();
        switch ($name) {
            case "stop":
                return self::STATE_STOPPED;
            case "play":
                return self::STATE_PLAYING;
            case "pause":
                return self::STATE_PAUSED;
            case "fast_reverse":
                return self::STATE_TRANSITIONING;
            case "fast_forward":
                return self::STATE_TRANSITIONING;
        }
        return self::STATE_UNKNOWN;
    }

    /**
     * Get the current state of the group of speakers as the string reported by sonos: PLAYING, PAUSED_PLAYBACK, etc
     *
     * @return string
     */
    public function getStateName()
    {
        return $this->call('netusb', 'getPlayInfo')['playback'];
    }

    /**
     * Get attributes about the currently active track in the queue.
     *
     * @return State Track data containing the following elements
     */
    public function getStateDetails()
    {
        $json = $this->call('netusb', 'getPlayInfo');
        return State::buildState($json, $this->getIp());
    }

    /**
     * Set the state of the group.
     *
     * @param int $state One of the class STATE_ constants
     *
     * @return static
     */
    public function setState($state)
    {
        switch ($state) {
            case self::STATE_PLAYING:
                return $this->play();
            case self::STATE_PAUSED:
                return $this->pause();
            case self::STATE_STOPPED:
                return $this->stop();
        }
        throw new \InvalidArgumentException("Unknown state: {$state})");
    }

    /**
     * Start playing the active music for this group.
     *
     * @return static
     */
    public function play()
    {
        return $this->setPlayback('play');
    }

    private function setPlayback($playback)
    {
        return $this->call('netusb', 'setPlayback', [$playback]);
    }

    /**
     * Pause the group.
     *
     * @return static
     */
    public function pause()
    {
        return $this->setPlayback('pause');
    }

    /**
     * Pause the group.
     *
     * @return static
     */
    public function stop()
    {
        return $this->setPlayback('stop');
    }

    /**
     * Skip to the next track in the current queue.
     *
     * @return static
     */
    public function next()
    {
        return $this->setPlayback('next');
    }

    /**
     * Skip back to the previous track in the current queue.
     *
     * @return static
     */
    public function previous()
    {
        return $this->setPlayback('previous');
    }


    public function setInput($input)
    {
        if (key_exists('prepareInputChange', $this->call('system', 'getFuncStatus'))) {
            $this->call('zone', 'prepareInputChange', ['main', $input]);
        }
        $this->call('zone', 'setInput', ['main', $input]);
    }

    /**
     * Adds the specified speaker to the group of this Controller.
     *
     * @param Speaker $speaker The speaker to add to the group
     *
     * @return static
     */
    public function addSpeaker(Speaker $speaker)
    {
        if ($speaker->getUuid() === $this->getUuid()) {
            return $this;
        }
        if (!in_array($speaker, $this->speakers)) {
            $group = $this->getGroup();
            if ($group == Speaker::NO_GROUP) {
                $group = md5($this->device->getIp());
            }
            if ($speaker->getGroup() == Speaker::NO_GROUP) {
                $speaker->call('dist', 'setClientInfo', [$group, 'main', $this->device->getIp()]);
                $this->call(
                    'dist',
                    'setServerInfo',
                    [$group, 'add', 'main', array($speaker->device->getIp())]
                );
                $this->call('dist', 'startDistribution', [$this->distribution_id]);
                $this->speakers[] = $speaker;
            }
        }
        return $this;
    }


    /**
     * Removes the specified speaker from the group of this Controller.
     *
     * @param Speaker $speaker The speaker to remove from the group
     *
     * @return static
     */
    public function removeSpeaker(Speaker $speaker)
    {
        if ($speaker->getUuid() === $this->getUuid()) {
            return $this;
        }
        if (in_array($speaker, $this->speakers)) {
            unset($this->speakers[array_search($speaker, $this->speakers)]);
            $speaker->call('dist', 'setClientInfo');
            $this->call(
                'dist',
                'setServerInfo',
                [$this->getGroup(), 'remove', 'main', array($speaker->device->getIp())]
            );
            $this->call('dist', 'startDistribution', [$this->distribution_id]);
        }
        return $this;
    }

    /**
     * Removes all speakers from the group of this Controller.
     *
     * @return static
     */
    public function removeAllSpeakers()
    {
        foreach ($this->getSpeakers() as $speaker) {
            $this->removeSpeaker($speaker);
        }
        return $this;
    }


    /**
     * Adjust the volume of all the speakers controlled by this Controller.
     *
     * @param int $adjust A relative amount between -100 and 100
     *
     * @return static
     */
    public function adjustVolume($adjust)
    {
        $speakers = $this->getSpeakers();
        foreach ($speakers as $speaker) {
            $speaker->adjustVolume($adjust);
        }

        return $this;
    }

    /**
     * Check if repeat is currently active.
     *
     * @return bool
     */
    public function getRepeat()
    {
        return $this->call('netusb', 'getPlayInfo')['repeat'] != 'off';
    }


    /**
     * Turn repeat mode on or off.
     *
     * @return static
     */
    public function toggleRepeat()
    {
        return $this->call('netusb', 'toggleRepeat');
    }


    /**
     * Check if shuffle is currently active.
     *
     * @return bool
     */
    public function getShuffle()
    {
        return $this->call('netusb', 'getPlayInfo')['shuffle'] != 'off';
    }

    /**
     * Turn shuffle mode on or off.
     *
     * @return static
     */
    public function toggleShuffle()
    {
        return $this->call('netusb', 'toggleShuffle');
    }


    /**
     * Get the queue for this controller.
     *
     * @return Queue
     */
    public function getQueue()
    {
        return new Queue($this->call('netusb', 'getPlayQueue'));
    }


    /**
     * Check if a playlist with the specified name exists on this network.
     *
     * If no case-sensitive match is found it will return a case-insensitive match.
     *
     * @param string $name The name of the playlist
     *
     * @return bool
     */
    public function hasPlaylist($name)
    {
        $playlists = $this->getPlaylists();
        foreach ($playlists as $playlist) {
            if ($playlist->getName() === $name) {
                return true;
            }
            if (strtolower($playlist->getName()) === strtolower($name)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get all the playlists available on the network.
     *
     * @return Playlist[]
     */
    public function getPlaylists()
    {
        if (is_array($this->playlists)) {
            return $this->playlists;
        }
        $playlist_names = $this->call('netusb', 'getMcPlaylistName')['name_list'];
        $playlists = [];
        $index = 1;
        foreach ($playlist_names as $playlist_name) {
            $playlists[$playlist_name] = new Playlist($index++, $playlist_name, $this);
        }
        return $this->playlists = $playlists;
    }

    /**
     * Get the playlist with the specified name.
     *
     * If no case-sensitive match is found it will return a case-insensitive match.
     *
     * @param string $name The name of the playlist
     *
     * @return Playlist|null
     */
    public function getPlaylistByName($name)
    {
        $roughMatch = false;
        $playlists = $this->getPlaylists();
        foreach ($playlists as $playlist) {
            if ($playlist->getName() === $name) {
                return $playlist;
            }
            if (strtolower($playlist->getName()) === strtolower($name)) {
                $roughMatch = $playlist;
            }
        }
        if ($roughMatch) {
            return $roughMatch;
        }
        return null;
    }

    /**
     * Get the playlist with the specified id.
     *
     * @param int $id The ID of the playlist
     *
     * @return Playlist
     */
    public function getPlaylistById($id)
    {
        $playlists = $this->getPlaylists();
        foreach ($playlists as $playlist) {
            if ($playlist->getId() === $id) {
                return $playlist;
            }
        }
        return null;
    }

    /**
     * Check if a playlist with the specified name exists on this network.
     *
     * If no case-sensitive match is found it will return a case-insensitive match.
     *
     * @param string $name The name of the playlist
     *
     * @return bool
     */
    public function hasFavorite($name)
    {
        $favorites = $this->getFavorites();
        foreach ($favorites as $item) {
            if ($item->getName() === $name) {
                return true;
            }
            if (strtolower($item->getName()) === strtolower($name)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get Favorites on the network.
     *
     * @return Favorite[]
     */
    public function getFavorites()
    {
        if (is_array($this->favorites)) {
            return $this->favorites;
        }
        $favorites_info = $this->call('netusb', 'getPresetInfo')['preset_info'];
        $favorites = [];
        $index = 0;
        foreach ($favorites_info as $item) {
            $index++;
            if ($item['text'] != '') {
                $favorites[$item['text']] = new Favorite($index, $item, $this);
            }
        }
        return $this->favorites = $favorites;
    }

    /**
     * Get the playlist with the specified name.
     *
     * If no case-sensitive match is found it will return a case-insensitive match.
     *
     * @param string $name The name of the playlist
     *
     * @return Favorite|null
     */
    public function getFavoriteByName($name)
    {
        $roughMatch = false;
        $favorites = $this->getFavorites();
        foreach ($favorites as $item) {
            if ($item->getName() === $name) {
                return $item;
            }
            if (strtolower($item->getName()) === strtolower($name)) {
                $roughMatch = $item;
            }
        }
        if ($roughMatch) {
            return $roughMatch;
        }
        return null;
    }

    /**
     * Get the playlist with the specified id.
     *
     * @param int $id The ID of the playlist
     *
     * @return Favorite
     */
    public function getFavoriteById($id)
    {
        $favorites = $this->getFavorites();
        foreach ($favorites as $item) {
            if ($item->getId() === $id) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Get the network instance used by this controller.
     *
     * @return Network
     */
    public function getNetwork()
    {
        return $this->network;
    }

    public function powerOn()
    {
        $speakers = $this->getSpeakers();
        foreach ($speakers as $speaker) {
            $speaker->powerOn();
        }
        return $this;
    }

    public function standBy()
    {
        $speakers = $this->getSpeakers();
        foreach ($speakers as $speaker) {
            $speaker->standBy();
        }
        return $this;
    }
}
