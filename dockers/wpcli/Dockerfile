FROM php:7.0-fpm
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng12-dev \
        unzip \
    && docker-php-ext-install -j$(nproc) iconv mcrypt \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Install requirements for wp-cli support
RUN apt-get update \
  && apt-get install -y sudo less mysql-client \
  && rm -rf /var/lib/apt/lists/*

# Add WP-CLI 
RUN curl -o /usr/local/bin/wp-cli.phar https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
# COPY ./wp-su.sh /usr/local/bin/wp
RUN chmod +x /usr/local/bin/wp-cli.phar

RUN mv /usr/local/bin/wp-cli.phar /usr/local/bin/wp
RUN chown www-data:www-data /usr/local/bin/wp
