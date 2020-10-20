<?php

namespace App\Command;

use App\Endpoint\UdpEndpoint;
use App\Parser\StatsDParser;
use App\Queue\QueueProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UdpServerCommand extends Command
{
    /**
     * @var QueueProcessor
     */
    protected $queueProcessor;

    protected static $defaultName = 'statsd:listen-udp';

    public function setQueueProcessor(QueueProcessor $queueProcessor)
    {
        $this->queueProcessor = $queueProcessor;
    }

    protected function configure()
    {
        $this->setDescription('start udp packet listener')
        ->addOption('port', 'P', InputOption::VALUE_OPTIONAL, 'port to listen on [9125]')
            ->addOption('host', 'H', InputOption::VALUE_OPTIONAL, 'host to listen on [0.0.0.0]')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queueProvider = $this->queueProcessor;
        (new UdpEndpoint())
            ->listen(
                $input->getOption('host') ?? '0.0.0.0',
                $input->getOption('port') ?? 9125,
                function ($string) use ($queueProvider) {
                    $queueProvider->push($string);
                }
        );


        return Command::SUCCESS;
    }
}