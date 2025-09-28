<?php

namespace App\Command\Prestashop;

use App\Service\Prestashop\ProductImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:prestashop-import-products',
    description: 'Prepare import products from Prestashop',
)]
class ImportProductsCommand extends Command
{
    public function __construct(private ProductImporter $importer)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $response = $this->importer->import();
        if ($response) {
            $io->success('Prestashop Import Products.');
            return Command::SUCCESS;
        }

        $io->error('Prestashop Import Products Fail');
        return Command::FAILURE;
    }
}
