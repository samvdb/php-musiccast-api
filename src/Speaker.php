<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 06/10/17
 * Time: 20:01
 */

namespace MusicCast;

/**
 * Represents an individual MusicCast speaker, to allow volume, equalisation, and other settings to be managed.
 * @author Damien Surot <damien@toxeek.com>
 */
class Speaker
{
    protected $model;
    protected $name;
    protected $device;
    /**
     * @var
     */
    protected $playlists;

    /**
     * @var
     */
    protected $favorites;

    const NO_GROUP = "NoGroup";

    /**
     * Create an instance of the Speaker class.
     *
     * @param Device $device the ip address that the speaker is listening on
     */
    public function __construct($device)
    {
        $this->device = $device;
        $this->model = $device->getDeviceInfo()['model_name'];
        $this->name = $device->getNetworkStatus()['network_name'];
    }

    public function call($api, $method, array $args = [])
    {
        return $this->device->call($api, $method, $args);
    }

    public function getIp()
    {
        return $this->device->getIp();
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the uuid of the group this speaker is a member of.
     *
     * @return string
     */
    public function getGroup()
    {
        $group = $this->call('dist', 'getDistributionInfo')['group_id'];
        if (is_numeric($group) && intval($group == 0)) {
            $group = self::NO_GROUP;
        }
        return $group;
    }

    /**
     * Get the name of the group this speaker is a member of.
     *
     * @return string
     */
    public function getGroupName()
    {
        $group = $this->call('dist', 'getDistributionInfo')['group_name'];
        return $group;
        //return str_replace('(Linked) ', '', $group);
    }

    /**
     * Check if this speaker is the coordinator of it's current group.
     *
     * @return bool
     */
    public function isCoordinator()
    {
        $role = $this->call('dist', 'getDistributionInfo')['role'];
        return $role == 'server' || $this->getGroup() == Speaker::NO_GROUP;
    }

    /**
     * Get the uuid of this speaker.
     *
     * @return string The uuid of this speaker
     */
    public function getUuid()
    {
        return $this->device->getUuid();
    }

    /**
     * Get the current volume of this speaker.
     *
     * @return int
     */
    public function getVolume()
    {
        return (int)$this->call('zone', 'getStatus', ['main'])['volume'];
    }

    /**
     * Adjust the volume of this speaker to a specific value.
     *
     * @param int $volume The amount to set the volume to between 0 and 100
     *
     * @return static
     */
    public function setVolume($volume)
    {
        $this->call('zone', 'setVolume', ['main', $volume]);
        return $this;
    }


    /**
     * Adjust the volume of this speaker by a relative amount.
     *
     * @param int $adjust The amount to adjust by between -100 and 100
     *
     * @return static
     */
    public function adjustVolume($adjust)
    {
        $this->call('zone', 'setVolume', ['main', $adjust > 0 ? 'up' : 'down', abs($adjust)]);
        return $this;
    }

    /**
     * Check if this speaker is currently muted.
     *
     * @return bool
     */
    public function isMuted()
    {
        return (bool)$this->call('zone', 'getStatus', ['main'])['mute'];
    }

    /**
     * Unmute this speaker.
     *
     * @return static
     */
    public function unmute()
    {
        return $this->mute(false);
    }

    /**
     * Mute this speaker.
     *
     * @param bool $mute Whether the speaker should be muted or not
     *
     * @return static
     */
    public function mute($mute = true)
    {
        $this->call('zone', 'setMute', ['main', $mute]);
        return $this;
    }

    /**
     * Power On this speaker.
     *
     * @return static
     */
    public function powerOn()
    {
        return $this->setPower('on');
    }


    /**
     * Power On this speaker.
     *
     * @return bool
     */
    public function isPowerOn()
    {
        return $this->call('zone', 'getStatus', ['main'])["power"] == 'on';
    }

    /**
     * Stand by this speaker.
     *
     * @return static
     */
    public function standBy()
    {
        return $this->setPower('standby');
    }


    /**
     * Power toggle this speaker.
     *
     * @return static
     */
    public function powerToggle()
    {
        return $this->setPower('toggle');
    }

    private function setPower($power)
    {
        $this->call('zone', 'setPower', ['main', $power]);
        return $this;
    }

    /**
     * Get the currently active media info.
     *
     * @return array
     */
    public function getInput()
    {
        return $this->call('zone', 'getStatus', ['main'])['input'];
    }


    public function isStreaming()
    {
        $input = 'input' . $this->call('zone', 'getStatus', ['main'])['input'];
        return $input == "tuner" || stripos($input, "hdmi") != false || stripos($input, "av") != false
            || stripos($input, "aux") != false || stripos($input, "audio") != false;
    }

    /**
     * Get List of available input
     *
     * @return array
     */
    public function getInputList()
    {
        $tags = $this->call('system', 'getTag');
        $inputs = $tags['input_list'];
        $return = [];
        foreach ($inputs as $input) {
            $return[] = $input['id'];
        }
        return $return;
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
}
