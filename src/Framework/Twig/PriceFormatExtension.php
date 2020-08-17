<?php

namespace Framework\Twig;

use Framework\Session\FlashService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class PriceFormatExtension extends AbstractExtension
{
    /**
     * @var string
     */
    private $currency;

    public function __construct(string $currency = '€')
    {
        $this->currency = $currency;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('vat', [$this, 'getVat']),
            new TwigFunction('vat_only', [$this, 'getVatOnly'])
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('price_format', [$this, 'priceFormat'])
        ];
    }

    /**
     * Renvoie un extrait du contenu
     * @param string $text
     * @param int $maxLength
     * @return string
     */
    public function priceFormat(?float $price = null, ?string $currency = null): string
    {
        return number_format($price, 2, ',', ' ') . ' ' . ($currency ?: $this->currency);
    }


    public function getVat(float $price, ?float $vat): float
    {
        return $price + $this->getVatOnly($price, $vat);
    }


    public function getVatOnly(float $price, ?float $vat): float
    {
        if ($vat === null) {
            return 0;
        }
        return $price * ($vat / 100);
    }

}
