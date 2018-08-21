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

/**
 * Class Device
 * @package MusicCast
 * @author Damien Surot <damien@toxeek.com>
 */
class Device implements LoggerAwareInterface
{
    /**
     * @var string $ip Device Ip
     */
    protected $ip;
    /**
     * @var Client $client MusicCast Client
     */
    private $client;
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
     * Device constructor.
     * @param $ip
     * @param int $port
     * @param CacheInterface|null $cache
     * @param LoggerInterface|null $logger
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


    public function call($api, $method, array $args = [])
    {
        return call_user_func_array(array($this->client->api($api), $method), $args);
    }


    public function getDeviceInfo()
    {
        $cacheKey = $this->getCacheKey() . __FUNCTION__;
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }
        $info = $this->call('system', 'getDeviceInfo');
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
        $info = $this->call('system', 'getLocationInfo');
        $this->cache->set($cacheKey, $info);
        return $info;
    }

    public function getMusicCastTreeInfo()
    {
        $cacheKey = $this->getCacheKey() . __FUNCTION__;
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }
        $info = $this->call('system', 'getMusicCastTreeInfo');
        $this->cache->set($cacheKey, $info);
        return $info;
    }

    public function getNetworkStatus()
    {
        $cacheKey = $this->getCacheKey() . __FUNCTION__;
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }
        $info = $this->call('system', 'getNetworkStatus');
        $this->cache->set($cacheKey, $info);
        return $info;
    }

    public function getUuid()
    {
        return $this->getDeviceInfo()['device_id'];
    }
}
