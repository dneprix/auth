<?php

require_once __DIR__.'/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->register(Jenssegers\Mongodb\MongodbServiceProvider::class);

$app->configure('app');
$app->configure('database');
$app->configure('jwt');
$app->configure('mail');

$app->withFacades();
$app->withEloquent();

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->register(Illuminate\Mail\MailServiceProvider::class);
$app->register(App\Providers\UserServiceProvider::class);
$app->register(App\Providers\EmailServiceProvider::class);

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__.'/../routes/web.php';
    require __DIR__.'/../routes/api.php';
});


return $app;
