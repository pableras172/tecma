<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\CheckUserActive;
use TomatoPHP\FilamentSettingsHub\FilamentSettingsHubPlugin;
use App\Filament\Widgets\TareasPorUsuarioChart;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;
use App\Filament\Pages\EditProfile;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use Illuminate\Validation\ValidationException;

class DashboardPanelProvider extends PanelProvider
{

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('dashboard')
            ->path('dashboard')
            ->login(\App\Filament\Personal\Pages\Login::class)
            ->passwordReset()
            ->emailVerification()
            ->registration()            
            ->profile(EditProfile::class)
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => Color::Indigo,
                'success' => Color::Green,
                'warning' => Color::Orange,
            ])
            ->font('Poppins')
            ->brandName(setting("site_name"))
            ->brandLogo(fn() => setting("site_logo") ? asset('storage/' . setting("site_logo")) : null)
            ->brandLogoHeight('3rem')
            ->favicon(fn() => setting("site_profile") ? asset('storage/' . setting("site_profile")) : null)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->plugin(
                FilamentSettingsHubPlugin::make()
                    ->allowSiteSettings()
                    ->allowSocialMenuSettings(),
                FilamentBackgroundsPlugin::make()
                    ->imageProvider(MyImages::make()
                        ->directory('images/backgrounds')),
            )
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Personal\Widgets\PersonalTareasUsuarioWidget::class,
                TareasPorUsuarioChart::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                CheckUserActive::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->userMenuItems([
                'panel_personal' => \Filament\Navigation\MenuItem::make()
                    ->label('Panel Personal')
                    ->url(fn () => route('filament.personal.pages.dashboard'))
                    ->icon('heroicon-o-user')
                    ->visible(fn () => auth()->user()?->hasRole('admin')),
            ])
            ->unsavedChangesAlerts()
            ->sidebarCollapsibleOnDesktop()
            ->databaseNotifications();
    }

    public function boot(): void
    {
        FilamentView::registerRenderHook(
            'panels::head.end',
            fn(): string => Blade::render(<<<'HTML'
                <link rel="manifest" href="/manifest.json">
                <meta name="theme-color" content="#4f46e5">
                <meta name="apple-mobile-web-app-capable" content="yes">
                <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
                <meta name="apple-mobile-web-app-title" content="TECMA">
                <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png">
                <script src="/pwa-register.js" defer></script>
                <script src="/livewire-csrf-handler.js" defer></script>
            HTML)
        );
    }
}
