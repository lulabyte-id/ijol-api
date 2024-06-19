<?php

use Laravel\Socialite\SocialiteServiceProvider;
use SocialiteProviders\Manager\ServiceProvider;

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\FilamentServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\TokenProvider::class,
    SocialiteServiceProvider::class,
    ServiceProvider::class,
];
