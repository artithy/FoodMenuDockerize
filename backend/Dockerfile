FROM php:8.2-cli

RUN docker-php-ext-install mysqli pdo pdo_mysql

WORKDIR /app

COPY . /app

EXPOSE 8000

# Start PHP built-in server with public folder as root
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]

