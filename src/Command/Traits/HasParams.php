<?php

namespace App\Command\Traits;

trait HasParams
{
    protected function param(string $key): mixed
    {
        return $this->getApplication()
            ->getKernel()
            ->getContainer()
            ->getParameter($key);
    }
}