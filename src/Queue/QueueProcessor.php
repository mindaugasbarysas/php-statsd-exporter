<?php

namespace App\Queue;

use App\Metrics\MetricProcessor;
use App\Parser\StatsDParser;
use App\Queue\Backend\RedisBackend;

class QueueProcessor
{
    /**
     * @var RedisBackend
     */
    protected $backend;

    /**
     * @var StatsDParser
     */
    protected $parser;

    /**
     * @var MetricProcessor
     */
    protected $metricProcessor;

    /**
     * QueueProcessor constructor.
     * @param RedisBackend $backend
     * @param StatsDParser $parser
     * @param MetricProcessor $metricProcessor
     */
    public function __construct(RedisBackend $backend, StatsDParser $parser, MetricProcessor $metricProcessor)
    {
        $this->backend = $backend;
        $this->parser = $parser;
        $this->metricProcessor = $metricProcessor;
    }

    public function push($workitem)
    {
        $this->backend->addToQueue($workitem);
    }

    public function consume()
    {
        $queue = null;
        while ($queue === null) {
            $queue = $this->backend->getFromQueue();
        }
        foreach ($queue as $thing) {
            if ($thing === null) continue;
            if ($thing === $this->backend->getQueueName()) continue;
            foreach ($this->parser->parse($thing) as $item) {
                $this->metricProcessor->process($item);
            }
        }
    }

}