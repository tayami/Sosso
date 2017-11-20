<?php

namespace tayami\Sosso\core;

class Sso
{
    /**
     * @var \Redis
     */
    private static $redis;

    /**
     * @var
     */
    private static $expire;

    public static function init(\Redis $redis, $expire = 86400)
    {
        self::$redis = $redis;
        self::$expire = abs((int)$expire);
    }

    /**
     * @return \Redis
     * @throws \Exception
     */
    public static function driver()
    {
        if (!(self::$redis instanceof \Redis)) {
            throw new \Exception('not init');
        }

        return self::$redis;
    }

    public static function expire()
    {
        return self::$expire;
    }
}