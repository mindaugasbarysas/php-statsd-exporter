<?php

namespace App\Metrics;

class GenericMetric
{
    const TYPE_GAUGE = 'g',
        TYPE_TIMING = 'ms',
        TYPE_SET = 's',
        TYPE_COUNT = 'c';

    protected $name;
    protected $value;
    protected $labels = [];

    protected $type;
    protected $sampling;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return GenericMetric
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return GenericMetric
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * @param array $labels
     * @return GenericMetric
     */
    public function setLabels(array $labels): GenericMetric
    {
        $this->labels = $labels;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return GenericMetric
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSampling()
    {
        return $this->sampling;
    }

    /**
     * @param mixed $sampling
     * @return GenericMetric
     */
    public function setSampling($sampling)
    {
        $this->sampling = $sampling;
        return $this;
    }
}