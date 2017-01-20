<?php
namespace MusicCast\Api;

class Zone extends AbstractApi
{

    
    /**
     * Returns the status of the given zone
     *
     * @param string $zone
     *
     * @return array
     */
    public function status($zone)
    {
        return $this->call($zone, 'getStatus');
    }


    private function call($zone, $path)
    {
        return $this->get(rawurlencode($zone).'/'.$path);
    }
}
