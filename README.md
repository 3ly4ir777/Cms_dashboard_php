# EliasCms 🐧

Panel de administración (CMS) para e-commerce — proyecto escolar.
Backend en **PHP 8.2** con **MariaDB**, desplegado con **Podman**.

## ✨ Funcionalidades

- 🔐 **Login de administrador** con usuario, contraseña (Argon2id) y código especial numérico.
- 📦 **CRUD de productos** (nombre, descripción, precio, stock, categoría).
- 🧾 **Gestión de órdenes** con items y cambio de estado
  (`pendiente`, `pagada`, `enviada`, `entregada`, `cancelada`).
- 📊 **Dashboard** con métricas: total productos, stock total, órdenes,
  órdenes pendientes e ingresos.
- 🍪 Sesión persistente con `session_regenerate_id` al loguear.

## 🧰 Requisitos

- Podman + `podman-compose`
- Navegador moderno (Chrome/Firefox)

## 🚀 Arrancar en local

```bash
podman compose up -d
```

La primera vez, ejecuta la migración para crear las tablas y el usuario
de prueba (solo una vez, o cada vez que borres el volumen `db_data`):

```bash
podman exec -it mi_php_backend php /var/www/migrar.php
```

Abre **http://localhost:8080/login.html**

## 👤 Credenciales de prueba

| Usuario  | Contraseña | Código especial |
|----------|------------|-----------------|
| amiguito | secreto123 | 777             |

## 🗂️ Estructura

```
backend/
├── config/
│   ├── db.php              # conexión PDO (lee env vars)
│   └── auth_helper.php     # require_auth() + json_response()
├── controllers/            # Auth, Product, Order, Dashboard
├── models/                 # Product, Order
├── public/                 # DocumentRoot de Apache
│   ├── login.html
│   ├── dashboard.html
│   ├── *_api.php           # endpoints JSON
│   ├── check_auth.php
│   ├── logout_api.php
│   └── index.php           # prueba de conexión
└── migrar.php              # migración + seed data
compose.yaml                # Podman: web (PHP+Apache) + db (MariaDB)
```

## 🌐 Endpoints (JSON)

| Método | Endpoint            | Descripción                          |
|--------|---------------------|--------------------------------------|
| POST   | `/login_api.php`    | Login admin                          |
| GET    | `/check_auth.php`   | Estado de la sesión                  |
| POST   | `/logout_api.php`   | Cerrar sesión                        |
| GET    | `/productos_api.php`| Lista productos                      |
| GET    | `/productos_api.php?id=N` | Un producto                   |
| POST   | `/productos_api.php`| Crear producto                       |
| PUT    | `/productos_api.php`| Actualizar producto                  |
| DELETE | `/productos_api.php?id=N` | Eliminar producto             |
| GET    | `/ordenes_api.php`  | Lista órdenes                        |
| POST   | `/ordenes_api.php`  | Crear orden                          |
| PUT    | `/ordenes_api.php`  | Cambiar estado (`{id, estado}`)      |
| DELETE | `/ordenes_api.php?id=N` | Eliminar orden                  |
| GET    | `/dashboard_api.php`| Estadísticas + órdenes recientes     |

Todos los endpoints (excepto login/logout) requieren sesión activa.

## 🌍 Despliegue en InfinityFree

1. Sube el contenido de `backend/public/` a `htdocs/`, y `config/`,
   `controllers/`, `models/` como subcarpetas directas en `htdocs/`.
2. Crea `config/db_credentials.php` (ver `.env.example`) con los datos
   del panel de InfinityFree.
3. Ejecuta `migrar.php` una sola vez visitando
   `https://tudominio.infinityfreeapp.com/migrar.php`
   y **bórralo del servidor inmediatamente después**.
4. En `config/`, `controllers/` y `models/` agrega un `.htaccess` con
   `Require all denied` para que no sean accesibles por URL.

## 🏷️ Versión

`v0.1.0` — primera entrega funcional.
```

