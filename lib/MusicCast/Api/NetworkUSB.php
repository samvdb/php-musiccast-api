<?php


namespace MusicCast\Api;

/**
 * APIs in regard to Network/USB related setting and getting information
 * Target Inputs: USB / Network related ones (Server / Net Radio / Pandora / Spotify / AirPlay etc.)
 *
 * @author Sam Van der Borght <samvanderborght@gmail.com>
 * @author Damien Surot <damien@toxeek.com>
 */
class NetworkUSB extends AbstractApi
{

    /**
     *
     * @return array
     */
    public function getPresetInfo()
    {
        return $this->call('getPresetInfo');
    }

    private function call($path)
    {
        return $this->get('/netusb/' . $path);
    }
}
