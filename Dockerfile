FROM wordpress:latest

ENV XDEBUG_PORT 9000
ENV XDEBUG_IDEKEY docker

RUN pecl install "xdebug" \
    && docker-php-ext-enable xdebug

RUN echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.remote_port=${XDEBUG_PORT}" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.idekey=${XDEBUG_IDEKEY}" >> /usr/local/etc/php/conf.d/xdebug.ini

# install git for development purposes
RUN apt-get update && apt-get install git -y

# install composer
RUN apt install wget unzip -y
RUN wget -O composer-setup.php https://getcomposer.org/installer && php composer-setup.php --install-dir=/usr/local/bin --filename=composer

RUN chmod  +w /usr/local/etc/php/ && cat ./tests/conf.init >> /usr/local/etc/php/php.ini
