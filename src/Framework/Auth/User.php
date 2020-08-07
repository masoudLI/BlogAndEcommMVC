<?php

namespace Framework\Auth;

interface User
{

    
    /**
     * getUsername
     *
     * @return string
     */
    public function getUsername(): string;
    
    /**
     * getRoles
     *
     * @return string[]
     */
    public function getRoles(): array;
}
