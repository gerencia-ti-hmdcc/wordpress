FROM wordpress:6.6-php8.2-apache
WORKDIR /var/www/html

# Define o fuso horário como variável de ambiente
ENV TZ=America/Sao_Paulo

# Configura o fuso horário no sistema operacional e no PHP
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone \
&& echo "date.timezone = America/Sao_Paulo" > /usr/local/etc/php/conf.d/timezone.ini

ENV WORDPRESS_CONFIG_EXTRA="define('WPLANG', 'pt_BR'); define('TZ', 'America/Sao_Paulo');"


COPY wordpress/.htaccess ./
COPY wordpress/eightmedi-lite/ ./wp-content/themes/eightmedi-lite/
COPY wordpress/media-offload-for-oci/ ./wp-content/plugins/media-offload-for-oci/
COPY wordpress/jetpack/ ./wp-content/plugins/jetpack/
COPY wordpress/all-in-one-seo-pack/ ./wp-content/plugins/all-in-one-seo-pack/
COPY wordpress/ml-slider ./wp-content/plugins/ml-slider
