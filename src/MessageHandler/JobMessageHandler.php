<?php

namespace App\MessageHandler;

use App\Message\JobMessage;
use App\Repository\SyncJobRepository;
use App\Service\SyncJob\SyncJobProcessor;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class JobMessageHandler
{
    public function __construct(
        private MessageBusInterface $bus,
        private SyncJobProcessor $syncJobProcessor,
        private SyncJobRepository $syncJobRepository,
    ) {
    }

    public function __invoke(JobMessage $message): void
    {
        $criteria = [];
        $limit = 5;
        $report = $this->syncJobProcessor->process($criteria, $limit);
        dump($report);
        $type = $message->getType();
        // add new dispatch if have more jobs of same type
        if($this->syncJobRepository->hasOpenJobs($type)) {
            $this->bus->dispatch(new JobMessage(type:$type));
        }
    }
}