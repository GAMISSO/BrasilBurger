FROM php:8.4-cli

# Installer dépendances système et extensions PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    zip \
    intl \
    opcache

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Dossier de travail
WORKDIR /app

# Copier composer.json (et composer.lock s'il existe)
COPY composer.json ./
COPY composer.lock* ./

# Installer dépendances Symfony (sans scripts pour éviter erreurs)
RUN composer install --no-scripts --no-autoloader --no-dev --prefer-dist

# Copier le reste du projet
COPY . .

# Finaliser l'installation de Composer
RUN composer dump-autoload --optimize --no-dev

# Créer les répertoires nécessaires et définir les permissions
RUN mkdir -p var/cache var/log var/share \
    && chmod -R 777 var

# Configurer PHP pour la production
RUN echo 'memory_limit = 256M' >> /usr/local/etc/php/conf.d/docker-php.ini \
    && echo 'upload_max_filesize = 20M' >> /usr/local/etc/php/conf.d/docker-php.ini \
    && echo 'post_max_size = 20M' >> /usr/local/etc/php/conf.d/docker-php.ini

# Rendre le script de démarrage exécutable
RUN chmod +x start.sh

# Exposer le port Render
EXPOSE 8080

# Lancer l'application via le script de démarrage
CMD ["./start.sh"]
