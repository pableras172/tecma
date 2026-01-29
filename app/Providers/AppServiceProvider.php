<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use TomatoPHP\FilamentSettingsHub\Facades\FilamentSettingsHub;
use TomatoPHP\FilamentSettingsHub\Services\Contracts\SettingHold;
use App\Models\User;
use App\Observers\UserObserver;

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
        User::observe(UserObserver::class);

        FilamentSettingsHub::register([
            SettingHold::make()
                ->order(8)
                ->label('Países')
                ->icon('heroicon-o-globe-alt')
                ->route('filament.dashboard.resources.countries.index')
                ->description('Listado de países y sus códigos')
                ->group('Ubicación'),

            SettingHold::make()
                ->order(9)
                ->label('Provincias')
                ->icon('heroicon-o-map')
                ->route('filament.dashboard.resources.provinces.index')
                ->description('Provincias por país')
                ->group('Ubicación'),

            SettingHold::make()
                ->order(10)
                ->label('Ciudades')
                ->icon('heroicon-o-building-office-2')
                ->route('filament.dashboard.resources.cities.index')
                ->description('Gestión de municipios')
                ->group('Ubicación'),
            SettingHold::make()
                ->order(6)
                ->label('Tipos de trabajo')
                ->icon('heroicon-o-wrench-screwdriver')
                ->route('filament.dashboard.resources.tipo-trabajos.index')
                ->description('Gestión de tipos de trabajo')
                ->group('Configuración Tec-Ma'),
            SettingHold::make()
                ->order(7)
                ->label('Clientes')
                ->icon('heroicon-o-building-office')
                ->route('filament.dashboard.resources.clientes.index')
                ->description('Gestión de clientes')
                ->group('Configuración Tec-Ma'),
            SettingHold::make()
                ->order(5)
                ->label('Empleados')
                ->icon('heroicon-o-identification')
                ->route('filament.dashboard.resources.users.index')
                ->description('Gestión de empleados')
                ->group('Configuración Tec-Ma'),
            SettingHold::make()
                ->order(2)
                ->label('Categorias Profesiones')
                ->icon('heroicon-o-academic-cap')
                ->route('filament.dashboard.resources.categoria-profesionals.index')
                ->description('Gestión de categorias profesiones')
                ->group('Configuración Tec-Ma'),
            SettingHold::make()
                ->order(3)
                ->label('Departamentos')
                ->icon('heroicon-o-building-library')
                ->route('filament.dashboard.resources.departamentos.index')
                ->description('Gestión de departamentos')
                ->group('Configuración Tec-Ma'),
        ]);
    }
}
