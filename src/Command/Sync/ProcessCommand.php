<?php

namespace App\Command\Sync;


use App\Service\SyncJob\SyncJobProcessor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:sync:process',
    description: 'Add a short description for your command',
)]
class ProcessCommand extends Command
{
    public function __construct(
        private SyncJobProcessor $jobs
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('type', InputArgument::OPTIONAL, 'Job Type')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Number of jobs', 2)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $jobType = $input->getArgument('type');
        $jobsLimit = (int) $input->getOption('limit');

        $criteria = ['state' => 'open'];
        if($jobType) {
            $criteria['type'] = $jobType;
        }

        $process = $this->jobs->process($criteria, $jobsLimit);
        if(!$process) {
            $io->fail('Process error');
            return Command::FAILURE;
        }
        if (!$process['count']) {
            $io->success('No jobs to process');
            return Command::SUCCESS;
        }

        $io->success(" {$process['done']}/{$process['count']} jobs processed");
        if($process['fail']) {
             $io->error(" {$process['fail']}/{$process['count']} jobs processed");
        }
        return Command::SUCCESS;
    }
}
