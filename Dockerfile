FROM php:8.4-fpm

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier composer.json et composer.lock d'abord (pour le cache Docker)
COPY composer.json composer.lock* ./

# Installer les dépendances (sans scripts pour éviter les erreurs)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts || \
    (composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --ignore-platform-reqs && \
     echo "Installed with --ignore-platform-reqs")

# Copier le reste des fichiers de l'application (sans .env qui sera injecté)
COPY . .
# Ne pas copier .env dans l'image (sera injecté via Kubernetes secrets)
RUN rm -f .env .env.local 2>/dev/null || true

# Exécuter les scripts Composer si nécessaire
RUN composer dump-autoload --optimize --no-interaction || true

# Créer les répertoires nécessaires avec les bonnes permissions
RUN mkdir -p logs data/cache data/uploads && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod -R 775 logs data/cache data/uploads

# Configuration PHP pour la production
RUN echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "upload_max_filesize = 20M" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "post_max_size = 20M" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/custom.ini

# Exposer le port 9000 pour PHP-FPM
EXPOSE 9000

# Healthcheck
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD php -r "if (file_exists('/var/www/html/vendor/autoload.php')) { exit(0); } exit(1);"

# Commande par défaut
CMD ["php-fpm"]


