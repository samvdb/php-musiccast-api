<?php
namespace MusicCast\Api;

/**
 * Events are to notify Devices’ status/setup changes immediately to external applications. Events
 * are spread out as UDP unicast.
 *
 * @author Sam Van der Borght <samvanderborght@gmail.com>
 */
class Event extends AbstractApi
{
    /**
     *
     * Event notification timeouts in 10 minutes if no further event request is sent from an IP address
     * set as event receiving device. If another request is made within 10 minutes of previous request,
     * the timeout duration is reset and extended.
     * Event receiving port will be overwritten if a different port number is sent as a request by the
     * registered device using X-AppPort.
     *
     * The default port is 41100
     * @param int $port
     * @param string $appName
     * @param string $version
     * @return array|string
     */
    public function subscribe($port = 41100, $appName = 'MusicCast', $version = '1')
    {
        $headers = [
            'X-AppName' => sprintf('%s/%s', $appName, $version),
            'X-AppPort' => sprintf('%s', $port),
        ];

        //        $plugin = $this->client->getPlugin(AddBasePath::class);
        //        $this->client->removePlugin(AddBasePath::class);
        return $this->get('', $headers);
        //        $this->client->addPlugin($plugin);
    }
}
