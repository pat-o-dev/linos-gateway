<?php

namespace App\Entity;

use App\Repository\SyncJobRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SyncJobRepository::class)]
class SyncJob
{
    public const MAX_TRY = 3;

    public const DELAY_TRY = [
        0 => '+1 minutes',
        1 => '+5 minutes',
        2 => '+30 minutes',
        3 => '+1 hour',
        4 => '+3 hour',
        5 => '+6 hour',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64, index: true)]
    private ?string $type = null;

    #[ORM\Column(nullable: true, index: true)]
    private ?int $objectId = null;

    #[ORM\Column(type: Types::JSON)]
    private ?array $payload = null;

    #[ORM\Column(length: 64, index: true)]
    private ?string $source = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $origin = null;

    #[ORM\Column(length: 24, index: true)]
    private ?string $state = 'open';

    #[ORM\Column(index: true)]
    private ?int $tries = 0;

    #[ORM\Column(index: true)]
    private ?int $priority = 0;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true, index: true)]
    private ?\DateTimeImmutable $availableAt = null;

    public function __construct(string $type, ?int $objectId, ?string $source, ?string $origin, array $payload, ?int $priority = 0)
    {
        $this->type = $type;
        $this->objectId = $objectId;
        $this->source = $source;
        $this->origin = $origin;
        $this->payload = $payload;
        $this->priority = $priority;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function markOpen(): void { $this->state = 'open'; $this->updatedAt = new \DateTimeImmutable(); }
    public function markPending(): void { $this->state = 'pending'; $this->updatedAt = new \DateTimeImmutable(); }
    public function markDone(): void { $this->state = 'done'; $this->updatedAt = new \DateTimeImmutable(); }
    public function markError(): void { $this->state = 'error'; $this->updatedAt = new \DateTimeImmutable(); }
    public function markRetry(): void { 
        $this->state = 'open';
        $this->updatedAt = new \DateTimeImmutable();
        $this->tries++; 
        $delayStr = self::DELAY_TRY[ $this->tries] ?? end(self::DELAY_TRY);
        $this->availableAt = new \DateTimeImmutable()->modify($delayStr);
        
    }

    public function isMaxTriesReached(): bool
    {
        return $this->tries >= self::MAX_TRY;
    }

    public function addTry(): void
    {
        $this->markRetry();
        if($this->isMaxTriesReached()) {
            $this->markError();
        }
    }

    public function getPayloadDto(string $dtoClass): object
    {
        return $dtoClass::fromArray($this->payload);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getObjectId(): ?int
    {
        return $this->objectId;
    }

    public function setObjectId(?int $objectId): static
    {
        $this->objectId = $objectId;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): static
    {
        $this->source = $source;

        return $this;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(?string $origin): static
    {
        $this->origin = $origin;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getTries(): ?int
    {
        return $this->tries;
    }

    public function setTries(int $tries): static
    {
        $this->tries = $tries;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getAvailableAt(): ?\DateTimeImmutable
    {
        return $this->availableAt;
    }

    public function setAvailableAt(\DateTimeImmutable $availableAt): static
    {
        $this->availableAt = $availableAt;

        return $this;
    }

    public function getPayload(): ?array
    {
        return $this->payload;
    }

    public function setPayload(array $payload): static
    {
        $this->payload = $payload;

        return $this;
    }

    public function isAvailableNow(): bool {
        return $this->availableAt === null || $this->availableAt <= new \DateTimeImmutable();
    }
}
