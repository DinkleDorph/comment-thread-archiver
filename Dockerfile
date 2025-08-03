FROM ubuntu:24.04

# Install Apache and PHP
RUN apt update && apt install -y apache2 php8.3 libapache2-mod-php nano vim

# Enable Apache mod_rewrite (optional but common)
RUN a2enmod rewrite

# Update DocumentRoot to point to /var/www/html/public
# Create placeholder public folder to silence Apache warning (will be overwritten
# by Podman mount at runtime)
RUN mkdir -p /var/www/html/public && \
    sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Copy custom Apache config
COPY custom-apache.conf /etc/apache2/conf-available/custom-apache.conf
RUN a2enconf custom-apache

# Install Composer
RUN apt update && apt install -y curl php-cli unzip && \
    curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Start Apache in foreground
CMD ["bash", "-c", "exec apache2ctl -D FOREGROUND"]

EXPOSE 8080
