<?php

namespace App\Parser;

use App\Metrics\GenericMetric;

class StatsDParser
{
    protected $regexes = [
        '/([^,#:]*)[,#]?(.*)?:([+0-9]*)\|(c|g|ms)\|?(@[0-9.]*)?/', #Librato, InfluxDB
    ];

    protected $labelValueSeparators = [
        '=', #Librato, InfluxDB
    ];

    public function parse($string): array
    {
        $parselines = explode("\n", $string);
        $lines = [];
        foreach ($parselines as $line) {
            foreach ($this->regexes as $key => $regex) {
                $match = null;
                if (1 === preg_match($regex, $line, $match) && count($match) >= 5) {
                    $labels = explode(',', $match[2]);
                    $labelResult = [];
                    foreach ($labels as $label) {
                        $kv = explode($this->labelValueSeparators[$key], $label);
                        if (count($kv) !== 2) {
                            continue;
                        }
                        $labelResult[$kv[0]] = $kv[1];
                    }
                    ksort($labelResult);
                    $lines[] = (new GenericMetric())
                        ->setName($match[1])
                        ->setLabels($labelResult)
                        ->setValue($match[3])
                        ->setType($match[4])
                        ->setSampling($match[6] ?? null);
                    continue;
                }
            }
        }

        return $lines;
    }
}