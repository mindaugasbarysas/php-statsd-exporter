<?php

namespace App\Metrics;

use App\Metrics\Backend\RedisBackend;

class MetricProcessor
{
    /**
     * @var RedisBackend
     */
    protected $backend;

    /**
     * @var int
     */
    protected $ttl;

    /**
     * @var array
     */
    protected $quantiles = [0.5, 0.9, 0.99];

    /**
     * MetricProcessor constructor.
     * @param RedisBackend $backend
     * @param array|null $quantiles
     * @param int|null $ttl
     */
    public function __construct(RedisBackend $backend, ?array $quantiles, ?int $ttl = null)
    {
        $this->backend = $backend;
        $this->ttl = $ttl;
        if (is_array($quantiles)) {
            $this->quantiles = $quantiles;
        }
    }

    public function generateKey(GenericMetric $genericMetric): string
    {
        $labels = [];
        foreach ($genericMetric->getLabels() as $key => $label) {
            if (is_numeric(substr($key,0,1))) { continue; } //skip invalid labels
            $labels[] = sprintf('%s="%s"', $key, str_replace([',', '"'], '', $label));
        }
        return sprintf(
            '%s{%s}',
            str_replace('.', '_', $genericMetric->getName()),
            join(',', $labels)
        );
    }

    public function process(GenericMetric $genericMetric)
    {
        switch ($genericMetric->getType()) {
            case GenericMetric::TYPE_TIMING:
                // need to calculate sum buckit
                $genericMetricDerivative = clone $genericMetric;

                $this->backend->inc($this->generateKey($genericMetricDerivative->setName(sprintf('%s_sum', $genericMetric->getName()))), $genericMetric->getValue() / 1000, $this->ttl);
                $this->backend->inc($this->generateKey($genericMetricDerivative->setName(sprintf('%s_count', $genericMetric->getName()))), 1, $this->ttl);
                $this->backend->appendList($this->generateKey($genericMetric), $genericMetric->getValue() / 1000, $this->ttl);
                $valueList = $this->backend->getList($this->generateKey($genericMetric));
                sort($valueList, SORT_NUMERIC);
                foreach ($this->quantiles as $quantile) {
                    $genericMetricDerivative = clone $genericMetric;
                    $genericMetricDerivative->setLabels(array_merge($genericMetricDerivative->getLabels(), ['quantile' => "$quantile"]));
                    $genericMetricDerivative->setValue($this->getQuantile($quantile, $valueList));
                    $this->backend->set($this->generateKey($genericMetricDerivative), $genericMetricDerivative->getValue(), $this->ttl);
                }
                break;
            case GenericMetric::TYPE_GAUGE:
                $prefix = substr($genericMetric->getValue(), 0, 1);
                if (is_numeric($prefix)) {
                    $this->backend->set($this->generateKey($genericMetric), $genericMetric->getValue(), $this->ttl);
                    break;
                }
                if ($prefix !== '+')
                {
                    break;
                }
            default:
                $this->backend->inc($this->generateKey($genericMetric), (float)str_replace('+', '', $genericMetric->getValue()));
        }
    }

    public function getQuantile($quantile, &$values)
    {
        return $values[(int)$quantile * (count($values) +1)];
    }

    public function export()
    {
        foreach ($this->backend->getAll() as $out) {
            yield $out;
        }
    }
}