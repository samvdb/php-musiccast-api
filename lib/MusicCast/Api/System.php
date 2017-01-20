<?php


namespace MusicCast\Api;

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
    public function deviceInfo()
    {
        return $this->get('/getDeviceInfo');
    }

    /**
     * For retrieving feature information equipped with a Device
     */
    public function features()
    {
        return $this->get('/getFeatures');
    }

    public function networkStatus()
    {
        return $this->get('/getNetworkStatus');
    }

    public function functionStatus()
    {
        return $this->get('/getFuncStatus');
    }

    public function autoPowerStandby($enable = true)
    {
        throw new \Exception('Not implemented');
    }

    public function locationInfo()
    {
        return $this->get('/getLocationInfo');
    }

    public function sendIrCode($code)
    {
        throw new \Exception('Not implemented');

        return $this->get('/sendIrCode?code=1');
    }

    public function get($path, array $parameters = array(), array $requestHeaders = array())
    {
        return parent::get('/system' . $path, $parameters, $requestHeaders);
    }
}
