# ---------------------------------------------- Build Time Arguments --------------------------------------------------
ARG PHP_VERSION="8.4"

# stage 1: build stage
FROM php:${PHP_VERSION}-apache AS base

# Set SHELL flags for RUN commands to allow -e and pipefail
SHELL ["/bin/bash", "-eo", "pipefail", "-c"]

# Changing user_id to match WSL user ownership
ARG user_id=1000
RUN usermod -u $user_id www-data

# Set Apache document root
ENV APACHE_DOCUMENT_ROOT /app/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set Apache options
RUN sed -i -e 's/Options Indexes FollowSymLinks/Options FollowSymLinks/g' /etc/apache2/apache2.conf \
    && echo 'ServerSignature Off' >> /etc/apache2/apache2.conf \
    && echo 'ServerTokens Prod' >> /etc/apache2/apache2.conf

RUN a2enmod rewrite \
    && sed -i 's/Listen 80$/Listen 8080/' /etc/apache2/ports.conf \
    && sed -i 's/:80>/:8080>/g' /etc/apache2/sites-available/*.conf

# ------------------------------------- Install Packages Needed Inside Base Image --------------------------------------
RUN IMAGE_DEPS="gettext tini" \
    && RUNTIME_DEPS="libfcgi-bin" \
    && apt-get update -y \
    && apt-get upgrade -y \
    && apt-get install -y --no-install-recommends ${IMAGE_DEPS} ${RUNTIME_DEPS} \
    && rm -rf /var/lib/apt/lists/*

# ---------------------------------------- Install / Enable PHP Extensions ---------------------------------------------
RUN apt-get update -y \
    && apt-get install -y --no-install-recommends \
        $PHPIZE_DEPS \
        libicu-dev \
        libjpeg-dev \
        libmagickwand-dev \
        libpng-dev \
        libssh2-1 \
        libssh2-1-dev \
        libwebp-dev \
        libxml2-dev \
        libzip-dev \
        openssh-client \
        sudo \
        unzip \
        vim \
        git \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        ftp \
        gd \
        intl \
        mysqli \
        opcache \
        pcntl \
        pdo_mysql \
        soap \
        zip \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && pecl install ssh2 \
    && pecl install imagick \
    && docker-php-ext-enable ssh2 imagick \
    && rm -rf /tmp/pear \
    && rm -rf /var/lib/apt/lists/*

RUN apt-get update -y \
    && apt-get install -y --no-install-recommends ssmtp mailutils \
    && echo "hostname=rimdeal.localhost" > /etc/ssmtp/ssmtp.conf \
    && echo "root=postmaster@localhost" >> /etc/ssmtp/ssmtp.conf \
    && echo "mailhub=mailhog:1025" >> /etc/ssmtp/ssmtp.conf \
    && echo "sendmail_path=sendmail -i -t" >> /usr/local/etc/php/conf.d/php-sendmail.ini \
    && rm -rf /var/lib/apt/lists/*

RUN echo "alias ll='ls -alF'" >> /root/.bashrc \
    && echo "alias ll='ls -alF'" >> /var/www/.bashrc

# ------------------------------------------------ PHP Configuration ---------------------------------------------------

# Add Default Config
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Add in Base PHP Config
COPY docker/php/base-* $PHP_INI_DIR/conf.d/

# --------------------------------------------------- Scripts ----------------------------------------------------------

COPY docker/*-base docker/healthcheck-* docker/command-loop /usr/local/bin/
RUN chmod +x /usr/local/bin/*-base /usr/local/bin/healthcheck-* /usr/local/bin/command-loop

# ---------------------------------------------------- Composer --------------------------------------------------------

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ----------------------------------------------------- MISC -----------------------------------------------------------

WORKDIR /app

RUN post-build-base

# Validate apache config
RUN apachectl configtest

# Create directories and set permissions before switching user
RUN mkdir -p /app/env \
    && chown -R www-data:www-data /app/env

USER www-data

# -------------------------------------------------- ENTRYPOINT --------------------------------------------------------

ENTRYPOINT ["entrypoint-base"]

# ======================================================================================================================
# ==============================================  DEVELOPMENT IMAGE  ===================================================
# ======================================================================================================================

FROM base AS app-dev

USER root

# ---------------------------------------------------- Scripts ---------------------------------------------------------

COPY docker/*-dev /usr/local/bin/
RUN chmod +x /usr/local/bin/*-dev

# ------------------------------------------------------ PHP -----------------------------------------------------------

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY docker/php/dev-* $PHP_INI_DIR/conf.d/

# ------------------------------------------------- Entry Point --------------------------------------------------------

USER www-data

ENTRYPOINT ["entrypoint-dev"]

WORKDIR /app

CMD ["/app/docker/run.sh"]
