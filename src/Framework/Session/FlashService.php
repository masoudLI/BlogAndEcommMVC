<?php

namespace Framework\Session;

class FlashService
{

    /**
     * @var SessionInterface
     */
    private $session;

    private $messages = null;

    const SESSION_KEY = '_flash';

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }


    public function success(string $message)
    {
        $flash = $this->session->get(self::SESSION_KEY, []);
        $flash['success'] = $message;
        $this->session->set(self::SESSION_KEY, $flash);
    }

    public function error(string $message)
    {
        $flash = $this->session->get(self::SESSION_KEY, []);
        $flash['error'] = $message;
        $this->session->set(self::SESSION_KEY, $flash);
    }

    public function warning(string $message)
    {
        $flash = $this->session->get(self::SESSION_KEY, []);
        $flash['warning'] = $message;
        $this->session->set(self::SESSION_KEY, $flash);
    }

    public function addFlash(string $type, string $message)
    {
        $flash = $this->session->get(self::SESSION_KEY, []);
        if ($type === 'success') {
            $flash['success'] = $message;
        } elseif ($type === 'error') {
            $flash['error'] = $message;
        } else {
            $flash['warning'] = $message;
        }
        $this->session->set(self::SESSION_KEY, $flash);
    }

    public function get(string $type): ?string
    {
        if (is_null($this->messages)) {
            $this->messages = $this->session->get(self::SESSION_KEY, []);
            $this->session->clear(self::SESSION_KEY);
        }
        if (array_key_exists($type, $this->messages)) {
            return $this->messages[$type];
        }
        return null;
    }
}
