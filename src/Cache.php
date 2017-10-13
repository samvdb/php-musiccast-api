<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 08/10/17
 * Time: 10:38
 */

namespace MusicCast;

use Cache\Adapter\Doctrine\DoctrineCachePool;
use Doctrine\Common\Cache\FilesystemCache;

/**
 * A cache provider.
 */
class Cache extends DoctrineCachePool
{
    const MINUTE = 60;
    const HOUR = self::MINUTE * 60;
    const DAY = self::HOUR * 60;

    public function __construct()
    {
        $cache = new FilesystemCache(sys_get_temp_dir() . DIRECTORY_SEPARATOR . "musicast");
        parent::__construct($cache);
    }
}
