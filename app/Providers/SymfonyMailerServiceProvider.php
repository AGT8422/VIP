<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\SmtpTransport;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class SymfonyMailerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Mailer::class, function ($app) {
            // Set up the transport (SMTP) using details from .env
            $transport = new SmtpTransport(
                env('MAIL_HOST'), 
                env('MAIL_PORT'),
                env('MAIL_ENCRYPTION') ?? 'tls'
            );
            
            // Set SMTP authentication
            $transport->setUsername(env('MAIL_USERNAME'));
            $transport->setPassword(env('MAIL_PASSWORD'));

            // Return a Mailer instance with the configured transport
            return new Mailer($transport);
        });
    }

    public function boot()
    {
        // You can boot anything related to the mailer service here, if needed
    }
}
