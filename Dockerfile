FROM php:8.4-cli AS base

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip curl procps libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
        libonig-dev libxml2-dev libzip-dev \
        nodejs npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && pecl install redis && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN echo "upload_max_filesize = 10M\npost_max_size = 12M\nmemory_limit = 256M" \
    > /usr/local/etc/php/conf.d/uploads.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# --- Dev target ---
FROM base AS dev

COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-interaction --prefer-dist

COPY package.json package-lock.json* ./
RUN npm ci

COPY . .
RUN composer dump-autoload --optimize

EXPOSE 8000 5173

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

# --- Production target ---
FROM base AS prod

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --optimize-autoloader --prefer-dist

COPY package.json package-lock.json* ./
RUN npm ci --production=false

COPY . .
RUN npm run build \
    && rm -rf node_modules \
    && composer dump-autoload --optimize

RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
