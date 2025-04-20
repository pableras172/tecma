<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use TomatoPHP\FilamentSettingsHub\Facades\FilamentSettingsHub;
use TomatoPHP\FilamentSettingsHub\Services\Contracts\SettingHold;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        FilamentSettingsHub::register([
            SettingHold::make()
                ->order(1)
                ->label('Países')
                ->icon('heroicon-o-globe-alt')
                ->route('filament.dashboard.resources.countries.index')
                ->description('Listado de países y sus códigos')
                ->group('Ubicación'),
    
            SettingHold::make()
                ->order(2)
                ->label('Provincias')
                ->icon('heroicon-o-map')
                ->route('filament.dashboard.resources.provinces.index')
                ->description('Provincias por país')
                ->group('Ubicación'),
    
            SettingHold::make()
                ->order(3)
                ->label('Ciudades')
                ->icon('heroicon-o-building-office-2')
                ->route('filament.dashboard.resources.cities.index')
                ->description('Gestión de municipios')
                ->group('Ubicación'),
        ]);
    }
}
