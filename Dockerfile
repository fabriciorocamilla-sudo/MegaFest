FROM php:8.3-cli-alpine

WORKDIR /app
COPY . /app

RUN mkdir -p /app/data && chmod -R 775 /app/data

EXPOSE 10000
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-10000} router.php"]
