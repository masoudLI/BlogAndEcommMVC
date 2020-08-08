<?php

namespace App\Contact;

use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;

class ContactModule extends Module
{

    const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(RendererInterface $renderer, Router $router)
    {

        $renderer->addPath('contact', __DIR__ . '/views');
        $router->get('contact', '/contact', ContactAction::class);
        $router->post(null, '/contact', ContactAction::class);
    }
}
