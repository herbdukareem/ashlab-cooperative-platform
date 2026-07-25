<?php

namespace App\Integrations\Contracts;

interface IdentityGateway
{
    /** @return array{verified:bool,reference:string,reason:?string,attributes:array<string,mixed>} */
    public function verify(string $type, string $identifier, array $subject = []): array;
}
