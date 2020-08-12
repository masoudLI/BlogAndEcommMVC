<?php

namespace Framework;

use Psr\Container\ContainerInterface;

class SwiftMailerFactory
{

    public function __invoke(ContainerInterface $container): \Swift_Mailer
    {
        if ($container->get('env') === 'production') {
            $transport = new \Swift_SendmailTransport('/usr/sbin/sendmail -bs');
        } else {
            $transport = new \Swift_SmtpTransport('localhost', 25);
        }
        return new \Swift_Mailer($transport);
    }
}
