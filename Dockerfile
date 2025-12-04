FROM php:8.3-fpm

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

# Copier les fichiers de l'application
COPY . .

# Installer les dépendances
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Créer les répertoires nécessaires
RUN mkdir -p logs data/cache && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Exposer le port 9000 pour PHP-FPM
EXPOSE 9000

# Commande par défaut
CMD ["php-fpm"]


