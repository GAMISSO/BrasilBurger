FROM php:8.2-apache
WORKDIR /var/www/html
RUN ls -la


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
    opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Dossier de travail
WORKDIR /app

# Copier uniquement composer.json (composer.lock peut être absent sur Render)
COPY composer.json composer.lock ./

# Installer dépendances Symfony (tolère l'absence de composer.lock)
RUN composer install \
    --no-scripts \
    --no-autoloader \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress

# Copier le reste du projet
COPY . .

# Finaliser l'installation de Composer
RUN composer dump-autoload \
    --optimize \
    --no-dev

# Créer les répertoires nécessaires et définir les permissions
RUN mkdir -p var/cache var/log var/share \
    && chmod -R 777 var

# Configurer PHP pour la production
RUN echo 'memory_limit = 256M' >> /usr/local/etc/php/conf.d/docker-php.ini \
    && echo 'upload_max_filesize = 20M' >> /usr/local/etc/php/conf.d/docker-php.ini \
    && echo 'post_max_size = 20M' >> /usr/local/etc/php/conf.d/docker-php.ini \
    && echo 'default_charset = "UTF-8"' >> /usr/local/etc/php/conf.d/docker-php.ini

# Rendre le script de démarrage exécutable
RUN chmod +x start.sh

# Exposer le port
EXPOSE 8080

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD php -r "file_get_contents('http://127.0.0.1:8080/') or exit(1);"

# Démarrer l'application
CMD ["./start.sh"]
