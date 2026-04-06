FROM php:8.1-apache

# Install dependencies
RUN apt-get update && \
    apt-get install -y \
        # packages for MySQL database
        && docker-php-ext-install mysqli pdo_mysql 

RUN apt-get install -y git 

RUN apt-get install -y \
		libfreetype-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) gd


RUN apt-get install -y \
        libzip-dev \
        zip \
        && docker-php-ext-install zip
        
# Set document root
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

#apache
RUN a2enmod rewrite