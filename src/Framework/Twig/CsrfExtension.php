<?php

namespace Framework\Twig;

use Framework\Middleware\CsrfMiddleware;
use Framework\CsrfToken;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CsrfExtension extends AbstractExtension
{

    /**
     * @var CsrfToken
     */
    private $csrfMiddleware;

    public function __construct(CsrfMiddleware $csrfMiddleware)
    {
        $this->csrfMiddleware = $csrfMiddleware;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('csrf_input', [$this, 'csrfInput'], ['is_safe' => ['html']])
        ];
    }

    public function csrfInput()
    {
        return '<input type="hidden" ' .
            'name="' . $this->csrfMiddleware->getFormKey() . '" ' .
            'value="' . $this->csrfMiddleware->generateToken() . '"/>';
    }
}
