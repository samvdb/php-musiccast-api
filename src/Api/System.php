<?php


namespace MusicCast\Api;

/**
 * @author Sam Van der Borght <samvanderborght@gmail.com>
 * @author Damien Surot <damien@toxeek.com>
 */
/**
 * @author Sam Van der Borght <samvanderborght@gmail.com>
 */
class System extends AbstractApi
{
    /**
     * For retrieving basic information of a Device
     *
     * @return array
     */
    public function getDeviceInfo()
    {
        return $this->call('getDeviceInfo');
    }

    /**
     * For retrieving feature information equipped with a Device
     *
     * @return array
     */
    public function getFeatures()
    {
        return $this->call('getFeatures');
    }

    /**
     * For retrieving network related setup / information
     *
     * @return array
     */
    public function getNetworkStatus()
    {
        return $this->call('getNetworkStatus');
    }

    /**
     * For retrieving setup/information of overall system function. Parameters are readable only when
     * corresponding functions are available in “func_list” of /system/getFeatures
     *
     * @return array
     */
    public function getFuncStatus()
    {
        return $this->call('getFuncStatus');
    }

    /**
     * For setting Auto Power Standby status. Actual operations/reactions of enabling Auto Power
     * Standby depend on each Device
     *
     * @param bool $enable Specifies Auto Power Standby status
     * @return array
     */
    public function setAutoPowerStandby($enable = true)
    {
        return $this->call('setAutoPowerStandby?enable=' . rawurlencode($enable ? 'true' : 'false'));
    }

    /**
     * For retrieving Location information
     * @return array
     */
    public function getLocationInfo()
    {
        return $this->call('getLocationInfo');
    }

    /**
     * For sending specific remote IR code. A Device is operated same as remote IR code reception. But
     * continuous IR code cannot be used in this command. Refer to each Device’s IR code list for details
     * @param string $code IR code in 8-digit hex
     * @return array
     */
    public function sendIrCode($code)
    {
        return $this->call('sendIrCode?code=' . rawurlencode($code));
    }

    /**
     * @return array
     */
    public function getNameText()
    {
        return $this->call('getNameText');
    }

    /**
     * @param string $type
     * @return array
     */
    public function isNewFirmwareAvailable($type = 'network')
    {
        return $this->call('isNewFirmwareAvailable?type=' . rawurlencode($type));
    }

    /**
     * @return array
     */
    public function getTag()
    {
        return $this->call('getTag');
    }

    /**
     * @return array
     */
    public function getDisklavierSettings()
    {
        return $this->call('getDisklavierSettings');
    }


    /**
     * @return array
     */
    public function getMusicCastTreeInfo()
    {
        return $this->call('getMusicCastTreeInfo');
    }

    /**
     * @param $path
     * @return array
     */
    private function call($path)
    {
        return $this->get('/system/' . $path);
    }
}
