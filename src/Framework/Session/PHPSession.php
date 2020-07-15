<?php

namespace Framework\Session;

class PHPSession implements SessionInterface
{


    /**
     * Assure que la Session est démarrée
     */
    private function ensureStarted()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * get
     *
     * @param  string $key
     * @param  mixed $default
     * @return void
     */
    public function get(string $key, $default = null) {
        $this->ensureStarted();
        if (!$this->has($key)) {
            return $default;
        }
        $return = $_SESSION[$key];
        return $return;
    }


    /**
     * {@inheritdoc}
     */
    public function has(string $key)
    {
        $this->ensureStarted();
        return \array_key_exists($key, $_SESSION) && $_SESSION[$key];
    }


    /**
     * set
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function set(string $key, $value): void {
        $this->ensureStarted();
        $_SESSION[$key] = $value;
    }


    /**
     * delete
     *
     * @param  string $key
     * @return void
     */
    public function clear(string $key): void {
        $this->ensureStarted();
        unset($_SESSION[$key]);
    }
}
