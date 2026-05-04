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
            // Ubicación
            SettingHold::make()
                ->order(8)
                ->label('Países')
                ->icon('heroicon-o-globe-alt')
                ->color('#3B82F6') // Azul
                ->route('filament.dashboard.resources.countries.index')
                ->description('Listado de países y sus códigos')
                ->group('Ubicación'),

            SettingHold::make()
                ->order(9)
                ->label('Provincias')
                ->icon('heroicon-o-map')
                ->color('#06B6D4') // Cian
                ->route('filament.dashboard.resources.provinces.index')
                ->description('Provincias por país')
                ->group('Ubicación'),

            SettingHold::make()
                ->order(10)
                ->label('Ciudades')
                ->icon('heroicon-o-building-office-2')
                ->color('#0EA5E9') // Azul cielo
                ->route('filament.dashboard.resources.cities.index')
                ->description('Gestión de municipios')
                ->group('Ubicación'),

            // Configuración Tec-Ma
            SettingHold::make()
                ->order(2)
                ->label('Categorias Profesiones')
                ->icon('heroicon-o-academic-cap')
                ->color('#A855F7') // Morado
                ->route('filament.dashboard.resources.categoria-profesionals.index')
                ->description('Gestión de categorias profesiones')
                ->group('Configuración Tec-Ma'),

            SettingHold::make()
                ->order(3)
                ->label('Departamentos')
                ->icon('heroicon-o-building-library')
                ->color('#64748B') // Gris pizarra
                ->route('filament.dashboard.resources.departamentos.index')
                ->description('Gestión de departamentos')
                ->group('Configuración Tec-Ma'),

            SettingHold::make()
                ->order(5)
                ->label('Empleados')
                ->icon('heroicon-o-identification')
                ->color('#F43F5E') // Rosa/Rojo
                ->route('filament.dashboard.resources.users.index')
                ->description('Gestión de empleados')
                ->group('Configuración Tec-Ma'),

            SettingHold::make()
                ->order(6)
                ->label('Tipos de trabajo')
                ->icon('heroicon-o-wrench-screwdriver')
                ->color('#F59E0B') // Naranja/Ámbar
                ->route('filament.dashboard.resources.tipo-trabajos.index')
                ->description('Gestión de tipos de trabajo')
                ->group('Configuración Tec-Ma'),

            SettingHold::make()
                ->order(7)
                ->label('Secuencias de partes')
                ->icon('heroicon-o-hashtag')
                ->color('#6366F1') // Índigo
                ->route('filament.dashboard.resources.secuencia-partes.index')
                ->description('Gestión de numeración de partes de trabajo')
                ->group('Configuración Tec-Ma'),

            SettingHold::make()
                ->order(8)
                ->label('Clientes')
                ->icon('heroicon-o-building-office')
                ->color('#10B981') // Verde
                ->route('filament.dashboard.resources.clientes.index')
                ->description('Gestión de clientes')
                ->group('Configuración Tec-Ma'),
        ]);
    }
}
