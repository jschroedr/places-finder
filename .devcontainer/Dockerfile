FROM wordpress:latest

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/conf.d/xdebug.ini

# install development binaries
RUN apt-get update && apt-get install git -y

# install composer
RUN apt install wget unzip -y
RUN wget -O composer-setup.php https://getcomposer.org/installer && php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# enable xdebug step debugging
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# ensure the pdo mysql driver is installed and enabled
RUN docker-php-ext-install pdo_mysql
RUN sed -i "s/;extension=pdo_mysql/extension=pdo_mysql/" /usr/local/etc/php/php.ini-production
RUN sed -i "s/;extension=pdo_mysql/extension=pdo_mysql/" /usr/local/etc/php/php.ini-development