<?php

namespace App\Command\Sync;


use App\Message\JobMessage;
use App\Repository\SyncJobRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:sync:dispatch',
    description: 'Create new dispatch',
)]
class DispatchCommand extends Command
{
    public function __construct(
        private MessageBusInterface $bus,
        private SyncJobRepository $syncJobRepository,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $type = 'category_import';
        if($this->syncJobRepository->hasOpenJobs($type)) {
            $this->bus->dispatch(new JobMessage(type:$type));
        }

        return Command::SUCCESS;
    }
}
