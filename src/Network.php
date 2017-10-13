<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 30/09/17
 * Time: 11:56
 */

namespace MusicCast;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Psr\SimpleCache\CacheInterface;

class Network implements LoggerAwareInterface
{

    /**
     * @var Speaker[]|null $speakers Speakers that are available on the current network.
     */
    protected $speakers;

    /**
     * @var Playlist[]|null $playlists Playlists that are available on the current network.
     */
    protected $playlists;

    /**
     * @var CacheInterface $cache The cache object to use for the expensive multicast discover to find
     * MusicCast devices on the network.
     */
    protected $cache;

    /**
     * @var LoggerInterface $logger The logging object.
     */
    protected $logger;

    /**
     * @var $multicastAddress string The multicast address to use for SSDP discovery.
     */
    protected $multicastAddress = "239.255.255.250";

    /**
     * @var $networkInterface string The network interface to use for SSDP discovery.
     */
    protected $networkInterface;
    /**
     * @var Favorite[]|null $favorites Favorites that are available on the current network.
     */
    protected $favorites;

    /**
     * Create a new instance.
     *
     * @param CacheInterface $cache The cache object to use for the expensive multicast discover to find
     * MusicCast devices on the network
     * @param LoggerInterface $logger The logging object
     */
    public function __construct(CacheInterface $cache = null, LoggerInterface $logger = null)
    {
        if ($cache === null) {
            $cache = new Cache();
        }
        $this->cache = $cache;

        if ($logger === null) {
            $logger = new NullLogger;
        }
        $this->logger = $logger;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return
     */
    public function getMulticastAddress()
    {
        return $this->multicastAddress;
    }

    /**
     * @param $multicastAddress
     */
    public function setMulticastAddress($multicastAddress)
    {
        $this->multicastAddress = $multicastAddress;
    }

    /**
     * @return
     */
    public function getNetworkInterface()
    {
        return $this->networkInterface;
    }

    /**
     * @param $networkInterface
     */
    public function setNetworkInterface($networkInterface)
    {
        $this->networkInterface = $networkInterface;
    }

    /**
     * Get a Controller instance from the network.
     *
     * Useful for managing playlists/alarms, as these need a controller but it doesn't matter which one.
     *
     * @return Controller|null
     */
    public function getController()
    {
        $controllers = $this->getControllers();
        if ($controller = reset($controllers)) {
            return $controller;
        }
    }

    /**
     * Get all the coordinators on the network.
     *
     * @return Controller[]
     */
    public function getControllers()
    {
        $controllers = [];
        $speakers = $this->getSpeakers();
        foreach ($speakers as $speaker) {
            if (!$speaker->isCoordinator()) {
                continue;
            }
            $controllers[$speaker->getDevice()->getIp()] = new Controller($speaker, $this);
        }
        return $controllers;
    }

    /**
     * Get all the speakers on the network.
     *
     * @return
     */
    public function getSpeakers()
    {
        if (is_array($this->speakers)) {
            return $this->speakers;
        }

        $this->logger->info("creating speaker instances");

        $cacheKey = $this->getCacheKey();

        if ($this->cache->has($cacheKey)) {
            $this->logger->info("getting device info from cache");
            $devices = $this->cache->get($cacheKey);
        } else {
            $devices = $this->getDevices();

            # Only cache the devices if we actually found some
            if (count($devices) > 0) {
                $this->cache->set($cacheKey, $devices);
            }
        }

        if (count($devices) < 1) {
            throw new \RuntimeException("No devices found on the current network");
        }


        # Get the MusicCast devices from 1 speaker
        $ip = reset($devices);
        $device = new Device($ip, 80);
        $this->logger->notice("Getting devices info from: {$ip}");
        $treeInfo = $device->getMusicCastTreeInfo();
        $this->speakers = [];
        foreach ($treeInfo['mac_address_list'] as $addr) {
            $ip = $addr['ip_address'];
            $speaker = new Speaker(new Device($ip, 80));
            $this->speakers[$ip] = $speaker;
        }

        return $this->speakers;
    }

    protected function getCacheKey()
    {
        $cacheKey = "devices";

        $cacheKey .= "_" . gettype($this->networkInterface);
        $cacheKey .= "_" . $this->networkInterface;

        $cacheKey .= "_" . $this->multicastAddress;

        return $cacheKey;
    }

    /**
     * Get all the devices on the current network.
     *
     * @return string[] An array of ip addresses
     */
    protected function getDevices()
    {
        $this->logger->info("discovering devices...");

        $port = 1900;

        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

        $level = getprotobyname("ip");

        socket_set_option($sock, $level, IP_MULTICAST_TTL, 2);

        if ($this->networkInterface !== null) {
            socket_set_option($sock, $level, IP_MULTICAST_IF, $this->networkInterface);
        }

        $data = "M-SEARCH * HTTP/1.1\r\n";
        $data .= "HOST: {$this->multicastAddress}:1900\r\n";
        $data .= "MAN: \"ssdp:discover\"\r\n";
        $data .= "MX: 2\r\n";
        $data .= "ST: urn:schemas-upnp-org:device:MediaRenderer:1\r\n";


        $this->logger->debug($data);

        socket_sendto($sock, $data, strlen($data), null, $this->multicastAddress, $port);

        $read = [$sock];
        $write = [];
        $except = [];
        $name = null;
        $port = null;
        $tmp = "";

        $response = "";
        while (socket_select($read, $write, $except, 1)) {
            socket_recvfrom($sock, $tmp, 2048, null, $name, $port);
            $response .= $tmp;
        }

        $this->logger->debug($response);

        $devices = [];
        foreach (explode("\r\n\r\n", $response) as $reply) {
            if (!$reply) {
                continue;
            }

            $data = [];
            foreach (explode("\r\n", $reply) as $line) {
                if (!$pos = strpos($line, ":")) {
                    continue;
                }
                $key = strtolower(substr($line, 0, $pos));
                $val = trim(substr($line, $pos + 1));
                $data[$key] = $val;
            }
            $devices[] = $data;
        }

        $return = [];
        $unique = [];
        foreach ($devices as $device) {
            if ($device["st"] !== "urn:schemas-upnp-org:device:MediaRenderer:1") {
                continue;
            }
            if (in_array($device["usn"], $unique)) {
                continue;
            }
            $this->logger->info("found device: {usn}", $device);

            $url = parse_url($device["location"]);
            $ip = $url["host"];

            $return[] = $ip;
            $unique[] = $device["usn"];
        }

        return $return;
    }

    /**
     * Get the coordinator for the specified ip address.
     *
     * @param string $ip The ip address of the speaker
     *
     * @return Controller|null
     */
    public function getControllerByIp($ip)
    {
        $speakers = $this->getSpeakers();
        if (!array_key_exists($ip, $speakers)) {
            throw new \InvalidArgumentException("No speaker found for the IP address '{$ip}'");
        }
        $group = $speakers[$ip]->getGroup();
        foreach ($this->getControllers() as $controller) {
            if ($controller->getGroup() === $group) {
                return $controller;
            }
        }
    }
}
