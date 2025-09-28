<?php

namespace App\Command\Prestashop;

use App\Service\Prestashop\CategoryImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:prestashop-import-categories',
    description: 'Prepare import categories from Prestashop',
)]
class ImportCategoriesCommand extends Command
{
    public function __construct(private CategoryImporter $importer)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $response = $this->importer->import();
        if ($response) {
            $io->success('Prestashop Import Categories.');
            return Command::SUCCESS;
        }

        $io->error('Prestashop Import Categories Fail');
        return Command::FAILURE;
    }
}
