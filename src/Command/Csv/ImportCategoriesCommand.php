<?php

namespace App\Command\Csv;

use App\Entity\SyncJob;
use App\Dto\CategoryDto;
use App\Message\JobMessage;
use App\Command\Traits\HasParams;
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
    name: 'app:csv:import:categories',
    description: 'Import Categories CSV to buffer',
)]
class ImportCategoriesCommand extends Command
{
    use HasParams;
    
    public function __construct(
        private readonly EntityManagerInterface $em,
        private MessageBusInterface $bus
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('filename', InputArgument::OPTIONAL, 'Csv namename')
            ->addOption('delimiter', null, InputOption::VALUE_REQUIRED, 'CSV delimiter', ',')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $jobType = 'category_import';
        $filename = $input->getArgument('filename');
        $csvDir = $this->param('csv_dir');
        $csvDelimiter = $input->getOption('delimiter');

        $csvFile = $filename ? $csvDir.'/'.$filename : $csvDir.'/categories.csv';
        if (!file_exists($csvFile)) {
            $io->error("CSV file not found: $csvFile");
            return Command::FAILURE;
        }
        
        if (($handle = fopen($csvFile, 'r')) === false) {
            $io->error("CSV file cannot be open : $csvFile");
            return Command::FAILURE;
        }

        $headers = fgetcsv($handle, 0, $csvDelimiter);
        if ($headers === false) {
            $io->error("CSV file  has no header row : $csvFile");
            return Command::FAILURE;
        }

        $count = 0;
        while (($row = fgetcsv($handle, 0, $csvDelimiter)) !== false) {
            $data = array_combine($headers, $row);
     
            $catDto = CategoryDto::fromArray($data);
            $syncJob = new SyncJob(
                type: $jobType, 
                objectId: $catDto->id, 
                payload: $catDto->toArray(), 
                source:'store_alpha', 
                origin: 'csv:'.$filename,
                priority: 0
            );

            $this->em->persist($syncJob);
            $count++;
        }

        fclose($handle);

        $this->em->flush();

         $this->bus->dispatch(new JobMessage($jobType));

        $io->success("$count categories queued into sync_job");

        return Command::SUCCESS;
    }
}
