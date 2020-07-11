<?php

namespace Framework\Twig;

use Framework\Router;
use Twig\TwigFunction;
use Pagerfanta\View\DefaultView;
use Pagerfanta\PagerfantaInterface;
use Twig\Extension\AbstractExtension;

class PagerFantaExtension extends AbstractExtension
{

    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('paginate', [$this, 'paginate'], ['is_safe' => ['html']])
        ];
    }

    public function paginate(
        PagerfantaInterface $pagerfanta,
        string $route,
        array $routerParams = [],
        array $queryArgs = []
    ) {
        $view = new DefaultView();
        return $view->render($pagerfanta, function (int $page) use ($route, $routerParams, $queryArgs) {
            if ($page > 1) {
                $queryArgs['p'] = $page;
            }
            return $this->router->generateUri($route, $routerParams, $queryArgs);
        });
    }
}
