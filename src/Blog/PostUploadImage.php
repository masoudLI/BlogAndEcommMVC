<?php

namespace App\Blog;

use Framework\UploadImage;

class PostUploadImage extends UploadImage
{

    protected $path = "public/uploads/posts";

    protected array $formats = [];
}
