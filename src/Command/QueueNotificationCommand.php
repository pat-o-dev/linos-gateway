<?php
namespace App\Command;

use App\Message\Notification;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:queue:notification',
    description: 'Mets une notification dans la queue Messenger'
)]
class QueueNotificationCommand extends Command
{
    public function __construct(private MessageBusInterface $bus)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'message',
            InputArgument::OPTIONAL,
            'Contenu de la notification',
            'Hello world!'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $msg = $input->getArgument('message');

        $this->bus->dispatch(new Notification($msg));

        $output->writeln("✅ Notification mise en queue : $msg");

        return Command::SUCCESS;
    }
}