<?php

namespace App\Blog\Actions;

use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogAction
{
    
    /**
     * @var RendererInterface
     */
    private RendererInterface $renderer;


    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request)
    {
        if ($request->getAttribute('slug')) {
            return $this->show($request);
        }
        return $this->index();
    }


    public function index()
    {
        return $this->renderer->render('@blog/index');
    }

    public function show(Request $request)
    {
        return $this->renderer->render('@blog/show', [
            'slug' => $request->getAttribute('slug')
        ]);
    }
}
