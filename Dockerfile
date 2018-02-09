# dockerfile for eventum-slack-unfurl app
# https://github.com/eventum/slack-unfurl

# step 1: install composer vendor
FROM composer:1.6 AS build

COPY . /app
WORKDIR /app

RUN composer install --no-dev -a
# not needed for production deploy
RUN rm -vf composer.* vendor/composer/*.json

# step 2: build production image
FROM php:7.2-cli-alpine

COPY --from=build /app /app
WORKDIR /app

# ensure logs dir is writable by web user
RUN set -x \
	&& install -d -o www-data -g www-data var/logs \
	&& exit 0

USER www-data

EXPOSE 4390

CMD ["php", "-S", "0.0.0.0:4390", "-t", "/app/web"]
