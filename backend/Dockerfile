ARG TARGET
FROM $TARGET AS local-host

ARG PHP_MAX_EXECUTION_TIME
ARG PHP_UPLOAD_MAX_FILESIZE
ARG PHP_POST_MAX_SIZE
ARG PROJECT_NAME
ARG BIN_CONSOLE_DIR_SYNCHTANK=$WORKDIR_SYNCHTANK/bin/console

WORKDIR $WORKDIR_SYNCHTANK

# Nginx config
COPY docker/app/server.conf /etc/nginx/conf.d/${PROJECT_NAME}.conf

# Replacing variable templates in config files
RUN sed -i \
# fpm php.ini
        -e "s/;date.timezone =.*/date.timezone = UTC/" /etc/php/${PHP8}/fpm/php.ini \
        -e "s/;extension =.*/extension = php_gmp.so/" /etc/php/${PHP8}/fpm/php.ini \
        -e "s/display_errors = Off/display_errors = On/" /etc/php/${PHP8}/fpm/php.ini \
        -e "s/upload_max_filesize = .*/upload_max_filesize = ${PHP_UPLOAD_MAX_FILESIZE}/" /etc/php/${PHP8}/fpm/php.ini \
        -e "s/post_max_size = .*/post_max_size = ${PHP_POST_MAX_SIZE}/" /etc/php/${PHP8}/fpm/php.ini \
        -e "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /etc/php/${PHP8}/fpm/php.ini \
        -e "s/max_execution_time = .*/max_execution_time = ${PHP_MAX_EXECUTION_TIME}/" /etc/php/${PHP8}/fpm/php.ini \
# server.conf
        -e "s|\${WORKDIR_SYNCHTANK}|$WORKDIR_SYNCHTANK|" /etc/nginx/conf.d/${PROJECT_NAME}.conf \
        -e "s|\${LOGDIR_SYNCHTANK}|$LOGDIR_SYNCHTANK|" /etc/nginx/conf.d/${PROJECT_NAME}.conf \
        -e "s|\${PHP8}|$PHP8|" /etc/nginx/conf.d/${PROJECT_NAME}.conf \
        -e "s|\${PROJECT_NAME}|$PROJECT_NAME|" /etc/nginx/conf.d/${PROJECT_NAME}.conf \
#
    && mkdir -p /run/php


EXPOSE 80


# Cron

# Add crontab file in the cron directory
COPY --chown=$CONTAINER_USER_NAME:crontab docker/app/crontab $CRONTAB_PATH

# Replacing variable templates in config files
RUN sed -i \
# crontab
    -e "s|\${BIN_CONSOLE_DIR_SYNCHTANK}|$BIN_CONSOLE_DIR_SYNCHTANK|" $CRONTAB_PATH \
#
    && mkdir -p /run/php

# Give execution rights on the cron job
RUN chmod 0644 $CRONTAB_PATH

# Apply cron job
RUN crontab -u $CONTAINER_USER_NAME $CRONTAB_PATH

# Debug cron: Create the log file to be able to run tail
#RUN touch /var/log/cron.log && chmod 777 -R /var/log/cron.log

# Cron


FROM local-host AS remote-host
COPY . $WORKDIR_SYNCHTANK