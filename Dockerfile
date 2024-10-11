FROM php:8.1-cli

# region included composer
# hadolint ignore=DL3008
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      git \
      unzip \
 && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php -r "copy('https://composer.github.io/installer.sig', 'composer-setup.php.sig');" \
 && php -r "if (trim(hash_file('SHA384', 'composer-setup.php')) === trim(file_get_contents('composer-setup.php.sig'))) { echo 'Installer verified' . PHP_EOL; exit(0); } else { echo 'Installer corrupt' . PHP_EOL; unlink('composer-setup.php'); unlink('composer-setup.php.sig'); exit(-1); }" \
 && php composer-setup.php \
 && php -r "unlink('composer-setup.php'); unlink('composer-setup.php.sig');" \
 && mv composer.phar /usr/local/bin/composer \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/* \
;
# endregion

# region included composer-library
WORKDIR /app
COPY . .
RUN composer update --prefer-lowest
# endregion
