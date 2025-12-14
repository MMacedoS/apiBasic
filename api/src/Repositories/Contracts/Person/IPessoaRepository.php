<?php

namespace App\Repositories\Contracts\Person;

use App\Repositories\Contracts\BaseInterface;

interface IPessoaRepository extends BaseInterface
{
    public function findByEmail(string $email);
    public function findByUserId(string $userId);
}
