<?php

namespace App\Command\Sync;

use App\Dto\CategoryDto;
use App\Repository\SyncJobRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        private readonly EntityManagerInterface $em,
        private SyncJobRepository $syncJobRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('type', InputArgument::OPTIONAL, 'Job Type')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Number of jobs', 10)
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
        $jobs = $this->syncJobRepository->findBy($criteria, ['priority' => 'DESC'], $jobsLimit);
        
        if (!$jobs) {
            $io->success('No jobs to process');
            return Command::SUCCESS;
        }

        foreach($jobs as $job) {
            $job->markPending();
            try {
                switch($job->getType()) {
                    case 'category_import':
                        $dto = $job->getPayloadDto(CategoryDto::class);
                        #TODO CategoryImporter
                        $io->writeln("Processed Category #{$dto->id} ({$dto->name})");
                         break;
                }
                $job->markDone();

            } catch(\Throwable $e) {
                if ($job->isMaxTriesReached()) {
                    $job->markError();
                } else {
                    $job->markRetry();
                }
                $io->error("Job {$job->getId()} failed: ". $e->getMessage());
            }
        }
        $this->em->flush();

        $io->success(count($jobs)." jobs processed");

        return Command::SUCCESS;
    }
}
