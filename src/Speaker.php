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
}
