<?php

namespace App\Metrics\Backend;

use Predis\Client;

class RedisBackend
{
    const LIST_LENGTH_MAX = 1000,
        PREFIX_KEYS = 'key_',
        PREFIX_LISTS = 'list_';

    /**
     * @var Client
     */
    protected $redisClient;

    /**
     * RedisBackend constructor.
     * @param Client $redisClient
     */
    public function __construct(Client $redisClient)
    {
        $this->redisClient = $redisClient;
    }

    public function beginTransaction()
    {
        $this->redisClient->multi();
    }

    public function rollbackTransaction()
    {
        $this->redisClient->discard();
    }

    public function commitTransaction()
    {
        $this->redisClient->exec();
    }

    public function set($key, $val, $ttl = null)
    {
        $this->redisClient->hset(self::PREFIX_KEYS, $key, $val);
        if ($ttl === null) {
            return;
        }

        $this->redisClient->expire($key, $ttl);
    }

    public function inc($key, $val, $ttl = null)
    {
        $value = $this->redisClient->hincrbyfloat(self::PREFIX_KEYS, $key, $val);
        if ($ttl === null) {
            return $value;
        }

        $this->redisClient->expire($key, $ttl);

        return $value;
    }

    public function appendList($key, $val, $ttl = null)
    {
        $listKey = self::PREFIX_LISTS . $key;
        $len = $this->redisClient->lpush($listKey, [$val]);
        $this->redisClient->expire($listKey, $ttl);
        if (!$len < self::LIST_LENGTH_MAX) {
            return;
        }

        $this->redisClient->lpop($listKey);
    }

    public function getList($key): array
    {
        return $this->redisClient->lrange(self::PREFIX_LISTS . $key, 0, self::LIST_LENGTH_MAX);
    }

    public function get($key)
    {
        return $this->redisClient->hget(self::PREFIX_KEYS, $key);
    }

    /**
     * @return \Generator
     */
    public function getAll(): \Generator
    {
        $firstrun = true;
        $scanResult = $this->redisClient->hscan(self::PREFIX_KEYS, 0, ['COUNT' => 100000]);
        while ($scanResult[0] !== "0" || $firstrun)
        {
            $firstrun = false;
            yield $scanResult[1];
            $scanResult = $this->redisClient->hscan(self::PREFIX_KEYS, $scanResult[0], ['COUNT' => 100000]);
        }
    }

}