# Actualiza Composer
composer self-update

# Instala Laravel
composer create-project laravel/laravel mi-proyecto-laravel

# Entra en el directorio del proyecto
cd mi-proyecto-laravel

# Instala Filament v3 (versión actual)
composer require filament/filament:"^3.3"

# Instala los recursos de Filament con paneles
php artisan filament:install --panels

# Publica los assets (este paso puede que ya no sea necesario, depende de la instalación)
# php artisan filament:assets

# Crea un usuario administrador
php artisan make:filament-user