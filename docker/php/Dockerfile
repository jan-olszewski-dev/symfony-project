FROM php:8.1-fpm

ARG USER_ID=1000
ARG GROUP_ID=1000

RUN usermod --uid ${USER_ID} --shell /bin/bash www-data
RUN groupmod --gid ${GROUP_ID} www-data
RUN chmod 777 /tmp

RUN apt-get update
RUN apt-get install -y libicu-dev g++ libzip-dev zip git librabbitmq-dev libssh-dev
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl mysqli pdo pdo_mysql zip bcmath sockets
RUN pecl install xdebug amqp
RUN docker-php-ext-enable xdebug mysqli pdo pdo_mysql amqp

RUN echo "xdebug.mode=debug,coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN curl -sS https://get.symfony.com/cli/installer | bash -s -- --install-dir=/usr/local/bin/

CMD ["php-fpm"]
