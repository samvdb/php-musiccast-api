<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 06/10/17
 * Time: 20:05
 */

namespace MusicCast;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Psr\SimpleCache\CacheInterface;

class Device implements LoggerAwareInterface
{
    /**
     * @var string $ip Device Ip
     */
    protected $ip;
    /**
     * @var Client $client MusicCast Client
     */
    protected $client;
    /**
     * @var CacheInterface $cache The cache object to use for the expensive multicast discover
     * to find MusicCast devices on the network.
     */
    protected $cache;

    /**
     * @var LoggerInterface $logger The logging object.
     */
    protected $logger;


    /**
     * Create a new instance.
     *
     * @param CacheInterface $cache The cache object to use for the expensive multicast discover to find
     * MusicCast devices on the network
     * @param LoggerInterface $logger The logging object
     */
    public function __construct($ip, $port = 80, CacheInterface $cache = null, LoggerInterface $logger = null)
    {
        $this->ip = $ip;
        $this->client = new Client(['host' => $ip, 'port' => $port]);
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
     * Set the logger object to use.
     *
     * @var LoggerInterface $logger The logging object
     *
     * @return static
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }


    public function getDeviceInfo()
    {
        $cacheKey = $this->getCacheKey() . __FUNCTION__;
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }
        $info = $this->client->api('system')->getDeviceInfo();
        $this->cache->set($cacheKey, $info);
        return $info;
    }

    private function getCacheKey()
    {
        return $this->ip;
    }

    public function getLocationInfo()
    {
        $cacheKey = $this->getCacheKey() . __FUNCTION__;
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }
        $info = $this->client->api('system')->getLocationInfo();
        $this->cache->set($cacheKey, $info);
        return $info;
    }

    public function getMusicCastTreeInfo()
    {
        $cacheKey = $this->getCacheKey() . __FUNCTION__;
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }
        $info = $this->client->api('system')->getMusicCastTreeInfo();
        $this->cache->set($cacheKey, $info);
        return $info;
    }

    public function getNetworkStatus()
    {
        $cacheKey = $this->getCacheKey() . __FUNCTION__;
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }
        $info = $this->client->api('system')->getNetworkStatus();
        $this->cache->set($cacheKey, $info);
        return $info;
    }
}
