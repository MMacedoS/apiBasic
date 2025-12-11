<?php

namespace App\Repositories\Contracts\Users;

use App\Repositories\Contracts\BaseInterface;

interface IUserRepository extends BaseInterface
{
    public function authenticate(string $email, string $password);
}
