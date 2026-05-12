# CLAUDE.md — Autogestion Project

## Descripcion General

Portal de autogestión de Recursos Humanos para municipalidad argentina. Permite a empleados ver recibos de sueldo, gestionar asignaciones familiares, solicitar movimientos, ver asistencias, etc.

**Framework:** Laravel 12 | **PHP:** 8.2+ | **Timezone:** America/Argentina/Buenos_Aires

---

## Stack Tecnologico

### Backend
- Laravel 12, PHP 8.2+
- Livewire 3.6.4 + Volt 1.7.0 (componentes reactivos)
- Eloquent ORM (con providers personalizados)

### Frontend
- Alpine.js 3.15.0
- Tailwind CSS 3.1.0
- Livewire Flux 2.6 (componentes UI)
- FontAwesome 7
- Vite 7 (build tool)

### Bibliotecas Clave
- **FPDF 1.8** — generacion de PDFs de recibos y planillas
- **Laravel DomPDF 3.1** — renderizado PDF alternativo
- **Intervention Image 3.11** — procesamiento de fotos de perfil
- **Minishlink Web Push 10.0** — notificaciones push del navegador

---

## Bases de Datos

### Conexiones Configuradas

| Conexion | Motor | Base | Uso |
|----------|-------|------|-----|
| `mysql` (default) | MySQL | in_maestro | Datos de la aplicacion |
| `mysql1` | MySQL | munimer_inasi | Sistema legado INASI |
| Oracle OCI | Oracle | — | Recibos de sueldo (conexion directa, NO Eloquent) |
| SQLite | SQLite | — | Tests / desarrollo |

### Tablas Principales (MySQL in_maestro)

**in_maestro** — Empleados/usuarios
```
LEGAJO (PK, int), NOMBRE, DNI, CATEGORIA, CLAVEWEB (password TEXTO PLANO), FECHABAJ, remember_token
```

**in_familia** — Familiares/dependientes
```
LEGAJO (FK), NOMBRE, DNI, FECHA_NAC
TIPOFAMI: 1=Conyuge, 2=Hijo, 3=Hijo discapacitado, 4=Otro
TIPOESCO: P=Primario, S=Secundario, J=Jardin, L=Universidad
CURSO, ESCUELA, PLANILLA1, PLANILLA2
```

**in_planillas** — Certificados de escolaridad
```
legajo, anio, planilla (1 o 2), dni, fecha, confirmada (0/1), observa
```

**in_solicitudes** — Solicitudes/pedidos
```
id, legajo (FK), fecha_solicitud, tipo_movimiento (FK), origen (1=Autogestion)
estado: 1=Pendiente, 2=Lista
forma_pago: 'deposito' | 'cheque', importe
```

**in_tipo_movimiento** — Tipos de solicitudes disponibles
```
id, tipo_movimiento, periodo, fecha_desde, fecha_hasta, fecha_acreditacion
forma_pago: 'deposito' | 'cheque' | 'ambos', importe_maximo
```

**in_noticia** — Novedades/noticias del dashboard
```
ID, FECHAVTO (fecha de vencimiento para filtrar)
```

**in_preguntas_frecuentes** — FAQs

**push_subscriptions** — Suscripciones de notificaciones push
**sessions** — Sesiones (driver: database)
**cache** — Cache (driver: database)

### Tablas en INASI (mysql1)
- `in_movimie` — Historial de movimientos
- `in_compensa` — Compensatorios

### Oracle (recibos de sueldo)
- `per_recibo_cab` — Cabecera de recibos (consulta SQL directa con ROWNUM para paginacion)

---

## Autenticacion

- **Guard:** `web` (sesion)
- **Provider personalizado:** `InMaestroUserProvider` — autentica por DNI
- **Campo password:** `CLAVEWEB` — **TEXTO PLANO, SIN HASH** (comparacion directa de strings)
- **Sesion:** Driver `database`, 120 min, sin encriptacion
- **Tabla usuario:** `in_maestro`, PK = `LEGAJO` (entero, no auto-increment)
- **Sin timestamps** en in_maestro

---

## Modulos y Rutas

