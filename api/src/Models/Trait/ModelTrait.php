<?php

namespace App\Models\Trait;

trait ModelTrait
{
    public function setAttributes(array $data): void
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public  function getTable(): string
    {
        return self::TABLE;
    }
}
