# Dockerfile para "Gestión de Inventarios" (PHP + Apache + SQLite)
FROM php:8.1-apache

# Instala utilidades y dependencias necesarias (sqlite, builds)
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       libsqlite3-dev \
       sqlite3 \
       unzip \
       libzip-dev \
    && docker-php-ext-install pdo pdo_sqlite \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Configuración del documento raíz (opcional: si tu proyecto está en una subcarpeta cambia la ruta)
WORKDIR /var/www/html

# Copia el proyecto al contenedor
# Si tu zip contiene una carpeta, asegúrate de copiar la carpeta correcta (aquí asumimos que el contenido
# que quieres servir está en la raíz del contexto de build).
COPY . /var/www/html/

# Ajusta permisos: permitir que Apache / PHP escriban donde haga falta (ej. SQLite DB)
# Se crea además una carpeta "api/data" por si tu aplicacion la usa para la BD
RUN mkdir -p /var/www/html/api/data \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/api/data || true

# Exponer puerto HTTP
EXPOSE 80

# Crear un archivo sqlite por defecto (si la aplicación lo usa)
RUN touch /var/www/html/api/data/database.sqlite \
    && chown www-data:www-data /var/www/html/api/data/database.sqlite

# Comando por defecto (apache en primer plano)
CMD ["apache2-foreground"]
