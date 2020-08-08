<?php
return [
    'contact_to' => \DI\get('mail_to'),
    \App\Contact\ContactAction::class => DI\autowire()->constructorParameter('to', \DI\get('contact_to'))
];
