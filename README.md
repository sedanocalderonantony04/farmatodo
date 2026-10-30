#  FARMATODO - Sistema de Gestión para Farmacia

Sistema completo de gestión para farmacias desarrollado con Laravel 11.

## 🚀 Características

- ✅ Autenticación API con Sanctum
- ✅ Roles: Admin, Farmacéutico, Cajero
- ✅ Gestión de productos, categorías, proveedores, clientes
- ✅ Compras con kardex automático
- ✅ Ventas con descuento de stock en tiempo real
- ✅ Pagos múltiples (efectivo, tarjeta, transferencia, Yape, Plin)
- ✅ Anulación de ventas con reversión de stock
- ✅ Dashboard con métricas en tiempo real
- ✅ Reportes avanzados

## 🛠️ Tecnologías

- **Backend:** Laravel 11, PHP 8.3
- **Base de datos:** MySQL 8
- **Autenticación:** Laravel Sanctum
- **Servidor local:** Laragon

## 📦 Instalación

```bash
# Clonar repositorio
git clone https://github.com/sedanocalderonantony04/farmatodo.git
cd farmatodo

# Instalar dependencias
composer install

# Copiar archivo de entorno
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate

# Configurar base de datos en .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dbfarmatodo
DB_USERNAME=root
DB_PASSWORD=

# Migrar base de datos
php artisan migrate

# Crear link de storage
php artisan storage:link

# Ejecutar seeder de usuarios
php artisan db:seed --class=AdminUserSeeder

# Iniciar servidor
php artisan serve