<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
           // Other middleware
            \App\Http\Middleware\SubdomainMiddleware::class,
            \App\Http\Middleware\SetDatabaseConnection::class,
            // \App\Http\Middleware\mainAuth::class,
            \App\Http\Middleware\setLanguage::class, 
        ],
        
        'api' => [
            'throttle:60,1',
            'bindings',
            // Other middleware
            // \App\Http\Middleware\mainAuth::class,
            \App\Http\Middleware\SetDatabaseConnection::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'adminIzo'                => \App\Http\Middleware\adminIzo::class,
        'authIzo'                 => \App\Http\Middleware\AuthIzo::class,
        'auth'                    => \App\Http\Middleware\Authenticate::class,
        'Aapi'                    => \App\Http\Middleware\ApiAuthentication::class,
        'auth.basic'              => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings'                => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers'           => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can'                     => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'                   => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle'                => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'language'                => \App\Http\Middleware\Language::class,
        'timezone'                => \App\Http\Middleware\Timezone::class,
        'FirstLogin'              => \App\Http\Middleware\FirstLogin::class,
        'mainAuth'                => \App\Http\Middleware\mainAuth::class,
        'SetSessionData'          => \App\Http\Middleware\SetSessionData::class,
        'SetDatabaseConnection'   => \App\Http\Middleware\SetDatabaseConnection::class,
        'subdomain'               => \App\Http\Middleware\SubdomainMiddleware::class,
        'setData'                 => \App\Http\Middleware\IsInstalled::class,
        'authh'                   => \App\Http\Middleware\IsInstalled::class,
        'EcomApi'                 => \App\Http\Middleware\EcomApi::class,
        'AdminSidebarMenu'        => \App\Http\Middleware\AdminSidebarMenu::class,
        'superadmin'              => \App\Http\Middleware\Superadmin::class,
        'CheckUserLogin'          => \App\Http\Middleware\CheckUserLogin::class,        
        'SingleSessionMiddleware' => \App\Http\Middleware\SingleSessionMiddleware::class,    
        'cors'                    => \App\Http\Middleware\Cors::class, // added
        'setLanguage'             => \App\Http\Middleware\setLanguage::class,    
    ];
    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\Authenticate::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
        
    ];
}
