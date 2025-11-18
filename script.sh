#!/bin/bash

docker-php-ext-install pdo_mysql zip

# Habilitar mod_rewrite
a2enmod rewrite

# Canviar permisos de la carpeta de Assets
chown -R www-data:www-data /var/www/html/Public/Assets

# Instal.lar git (necessari para Composer)
apt-get update
apt-get install -y git unzip

# Instal.lar Composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"

# instal.lar Xdebug
pecl install xdebug
docker-php-ext-enable xdebug

# Configuració de Xdebug
echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" >> /usr/local/etc/php/conf.d/xdebug.ini
echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini
echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini
echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini
echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/xdebug.ini
echo "xdebug.log=/tmp/xdebug.log" >> /usr/local/etc/php/conf.d/xdebug.ini

# Afegir Composer a la variable PATH
echo 'export PATH="$PATH:/usr/local/bin"' >> ~/.bashrc

#instal.lar dependències
#composer require phpmailer/phpmailer
composer require vlucas/phpdotenv #vlucas/phpdotenv to work with ENV constants
#composer require google/apiclient




# Comprovar que Composer s'ha instal.lat correctament
echo "Comprovant que Composer s'ha instal.lat correctament..."


# Iniciar Apache
apache2-foreground