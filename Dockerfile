# Dockerfile for slack-unfurl app
# https://github.com/glensc/slack-unfurl

# install composer vendor
FROM composer:1.6 AS build

ARG COMPOSER_FLAGS="--no-interaction --no-suggest --ansi --no-dev"

WORKDIR /app

# install in two steps to cache composer run
COPY composer.* ./
RUN composer install $COMPOSER_FLAGS --no-scripts --no-autoloader

COPY . .
RUN composer install $COMPOSER_FLAGS --classmap-authoritative
# not needed for production deploy
RUN rm -vf composer.* vendor/composer/*.json

# build final runtime image
FROM php:7.4-cli-alpine

WORKDIR /app

# ensure logs dir is writable by web user
RUN install -d -o www-data -g www-data var/log

USER www-data
EXPOSE 4390
CMD ["php", "-S", "0.0.0.0:4390", "-t", "/app/web"]

COPY --from=build /app .
