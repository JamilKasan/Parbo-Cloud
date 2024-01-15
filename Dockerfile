FROM php:8.1.1-fpm

# set main params
ARG BUILD_ARGUMENT_ENV=dev
ENV ENV=$BUILD_ARGUMENT_ENV
ENV APP_HOME /var/www/html
ARG HOST_UID=1000
ARG HOST_GID=1000
ENV USERNAME=www-data
ARG INSIDE_DOCKER_CONTAINER=1
ENV INSIDE_DOCKER_CONTAINER=$INSIDE_DOCKER_CONTAINER
ARG XDEBUG_CONFIG=main
ENV XDEBUG_CONFIG=$XDEBUG_CONFIG

# check environment
RUN if [ "$BUILD_ARGUMENT_ENV" = "default" ]; then echo "Set BUILD_ARGUMENT_ENV in docker build-args like --build-arg BUILD_ARGUMENT_ENV=dev" && exit 2; \
    elif [ "$BUILD_ARGUMENT_ENV" = "dev" ]; then echo "Building development environment."; \
    elif [ "$BUILD_ARGUMENT_ENV" = "test" ]; then echo "Building test environment."; \
    elif [ "$BUILD_ARGUMENT_ENV" = "staging" ]; then echo "Building staging environment."; \
    elif [ "$BUILD_ARGUMENT_ENV" = "prod" ]; then echo "Building production environment."; \
    else echo "Set correct BUILD_ARGUMENT_ENV in docker build-args like --build-arg BUILD_ARGUMENT_ENV=dev. Available choices are dev,test,staging,prod." && exit 2; \
    fi

# install all the dependencies and enable PHP modules
RUN apt-get update && apt-get upgrade -y && apt-get install -y \
    vim \
    curl \
      apt-transport-https \
      procps \
      nano \
      git \
      unzip \
      libicu-dev \
      zlib1g-dev \
      libxml2 \
      libxml2-dev \
      libreadline-dev \
      supervisor \
      cron \
      sudo \
      libzip-dev \
      locales \
      unixodbc \
      libgss3 \
      odbcinst \
      libldap2-dev \
      libfreetype6-dev \
      libjpeg62-turbo-dev \
      gnupg2 \
      devscripts debhelper dh-exec dh-autoreconf libreadline-dev libltdl-dev \
      tdsodbc unixodbc-dev wget unzip apt-transport-https \
      libfreetype6-dev libmcrypt-dev libjpeg-dev libpng-dev \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
      pdo_mysql \
      sockets \
      intl \
      opcache \
      zip \
      ldap \
      -j$(nproc) gd \
    && rm -rf /tmp/* \
    && rm -rf /var/list/apt/* \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean \
    && echo "en_US.UTF-8 UTF-8" > /etc/locale.gen

RUN pecl install redis && docker-php-ext-enable redis
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN #curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add -

# Install Microsoft ODBC driver for SQL Server
#RUN curl https://packages.microsoft.com/config/debian/11/prod.list > /etc/apt/sources.list.d/mssql-release.list
#RUN apt-get update && ACCEPT_EULA=Y apt-get install -y msodbcsql18




# Install SQLSRV and PDO_SQLSRV extensions
#RUN pecl install sqlsrv pdo_sqlsrv
#RUN docker-php-ext-enable sqlsrv pdo_sqlsrv

# Install MongoDB extension
RUN pecl install mongodb && docker-php-ext-enable mongodb
#RUN apt-get update && apt-get install -y \
#    libssl-dev
#RUN docker-php-ext-install openssl


# Clean up
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN wget 'https://fastdl.mongodb.org/tools/db/mongodb-database-tools-debian10-x86_64-100.8.0.tgz'
RUN tar -xvzf mongodb-database-tools-debian10-x86_64-100.8.0.tgz
RUN cp mongodb-database-tools-debian10-x86_64-100.8.0/bin/* /usr/local/bin/

RUN apt-get update && apt install npm -y
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/master/install.sh | bash && . ~/.nvm/nvm.sh \
                                                                                         && nvm install v16 \
                                                                                         && nvm alias default v16





# create document root, fix permissions for www-data user and change owner to www-data
RUN mkdir -p $APP_HOME/public && \
    mkdir -p /home/$USERNAME && chown $USERNAME:$USERNAME /home/$USERNAME \
    && usermod -o -u $HOST_UID $USERNAME -d /home/$USERNAME \
    && groupmod -o -g $HOST_GID $USERNAME \
    && chown -R ${USERNAME}:${USERNAME} $APP_HOME

# put php config for Laravel
COPY ./docker/$BUILD_ARGUMENT_ENV/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./docker/$BUILD_ARGUMENT_ENV/php.ini /usr/local/etc/php/php.ini

# install Xdebug in case dev/test environment
COPY ./docker/general/do_we_need_xdebug.sh /tmp/
COPY ./docker/dev/xdebug-${XDEBUG_CONFIG}.ini /tmp/xdebug.ini
RUN chmod u+x /tmp/do_we_need_xdebug.sh && /tmp/do_we_need_xdebug.sh

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN chmod +x /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1

# add supervisor
RUN mkdir -p /var/log/supervisor
COPY --chown=root:crontab ./docker/general/cron /var/spool/cron/crontabs/root
COPY --chown=root:root ./docker/general/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
RUN chmod 0600 /var/spool/cron/crontabs/root

# set working directory
WORKDIR $APP_HOME
RUN apt-get update && apt-get install -y \
    php-openssl
USER ${USERNAME}

# copy source files and config file
COPY --chown=${USERNAME}:${USERNAME} . $APP_HOME/
COPY --chown=${USERNAME}:${USERNAME} .env.$ENV $APP_HOME/.env

# install all PHP dependencies
#RUN if [ "$BUILD_ARGUMENT_ENV" = "dev" ] || [ "$BUILD_ARGUMENT_ENV" = "test" ]; then COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader --no-interaction --no-progress; \
 #   else COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader --no-interaction --no-progress --no-dev; \
  #  fi


#RUN #echo 'root:Docker!' | chpasswd
#RUN echo 'Docker!' | passwd --stdin ${USERNAME}

RUN #npm install
RUN #npm run build
RUN #composer install



USER ${USERNAME}
