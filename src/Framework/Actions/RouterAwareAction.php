<?php

namespace Framework\Actions;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

trait RouterAwareAction
{

    /**
     * Renvoie une réponse de redirection
     *
     * @param  string $name
     * @param  array $params
     * @return ResponseInterface
     */
    public function redirect(string $name, array $params = []): ResponseInterface
    {
        $redirectUri = $this->router->generateUri($name, $params);
        return (new Response())
            ->withStatus(301)
            ->withHeader('Location', $redirectUri);
    }
}
