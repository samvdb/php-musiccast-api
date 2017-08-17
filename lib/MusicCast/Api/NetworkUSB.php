<?php


namespace MusicCast\Api;

/**
 * APIs in regard to Network/USB related setting and getting information
 * Target Inputs: USB / Network related ones (Server / Net Radio / Pandora / Spotify / AirPlay etc.)
 * 
 * @author Sam Van der Borght <samvanderborght@gmail.com>
 */
class Network extends AbstractApi
{
    private function call($path)
    {
        return $this->get('/system' . $path);
    }
}
