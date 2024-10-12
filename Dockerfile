FROM php:8.1-cli

# region included composer
# hadolint ignore=DL3008
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      git \
      unzip \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/* \
;
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
# endregion

# region included composer-library
WORKDIR /app
COPY . .
RUN composer update --prefer-lowest
# endregion
