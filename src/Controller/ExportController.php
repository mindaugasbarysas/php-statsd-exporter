<?php

namespace App\Controller;

use App\Metrics\MetricProcessor;

class ExportController
{
    /**
     * @var MetricProcessor
     */
    protected $metricsProcessor;

    /**
     * ExportController constructor.
     * @param MetricProcessor $metricsProcessor
     */
    public function __construct(MetricProcessor $metricsProcessor)
    {
        $this->metricsProcessor = $metricsProcessor;
    }

    public function getMetrics()
    {
        header('Content-Type: text/plain');
        foreach ($this->metricsProcessor->export() as $val) {
            foreach ($val as $key => $val) {
                printf("%s %s\n", $key, $val);
            }
        }
        die();
    }
}