| Modulo | Ruta | Componente |
|--------|------|-----------|
| Dashboard | `/dashboard` | `dashboard.blade.php` |
| Recibos | `/recibos` | `App\Livewire\Recibos` |
| Detalle recibo | `/recibo/{numero}/{anio}/{mes}/{tipo}` | `App\Livewire\ReciboDetalle` |
| PDF recibo | `/recibo/pdf/{nroRecibo}/{anio}/{mes}/{tipoLiq}` | `ReciboPDFController` |
| Asistencias | `/asistencias` | `App\Livewire\Asistencias` |
| Compensatorios | `/compensatorios` | `App\Livewire\Compensatorios` |
| Solicitudes | `/solicitudes` | `App\Livewire\Solicitudes` |
| Anticipo jubilatorio | `/anticipo` | `App\Livewire\AnticipoJubilatorio` |
| Hijos/dependientes | `/hijos` | `App\Livewire\Hijos` |
| Planillas | `/planillas` | `App\Livewire\Planillas` |
| Asig. familiares | `/asignaciones-familiares` | `App\Livewire\AsignacionesFamiliares` |
| Preguntas frecuentes | `/preguntas-frecuentes` | `App\Livewire\PreguntasFrecuentes` |
| Perfil | `/perfil` | `App\Livewire\Perfil` |
| Reloj | `/reloj` | `App\Livewire\Reloj` (layout especial) |
| Primera contrasena | `/primera-contrasena` | Livewire Volt |

Todas las rutas de modulos requieren middleware `auth` + `verified`.

---

## Estructura de Archivos Clave

```
app/
├── Helpers/FotoHelper.php          — URLs y verificacion de fotos de perfil
├── Http/Controllers/
│   ├── Auth/InMaestroUserProvider.php  — Proveedor auth personalizado
│   ├── PerfilFotoController.php    — Subida de fotos
│   ├── PlanillaController.php      — Certificados PDF + subida
│   ├── ReciboPDFController.php     — PDF de recibos
│   └── AsignacionesFamiliaresController.php
├── Livewire/                       — Todos los componentes interactivos
├── Models/                         — Eloquent models
├── Services/ReciboPDF.php          — Logica de generacion PDF recibos

public/
├── fotos-licencias/fotos-empleados/{legajo}.jpg  — Fotos de perfil
├── fotos-licencias/fotos-empleados/planillas/    — Archivos de planillas
├── img/ddjj_fami/                  — Documentos asignaciones familiares
├── images/logo.png, encabezado.png, no-foto.png

resources/views/
├── components/layouts/autogestion.blade.php  — Layout principal
├── layouts/reloj.blade.php          — Layout del reloj
├── livewire/                        — Vistas de componentes Livewire
```

---

## Convenciones del Proyecto

### Nomenclatura de columnas
- **Tablas INASI legacy:** UPPERCASE (`LEGAJO`, `NOMBRE`, `DNI`, `FECHABAJ`)
- **Tablas nuevas:** snake_case (`legajo`, `fecha_solicitud`, `tipo_movimiento`)
- **Booleanos:** enteros 0/1 (`confirmada`, `estado`)

### Patrones de codigo
- La mayoria de paginas interactivas usan **Livewire**, no controllers tradicionales
- Autenticacion usa **Volt** (sintaxis de clase)
- Los modelos usan **Query Scopes** para filtrado
- **Accessors** en User: `esta_inactivo`, `cantidad_hijos`, `tiene_hijos`
- Constantes de clase en `TipoMovimiento` y `Solicitud`

### Planillas (escolaridad)
- Periodo 1: Febrero-Junio (vence 30 de junio)
- Periodo 2: Noviembre-Enero (vence 31 de enero)
- Archivos: `{dni}{planilla}-{anio}.jpg|pdf`

### Recibos (Oracle)
- Consultas SQL directas con OCI, NO Eloquent
- Paginacion con ROWNUM a nivel SQL
- Tipos de recibo con colores: NOR, ADI, SAC, DDN, MUN, etc.
- Filtrado: excluye recibos de hoy y manana

---

## Comandos Utiles

```bash
# Desarrollo
composer dev          # servidor + queue + logs + vite en paralelo
npm run dev           # solo Vite

# Build produccion
npm run build
composer setup        # instala, genera key, migra, build

# Testing
composer test         # limpia config + PHPUnit

# Artisan
php artisan migrate
php artisan tinker
```

---

## Notas de Seguridad

- Las contrasenas en `CLAVEWEB` son **texto plano** — es una limitacion conocida del sistema legado INASI. No cambiar sin coordinacion con el sistema origen.
- La encriptacion de sesiones esta **deshabilitada** (`SESSION_ENCRYPT=false`).
- No hay rate limiting explicito en las rutas (fuera del throttle global de Laravel).

---

## Archivos de Configuracion Importantes

| Archivo | Detalle |
|---------|---------|
| `config/database.php` | Conexiones: sqlite, mysql, mysql1, mariadb, pgsql, sqlsrv |
| `config/auth.php` | Guard web, provider in_maestro personalizado |
| `config/app.php` | Timezone: America/Argentina/Buenos_Aires |
| `config/session.php` | Driver: database, 120 min, sin encriptar |
| `config/cache.php` | Store: database |
| `.env` | Variables de entorno (no commiteado) |
