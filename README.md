# SIGE Soft Financiero

Sistema de Gestión Financiera Empresarial — MVP inicial.

**Stack:** Laravel 11 · PostgreSQL · Vue 3 · Vite · Pinia · Vue Router · Laravel Sanctum

---

## Estructura del repositorio

```
sigesofFinaciero/
├── backend/    # API Laravel 11
└── frontend/   # SPA Vue 3 + Vite
```

---

## Requisitos previos

| Herramienta | Versión mínima |
|-------------|----------------|
| PHP         | 8.2+           |
| Composer    | 2.x            |
| Node.js     | 18+            |
| npm         | 9+             |
| PostgreSQL  | 14+            |

---

## Backend (Laravel 11)

### 1. Instalar dependencias

```bash
cd backend
composer install
```

### 2. Configurar entorno

```bash
cp .env.example .env
php artisan key:generate
```

Editar `.env` con los datos de la base de datos PostgreSQL:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sigesoft_financiero
DB_USERNAME=postgres
DB_PASSWORD=tu_contraseña
```

### 3. Crear la base de datos

```bash
psql -U postgres -c "CREATE DATABASE sigesoft_financiero;"
```

### 4. Ejecutar migraciones y semillas

```bash
php artisan migrate --seed
```

Esto crea las tablas y los usuarios iniciales:

| Email               | Contraseña    | Rol        |
|---------------------|---------------|------------|
| admin@gmail.com     | admin1234     | admin      |
| contador@gmail.com  | contador1234  | contador   |

### 5. Levantar el servidor de desarrollo

```bash
php artisan serve
# API disponible en http://localhost:8000
```

---

## Frontend (Vue 3 + Vite)

### 1. Instalar dependencias

```bash
cd frontend
npm install
```

### 2. Configurar entorno (opcional)

```bash
cp .env.example .env
```

### 3. Levantar el servidor de desarrollo

```bash
npm run dev
# SPA disponible en http://localhost:5173
```

### 4. Compilar para producción

```bash
npm run build
```

---

## Endpoints de la API

| Método | Ruta            | Descripción                         | Auth requerida |
|--------|-----------------|-------------------------------------|----------------|
| POST   | /api/login      | Iniciar sesión (email + password)   | No             |
| GET    | /api/me         | Obtener usuario autenticado y rol   | Sí             |
| POST   | /api/logout     | Cerrar sesión                       | Sí             |

### Ejemplo de login

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"admin@gmail.com","password":"admin1234"}'
```

Respuesta:
```json
{
  "token": "1|...",
  "user": {
    "id": 1,
    "name": "Administrador",
    "email": "admin@gmail.com",
    "rol": "admin",
    "empresa": {
      "id": 1,
      "nombre": "Empresa Demo S.A."
    }
  }
}
```

---

## Roles disponibles

| Rol       | Descripción                        |
|-----------|------------------------------------|
| admin     | Acceso total al sistema            |
| contador  | Módulos contables                  |
| auxiliar  | Soporte contable                   |
| cajero    | Gestión de caja                    |

---

## Funcionalidades del MVP

- ✅ Autenticación con Laravel Sanctum (token Bearer)
- ✅ Endpoint `/api/me` con usuario autenticado, rol y empresa
- ✅ Logout con invalidación del token
- ✅ RBAC: middleware `EsAdmin` para endpoints administrativos
- ✅ Modelos: `User`, `Empresa`, relación `usuario_empresas`
- ✅ Selección de empresa activa (cabecera `X-Empresa-Id`)
- ✅ SPA Vue 3 con Vue Router + Pinia
- ✅ Landing pública, Login, Layout con sidebar
- ✅ Dashboard diferenciado por rol (Admin / Contador / Auxiliar / Cajero)
- ✅ Menú lateral con opciones según rol
- ✅ Semilla con admin@gmail.com / admin1234

---

## Notas de desarrollo

- El módulo SRI y módulos contables completos no están implementados en este MVP.
- El frontend usa proxy Vite para las llamadas API en modo desarrollo.
- La autenticación usa tokens Sanctum (Bearer token almacenado en localStorage).
