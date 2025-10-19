FROM php:8.4-apache

# Set Go binary path
ENV GOBIN=/usr/local/bin

# Install system dependencies and PHP extensions in one RUN layer
RUN apt-get update -y \
  && apt-get install -y --no-install-recommends \
    libpng-dev \
    libzip-dev \
    git \
    golang-go \
  && docker-php-ext-install mysqli gd zip \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/*

# Install mailhog mhsendmail binary
RUN go install github.com/mailhog/mhsendmail@latest

# Copy custom PHP configuration
COPY ./conf.d /usr/local/etc/php/conf.d

# Set www-data UID/GID
RUN usermod -u 1000 www-data \
  && groupmod -g 1000 www-data

# Switch to non-root user
USER www-data
