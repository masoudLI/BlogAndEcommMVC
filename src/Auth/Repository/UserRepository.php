<?php

namespace App\Auth\Repository;

use App\Auth\Model\User;
use Framework\Database\AbstractRepository;
use PDO;
use Ramsey\Uuid\Uuid;

class UserRepository extends AbstractRepository
{

    protected string $table = 'users';

    protected string $entity;


    public function __construct(PDO $pdo, string $entity = User::class)
    {
        parent::__construct($pdo);
        $this->entity = $entity;
    }


    public function ResetPassword(int $id): string
    {
        $token = Uuid::uuid4()->toString();
        $this->update($id, [
            'password_reset' => $token,
            'password_reset_at' => date('Y-m-d')
        ]);
        return $token;
    }

    public function updatePassword(int $id, string $password)
    {
        $this->update($id, [
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'password_reset' => null,
            'password_reset_at' => null
        ]);
    }
}
