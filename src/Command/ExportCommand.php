<?php

namespace App\Command;

use App\Endpoint\UdpEndpoint;
use App\Metrics\MetricProcessor;
use App\Parser\StatsDParser;
use App\Queue\QueueProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExportCommand extends Command
{
    /**
     * @var MetricProcessor
     */
    protected $metricsProcessor;

    protected static $defaultName = 'metrics:export';

    protected function configure()
    {
        $this->setDescription('export metrics to stdout');
    }

    /**
     * @param MetricProcessor $metricsProcessor
     */
    public function setMetricsProcessor(MetricProcessor $metricsProcessor): void
    {
        $this->metricsProcessor = $metricsProcessor;
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->metricsProcessor->export() as $val) {
            foreach ($val as $key => $val) {
                printf("%s %s\n", $key, $val);
            }
        }
        return Command::SUCCESS;
    }
}