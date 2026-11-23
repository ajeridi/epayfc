# PHP 8.2 + Apache (remplace XAMPP)
FROM php:8.2-apache

# Extensions PHP : pdo_mysql (base de données) et gd avec JPEG/WebP (ImageUtils)
RUN apt-get update \
 && apt-get install -y --no-install-recommends libjpeg62-turbo-dev libpng-dev libwebp-dev libfreetype6-dev \
 && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
 && docker-php-ext-install pdo_mysql gd \
 && rm -rf /var/lib/apt/lists/*

# mod_rewrite pour le routeur (.htaccess)
RUN a2enmod rewrite

# Limites d'upload alignées sur max_file_size (5 Mo) de l'app ; PHP est à 2 Mo par défaut
RUN printf "upload_max_filesize=10M\npost_max_size=50M\n" > /usr/local/etc/php/conf.d/uploads.ini

# Redirige http://localhost:8080/ vers l'app
RUN echo '<?php header("Location: /prwb_2526_a08/");' > /var/www/html/index.php

# Le code dans le sous-dossier attendu par web_root et RewriteBase
WORKDIR /var/www/html/prwb_2526_a08
COPY . .

# Config Docker (dbhost = db) à la place de la config XAMPP, en gardant le mode dev
COPY config/docker.ini config/dev.ini

# Apache (www-data) doit pouvoir écrire dans uploads/ (et database/ pour le backup)
RUN chown -R www-data:www-data uploads database
