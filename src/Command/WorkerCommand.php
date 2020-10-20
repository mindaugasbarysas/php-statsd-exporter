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

class WorkerCommand extends Command
{
    /**
     * @var QueueProcessor
     */
    protected $queueProcessor;

    protected static $defaultName = 'metrics:process';

    public function setQueueProcessor(QueueProcessor $queueProcessor)
    {
        $this->queueProcessor = $queueProcessor;
    }

    protected function configure()
    {
        $this->setDescription('do work!');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        while (true) {
            $this->queueProcessor->consume();
        }

        return Command::SUCCESS;
    }
}