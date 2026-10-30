# FARMATODO API Documentation

## Base URL
http://farmatodo.test/api/v1

## Autenticación
Todas las rutas (excepto login) requieren:
Header: `Authorization: Bearer {token}`

## Endpoints

### Auth
- POST /auth/login - Iniciar sesión
- POST /auth/logout - Cerrar sesión
- GET /auth/profile - Mi perfil
- PUT /auth/profile - Actualizar perfil

### Usuarios (solo admin)
- GET /users - Listar usuarios
- POST /users - Crear usuario
- PUT /users/{user} - Actualizar usuario
- DELETE /users/{user} - Eliminar usuario

### Catálogos
- GET/POST/PUT/DELETE /categorias
- GET/POST/PUT/DELETE /proveedores
- GET/POST/PUT/DELETE /productos
- GET/POST/PUT/DELETE /clientes

### Compras
- GET/POST /compras
- GET /compras/{compra}

### Ventas
- GET/POST /ventas
- GET /ventas/{venta}
- POST /ventas/{venta}/anular

### Inventario
- GET/POST /movimientos-inventario
- GET /movimientos-inventario/{movimiento}

### Dashboard
- GET /dashboard/resumen
- GET /dashboard/top-productos/{cantidad}
- GET /dashboard/ventas-por-dia

### Reportes
- GET /reportes/ventas
- GET /reportes/compras
- GET /reportes/inventario
- GET /reportes/stock-bajo
- GET /reportes/productos-vencidos