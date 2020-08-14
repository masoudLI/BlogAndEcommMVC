<?php

namespace Framework\Session;

interface SessionInterface
{
    
    /**
     * get
     *
     * @param  string $key
     * @param  mixed $default
     * @return void
     */
    public function get(string $key, $default = null);

    
    /**
     * set
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function set(string $key, $value): void;


    /**
     * set
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function has(string $key);

    
    /**
     * delete
     *
     * @param  string $key
     * @return void
     */
    public function clear(string $key): void;
}
