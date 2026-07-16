<?php

namespace App\Support\Tenancy;

use App\Models\Cooperative;
use LogicException;

final class TenantContext
{
    private ?Cooperative $cooperative = null;

    public function set(Cooperative $cooperative): void
    {
        $this->cooperative = $cooperative;
    }

    public function get(): Cooperative
    {
        return $this->cooperative ?? throw new LogicException('No cooperative tenant has been resolved.');
    }

    public function id(): ?string
    {
        return $this->cooperative?->getKey();
    }

    public function hasTenant(): bool
    {
        return $this->cooperative !== null;
    }

    public function clear(): void
    {
        $this->cooperative = null;
    }
}

