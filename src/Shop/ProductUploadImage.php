<?php

namespace App\Shop;

use Framework\UploadImage;

class ProductUploadImage extends UploadImage
{

    protected $path = "public/uploads/products";

    protected array $formats = [
        'small' => ['318', '118']
    ];
}
