<?php
namespace MusicCast\Api;

/**
 * For retrieving basic information of each Zone like power, volume, input and so on
 *
 * @author Damien Surot <damien@toxeek.com>
 */
class Zone extends AbstractApi
{

    
    /**
     * Returns basic information of each Zone like power, volume, input and so on
     *
     * @param string $zone target zone. Available for zones with this function
     *                  Values: "main" / "zone2" / "zone3" / "zone4"
     *
     * @return array
     */
    public function getStatus($zone)
    {
        return $this->call($zone, 'getStatus');
    }


    /**
     * Returns a list of Sound Program available in each Zone. It is possible for the list contents to
    be dynamically changed
     *
     * @param string $zone target zone. Available for zones with this function
     *                  Values: "main" / "zone2" / "zone3" / "zone4"
     *
     * @return array
     */
    public function getSoundProgramList($zone)
    {
        return $this->call($zone, 'getSoundProgramList');
    }

    /**
     * Set the power status of each Zone
     *
     * @param string $zone target zone. Available for zones with this function
     *                  Values: "main" / "zone2" / "zone3" / "zone4"
     * @param string $power power status
     *                  Values: "on" / "standby" / "toggle"
     *
     * @return array
     */
    public function setPower($zone, $power)
    {
        return $this->call($zone, 'setPower?power='.rawurlencode($power));
    }

    /**
     * Set Sleep Timer for each Zone
    With Zone B enabled Devices, target Zone is described as Master Power, but Main Zone is used to
    set it up via YXC
     *
     * @param string $zone target zone. Available for zones with this function
     *                  Values: "main" / "zone2" / "zone3" / "zone4"
     * @param string $sleep Sleep Time (unit in minutes)
     *
     * @return array
     */
    public function setSleep($zone, $sleep)
    {
        return $this->call($zone, 'setSleep?sleep='.rawurlencode($sleep));
    }

    /**
     * Set Sleep Timer for each Zone
    With Zone B enabled Devices, target Zone is described as Master Power, but Main Zone is used to
    set it up via YXC
     *
     * @param string $zone target zone. Available for zones with this function
     *                  Values: "main" / "zone2" / "zone3" / "zone4"
     * @param string $volume Specifies volume value
     *                  Value Range: calculated by minimum/maximum/step values gotten
     *                     via /system/getFeatures
     * @param string $step Specifies volume step value if the volume is “up” or “down”. If
     *                      nothing specified, minimum step value is used implicitly
     *
     * @return array
     */
    public function setVolume($zone, $volume, $step = null)
    {
        return $this->call($zone, 'setVolume?volume='.rawurlencode($volume).
            (isset($step) ? '&step='.rawurlencode($step) : ''));
    }

    /**
     * Set mute status in each Zone
     *
     * @param string $zone target zone. Available for zones with this function
     *                  Values: "main" / "zone2" / "zone3" / "zone4"
     * @param string $mute Mute status
     *
     * @return array
     */
    public function setMute($zone, $mute)
    {
        return $this->call($zone, 'setMute?mute='.rawurlencode($mute?'true':'false'));
    }

    /**
     * Selecting each Zone input
     *
     * @param string $zone target zone. Available for zones with this function
     *                  Values: "main" / "zone2" / "zone3" / "zone4"
     * @param string $input Specifies Input ID
     *                  Values: Input IDs gotten via /system/getFeatures
     * @param string $mode Specifies select mode. If no parameter is specified, actions of input
     *                   change depend on a Device’s specification
     *                   Value: "autoplay_disabled" (Restricts Auto Play of Net/USB related Inputs).
     *
     * @return array
     */
    public function setInput($zone, $input, $mode = null)
    {
        return $this->call($zone, 'setInput?input='.rawurlencode($input).
            (isset($mode) ? '&mode='.rawurlencode($mode) : ''));
    }

    /**
     * Let a Device do necessary process before changing input in a specific zone. This is valid only
     * when “prepare_input_change” exists in “func_list” found in /system/getFuncStatus.
     * MusicCast CONTROLLER executes this API when an input icon is selected in a Room, right
     * before sending various APIs (of retrieving list information etc.) regarding selecting input
     *
     * @param string $zone target zone. Available for zones with this function
     *                  Values: "main" / "zone2" / "zone3" / "zone4"
     * @param string $input Specifies Input ID
     *                  Values: Input IDs gotten via /system/getFeatures
     *
     * @return array
     */
    public function prepareInputChange($zone, $input)
    {
        return $this->call($zone, 'prepareInputChange?input='.rawurlencode($input));
    }

    /**
     * @return array|string
     */
    public function getSignalInfo($zone)
    {
        return $this->call($zone, 'getSignalInfo');
    }


    private function call($zone, $path)
    {
        return $this->get(rawurlencode($zone).'/'.$path);
    }
}
