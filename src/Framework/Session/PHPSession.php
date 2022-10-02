<?php

namespace Framework\Session;

class PHPSession implements SessionInterface, \ArrayAccess
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
    public function get(string $key, $default = null)
    {
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
    public function set(string $key, $value): void
    {
        $this->ensureStarted();
        $_SESSION[$key] = $value;
    }


    /**
     * delete
     *
     * @param  string $key
     * @return void
     */
    public function clear(string $key): void
    {
        $this->ensureStarted();
        unset($_SESSION[$key]);
    }


    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset): bool
    {
        $this->ensureStarted();
        return array_key_exists($offset, $_SESSION);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset): void
    {
        $this->clear($offset);
    }
}
