<?php

namespace App\Shop\Repository;

use App\Auth\Model\User;
use Framework\Database\AbstractRepository;

class StripeUserRepository extends AbstractRepository
{

    protected string $table = "users_stripe";

    public function findCustomerForUser(User $user): ?string
    {
        $record = $this->makeQuery()
            ->select('customer_id')
            ->where('user_id = :user')
            ->setParams('user', $user->getId())
            ->fetch();
        if ($record === false) {
            return null;
        }
        return $record->customerId;

    }
}
