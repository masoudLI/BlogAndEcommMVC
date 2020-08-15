<?php

namespace App\Auth\Event;

use Framework\Auth\User;
use Massoud\Event;

class LoginEvent extends Event 
{
    
    public $name = "auth_login";


    public function __construct(User $user)
    {   
        $this->setTarget($user);
    }


    public function getTarget(): User
    {
        return parent::getTarget();
    }

}
