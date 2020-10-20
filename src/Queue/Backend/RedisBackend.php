<?php

namespace App\Queue\Backend;

use Predis\Client;

class RedisBackend
{
    const PREFIX_QUEUE = 'queue:';
    /**
     * @var Client
     */
    protected $redisClient;

    /**
     * @var string
     */
    protected $queueName;

    /**
     * RedisBackend constructor.
     * @param Client $redisClient
     * @param string $queueName
     */
    public function __construct(Client $redisClient, string $queueName)
    {
        $this->redisClient = $redisClient;
        $this->queueName = self::PREFIX_QUEUE . $queueName;
    }

    public function addToQueue($workitem)
    {
        $this->redisClient->lpush($this->queueName, [$workitem]);
    }

    public function getFromQueue()
    {
        return $this->redisClient->blpop($this->queueName, 10);
    }

    /**
     * @return string
     */
    public function getQueueName(): string
    {
        return $this->queueName;
    }
}