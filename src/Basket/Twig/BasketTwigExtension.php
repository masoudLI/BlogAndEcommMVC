<?php

namespace App\Basket\Twig;

use App\Basket\Basket;
use Framework\App;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BasketTwigExtension extends AbstractExtension
{
    private $basket;

    private $app;

    public function __construct(Basket $basket, App $app)
    {
        $this->basket = $basket;
        $this->app = $app;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('basket_count', [$this->basket, 'count']),
            new TwigFunction('module_enabled', [$this, 'moduleEnabale'])
        ];
    }


    public function moduleEnabale(string $nameModule)
    {
        foreach ($this->app->getModules() as $module) {
            if ($module::NAME === $nameModule) {
                return true;
            }
        }
        return false;
    }
}
