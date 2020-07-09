<?php

use App\Blog\BlogModule;

return [
    'blog_prefix' => '/blog',
    BlogModule::class => \DI\autowire()->constructorParameter('prefix', \DI\get('blog_prefix')),
];
