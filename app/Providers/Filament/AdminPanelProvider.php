<?php

namespace App\Providers\Filament;


use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use App\Filament\Auth\CustomLogin;
use App\Filament\Auth\AdminRegister;
use Filament\Support\Colors\Color;
use App\Filament\Widgets\ActiveBuyersOverviewWidget;
use App\Filament\Widgets\PendingOrdersOverviewWidget;
use App\Filament\Widgets\SalesSummaryWidget;
use Filament\Widgets\AccountWidget;
use Illuminate\Support\Facades\Route;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use App\Filament\Resources\BuisnessInfos\BuisnessInfoResource;
use App\Filament\Resources\ContentManagement\ContentManagementResource;
use App\Filament\Resources\OrderSettings\OrderSettingsResource;
use App\Filament\Resources\SeoSettings\SeoSettingsResource;
use App\Filament\Resources\TermsSettings\TermsSettingsResource;
use App\Filament\Resources\SalesAdminInvoices\SalesAdminInvoicesResource;
use App\Filament\Pages\ManageSmtpSettings;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(CustomLogin::class)
            ->registration(AdminRegister::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                ActiveBuyersOverviewWidget::class,
                PendingOrdersOverviewWidget::class,
                AccountWidget::class,
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationItems([
            NavigationItem::make('Sales Summary')
                ->url(fn () => SalesAdminInvoicesResource::getUrl('index'))
                ->icon('heroicon-o-chart-bar')
                ->group('Administration')
                ->sort(1)
                ->isActiveWhen(fn () => request()->routeIs('filament.admin.resources.sales-invoices.*'))
                ->visible(fn () => auth()->user()?->isSuperAdmin()),

            NavigationItem::make('Content Management')
                ->url(fn () => ContentManagementResource::getUrl('index'))
                ->icon('heroicon-o-document-text')
                ->group('Business Settings')
                ->sort(1)
                ->isActiveWhen(fn () => request()->routeIs('filament.admin.resources.content-management.*'))
                ->visible(fn () => auth()->user()?->isSuperAdmin()),
            
            NavigationItem::make('Business Info')
                ->url(fn () => BuisnessInfoResource::getUrl('index'))
                ->icon('heroicon-o-building-office')
                ->group('Business Settings')
                ->sort(2)
                ->isActiveWhen(fn () => request()->routeIs('filament.admin.resources.business-info.*'))
                ->visible(fn () => auth()->user()?->isSuperAdmin()),

                         NavigationItem::make('Order Settings')
                ->url(fn () => OrderSettingsResource::getUrl('index'))
                ->icon('heroicon-o-building-office')
                ->group('Business Settings')
                ->sort(3)
                ->isActiveWhen(fn () => request()->routeIs('filament.admin.resources.order_settings.*'))
                ->visible(fn () => auth()->user()?->isSuperAdmin()),

            NavigationItem::make('Terms & Conditions')
                ->url(fn () => TermsSettingsResource::getUrl('index'))
                ->icon('heroicon-o-document-text')
                ->group('Business Settings')
                ->sort(4)
                ->isActiveWhen(fn () => request()->routeIs('filament.admin.resources.terms_settings.*'))
                ->visible(fn () => auth()->user()?->isSuperAdmin()),

            NavigationItem::make('SMTP Settings')
                ->url(fn () => ManageSmtpSettings::getUrl())
                ->icon('heroicon-o-envelope')
                ->group('Business Settings')
                ->sort(4)
                ->isActiveWhen(fn () => request()->routeIs('filament.admin.pages.manage-smtp-settings'))
                ->visible(fn () => auth()->user()?->isSuperAdmin()),

            NavigationItem::make('SEO Settings')
                ->url(fn () => SeoSettingsResource::getUrl('index'))
                ->icon('heroicon-o-magnifying-glass')
                ->group('Business Settings')
                ->sort(5)
                ->isActiveWhen(fn () => request()->routeIs('filament.admin.resources.seo-settings.*'))
                ->visible(fn () => auth()->user()?->isSuperAdmin()),
        ])
        ->navigationGroups([
            NavigationGroup::make('Business Settings')
                // ->icon('heroicon-o-cog-6-tooth')
                ->collapsible()
                ->collapsed(false), // Start expanded
        ]);
    }
}
