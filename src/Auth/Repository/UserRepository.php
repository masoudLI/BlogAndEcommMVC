<?php

namespace App\Auth\Repository;

use App\Auth\Model\User;
use Framework\Database\AbstractRepository;

class UserRepository extends AbstractRepository
{

    protected string $table = 'users';

    protected string $entity = User::class;
}
