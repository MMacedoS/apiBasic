<?php

namespace App\Repositories\Contracts;

interface BaseInterface
{
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function findById(int $id);
    public function findAll(array $criteria = []);
    public function findByUuid(string $uuid);
}
