FROM php:8.2-cli

# Installer dépendances système
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Dossier de travail
WORKDIR /app

# Copier le projet
COPY . .

# Installer dépendances Symfony
RUN composer install --no-dev --optimize-autoloader

RUN php bin/console doctrine:migrations:migrate --no-interaction


# Exposer le port Render
EXPOSE 8080

# Lancer Symfony
CMD php -S 0.0.0.0:8080 -t public
