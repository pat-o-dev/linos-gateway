<?php

namespace App\Command\Import;

use App\Command\Traits\HasParams;
use App\Repository\SyncJobRepository;
use App\Service\CategoryImporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
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
       
        $jobId = $input->getArgument('id');

        // get syncJob
        $job = $this->syncJobRepository->find($jobId);
        $category = $this->categoryImporter->ImportJob($job);
        dd($category);
        // get payload

        // call import


        $io->success("Jobs id : $jobId");

        return Command::SUCCESS;
    }
}
