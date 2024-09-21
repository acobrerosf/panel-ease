# PanelEase

## About PanelEase

PanelEase is a Laravel/Filament skeleton to kickstart your projects. It comes with a default admin panel with a section to 
manage your admin users and it has the text translated to spanish as well.

## Users

A user must be assigned with a user type. By default, the skeleton comes with the types "Super Admin" and "Administrator".

## Customize user types

First modify the users migration file and add the new type to the "user_types" table. Then update the UserType model to include
the new added type/s, if you want this new user type to use the default admin panel make sure to allow the access by updating the
*canAccessPanel* method that exists in the User model class.

## Create a new panel for new user types.

You can create as many user types as you want and use them within the admin panel that comes by default. In case you need to 
create a new panel for a different type of user, follow these steps:

Let's say we want to create a panel for clients so they can log in and see their related data within the project. First we need
to create the Filament panel for that.

```
php artisan make:filament-panel clients
```

Then update the app\Enums\PanelEnums.php class:

```php
<?php

namespace App\Enums;

enum PanelEnums: string
{
    case Admin = 'admin';
    case Clients = 'clients';
}
```

The artisan command should have created a new provider in *app\Providers\Filament\ClientsPanelProvider.php*. Make sure to specify
the id and path using the new _PanelEnums_ case we just added:

```php
class ClientsPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id(PanelEnums::Clients->value)
            ->path(PanelEnums::Clients->path())
            ->login()
            ->passwordReset(resetAction: ResetPassword::class)
            ->profile(isSimple: false)
            ->colors([
                'primary' => '#56b0d9',
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Clients/Resources'), for: 'App\\Filament\\Clients\\Resources')
            ->discoverPages(in: app_path('Filament/Clients/Pages'), for: 'App\\Filament\\Clients\\Pages')
            ->discoverClusters(in: app_path('Filament/Clients/Clusters'), for: 'App\\Filament\\Clients\\Clusters')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Clients/Widgets'), for: 'App\\Filament\\Clients\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
            ->authGuard('admin')
            ->authMiddleware([
                Authenticate::class,
            ])          
            ->darkMode(
                condition: true, 
                isForced: true
            )
            ->renderHook(
                name: 'panels::body.end',
                hook: fn () => view('panels.footer')
            );
    }
}
```

Lastly, you need to update the *canAccessPanel* method to the _User_ model class:

```php
public function canAccessPanel(Panel $panel): bool
{
    if (!$this->hasVerifiedEmail() || !$this->isActive()) {
        return false;
    }

    switch ($panel->getId()) {
        case PanelEnums::Admin->value:
            return in_array($this->type_id, [UserType::SUPER_ADMIN, UserType::ADMINISTRATOR]);
        case PanelEnums::Clients->value:
            return $this->type_id == UserType::CLIENT;
    }

    return false;
}
```

## Contributing

This is just a simple skeleton to use as a base for my own projects. Feel free to fork or copy it and make any changes you want.
If you want to contribute I would really appreciate it but I can't confirm I will review and approve said changes.

## License

PanelEase is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Developed By
<p align="center"><a href="https://abelcobreros.com" target="_blank"><img src="https://abelcobreros.com/dark-logo.png" width="400" alt="Abel Cobreros Logo"></a></p>

