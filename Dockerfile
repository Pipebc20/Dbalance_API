FROM php:8.2-cli

# Dependencias del sistema
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev zip \
    && docker-php-ext-install pdo pdo_pgsql zip

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Carpeta de trabajo
WORKDIR /app

# Copiar todo el proyecto
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Permisos necesarios
RUN chmod -R 775 storage bootstrap/cache

# Puerto (Railway usa $PORT)
EXPOSE 8080

# Comando de arranque
CMD php artisan serve --host=0.0.0.0 --port=$PORT
