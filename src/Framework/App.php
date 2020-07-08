<?php declare(strict_types=1);

namespace Framework;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

class App
{

    public function run(ServerRequestInterface $request)
    {
        $uri = $request->getUri()->getPath();
        if (empty($uri) && $uri[-1] === '/') {
            return (new Response())
            ->withStatus(301)
            ->withHeader('Location', substr($uri, 0, -1));
        }
        if ($uri === '/blog') {
            return new Response(200, [], '<h1>Bonjour tout le monde</h1>');
        }
        return new Response(404, [], '<h1>Erreur 404</h1>');
    }
}
