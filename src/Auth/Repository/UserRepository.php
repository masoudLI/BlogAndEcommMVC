<?php

namespace App\Auth\Repository;

use App\Auth\Model\User;
use Framework\Database\AbstractRepository;
use PDO;

class UserRepository extends AbstractRepository
{
    
    protected string $table = 'users';
    
    protected string $entity;
    

    public function __construct(PDO $pdo, string $entity = User::class)
    {
        parent::__construct($pdo);
        $this->entity = $entity;
    }

    
}
