<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\MenuItem;
use Illuminate\Foundation\Vite;
use Illuminate\Support\ServiceProvider;
use App\Filament\Resources\RoleResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\PermissionResource;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Filament::serving(function () {
//            Filament::registerTheme(
//                app(Vite::class)('resources/css/filament.css'),
//            );

            if (
                auth()->user()
                && auth()->user()->is_admin
                && auth()->user()->hasRole(['superman', 'admin'])
            ) {
                Filament::registerUserMenuItems([
                    MenuItem::make()
                        ->label('Manage Users')
                        ->url(UserResource::getUrl())
                        ->icon('heroicon-s-users'),
                    MenuItem::make()
                        ->label('Manage Roles')
                        ->url(RoleResource::getUrl())
                        ->icon('heroicon-s-briefcase'),
                    MenuItem::make()
                        ->label('Manage Permissions')
                        ->url(PermissionResource::getUrl())
                        ->icon('heroicon-s-key'),
                ]);
            }
        });
    }
}
