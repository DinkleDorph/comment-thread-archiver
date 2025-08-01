FROM ubuntu:24.04

# Install Apache and PHP
RUN apt update && apt install -y apache2 php libapache2-mod-php

# Enable Apache mod_rewrite (optional but common)
RUN a2enmod rewrite

# Update DocumentRoot to point to /var/www/html/src
# Create placeholder src/ folder to silence Apache warning (will be overwritten
# by Podman mount at runtime)
RUN mkdir -p /var/www/html/src && \
    sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/src|' /etc/apache2/sites-available/000-default.conf && \
    echo '<Directory /var/www/html/src>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

# Install Composer
RUN apt update && apt install -y curl php-cli unzip && \
    curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Start Apache in foreground
CMD ["bash", "-c", "exec apache2ctl -D FOREGROUND"]

EXPOSE 8080
