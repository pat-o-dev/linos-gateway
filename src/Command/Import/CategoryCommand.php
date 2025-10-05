<?php

namespace App\Command\Import;

use App\Command\Traits\HasParams;
use App\Repository\SyncJobRepository;
use App\Service\CategoryImporter;
use App\Service\SyncJob\SyncJobProcessor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:import:category',
    description: 'Import Categories CSV to buffer',
)]
class CategoryCommand extends Command
{
    use HasParams;
    
    public function __construct(
        private readonly EntityManagerInterface $em,
        private SyncJobRepository $syncJobRepository,
        private CategoryImporter $categoryImporter,
        private SyncJobProcessor $syncJobProcessor,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::OPTIONAL, 'job id')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
       
        $jobId = $input->getArgument('id') ?? 0;

        // get syncJob
        if($jobId > 0) {
            $job = $this->syncJobRepository->find($jobId);
            $category = $this->categoryImporter->ImportJob($job);
            dump($category);
            $io->success("Jobs id : $jobId");
        }
        else {
            $report = $this->syncJobProcessor->process(limit: 5);
            dump($report);
        }
       
        return Command::SUCCESS;
    }
}
