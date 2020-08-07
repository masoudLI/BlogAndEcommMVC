<?php

namespace Framework;

use Framework\Auth\User;

interface Auth
{
    
    /**
     * getUser
     *
     * @return User|null
     */
    public function getUser(): ?User;
}
