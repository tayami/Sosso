<?php

namespace Tayami\Sosso\Core;

class Sso
{
    /**
     * @var \Redis
     */
    private static $redis;

    /**
     * @var
     */
    private static $cryptKey;

    /**
     * @var
     */
    private static $expire;

    public static function init(\Redis $redis, $cryptKey = '', $expire = 86400)
    {
        self::$redis    = $redis;
        self::$cryptKey = $cryptKey;
        self::$expire   = abs((int)$expire);
    }

    /**
     * @return \Redis
     * @throws \Exception
     */
    public static function driver()
    {
        if (!(self::$redis instanceof \Redis)) {
            throw new \Exception('please init redis');
        }

        return self::$redis;
    }

    public static function expire()
    {
        return self::$expire;
    }

    public static function cryptKey()
    {
        return self::$cryptKey;
    }
}