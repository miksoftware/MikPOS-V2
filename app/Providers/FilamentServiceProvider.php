<?php

namespace App\Providers;

use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Css;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Registra el archivo CSS como un objeto Asset
        FilamentAsset::register([
            Css::make('filament-custom', __DIR__ . '/../../resources/css/filament-custom.css'),
        ]);
    }
}