FROM wordpress:6.6-php8.2-apache
WORKDIR /var/www/html

### Para customizar o php.ini
# COPY custom.ini $PHP_INI_DIR/conf.d/

### Para customizar o wp-config.php
#COPY wp-config.php .

COPY wordpress/eightmedi-lite/ ./wp-content/themes/eightmedi-lite/
COPY wordpress/media-offload-for-oci/ ./wp-content/plugins/media-offload-for-oci/
