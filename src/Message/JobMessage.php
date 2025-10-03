<?php 

namespace App\Message;

class JobMessage
{
    public function __construct(
        public readonly ?string $type = null,
    ) {
    }

    public function getType(): string
    {
        return $this->type;
    }
}