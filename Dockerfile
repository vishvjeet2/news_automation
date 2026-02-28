FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libexif-dev \
    ffmpeg \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd exif

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader

CMD php artisan serve --host=0.0.0.0 --port=10000
