# Gunakan image PHP + Apache
FROM webdevops/php-apache:8.2

# Set working directory
WORKDIR /app

# Copy semua file project ke container
COPY . /app

# Install dependencies Laravel
RUN composer install --no-dev --optimize-autoloader
RUN php artisan key:generate
RUN php artisan storage:link
RUN php artisan optimize:clear

# Expose port web server
EXPOSE 80
