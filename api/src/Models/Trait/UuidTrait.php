<?php

namespace App\Models\Trait;

trait UuidTrait
{
    public function generateUuid(): string
    {
        return bin2hex(random_bytes(16));
    }
}
