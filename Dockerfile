FROM php:8.3-apache

# Set working directory
WORKDIR /var/www/html

# Enable Apache Rewrite Engine
RUN a2enmod rewrite

# Copy project files into container
COPY . /var/www/html/

# Set appropriate permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose standard web port
EXPOSE 80

CMD ["apache2-foreground"]
