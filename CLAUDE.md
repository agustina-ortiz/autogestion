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
| `mysql1` | MySQL | munimer_inasi | Definida en config, **sin credenciales en `.env`**. No usarla |
| `mysql2` | MySQL | munimer_inasinuevo | INASI nuevo, en desarrollo. Solo `corte_recibos` |
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

### Tablas en INASI viejo (base `munimer_inasi`)

Se acceden por la conexion **por defecto** (`mysql`) con la base calificada en el
nombre de tabla: `munimer_inasi.in_compensa`. **No** por la conexion `mysql1`:
esa entrada de `config/database.php` no tiene variables en `.env`, asi que cae a
los defaults (`127.0.0.1` / `root` / sin password) y solo funciona por casualidad
en desarrollo. En produccion falla.

- `in_movimie` — Historial de movimientos (modelo `Movimiento`)
- `in_compensa` — Compensatorios (modelo `Compensatorio`)
- `in_desempeno` — Evaluaciones de desempeno (modelo `Evaluacion`)

### Tablas en INASI nuevo (mysql2)
- `corte_recibos` — Fecha de corte de visibilidad de recibos. Solo lectura desde
  el portal: la escribe INASI. Historial de solo alta (`id`, `fecha_hasta`,
  `usuario_id`, `created_at`); la vigente es la de mayor `id`.

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
| Evaluaciones | `/evaluaciones` | `App\Livewire\Evaluaciones` |
| Reloj | `/reloj` | `App\Livewire\Reloj` (layout especial) |
| Primera contrasena | `/primera-contrasena` | Livewire Volt |

Todas las rutas de modulos requieren middleware `auth` + `verified`.

**No hay concepto de rol ni de administrador.** `auth` + `verified` es todo el
control de acceso que existe: cualquier empleado autenticado alcanza cualquier
ruta. Si se agrega una pantalla de gestion (por ejemplo para RRHH), hay que
construirle la restriccion aparte.

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
├── Console/Commands/SendEvaluacionesPush.php — Push de evaluaciones nuevas
├── Services/
│   ├── ReciboPDF.php               — Logica de generacion PDF recibos
│   └── ReciboVisibilidad.php       — Hasta que fecha se muestran los recibos

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
- Visibilidad: solo se muestran los recibos emitidos hasta la **fecha de corte**
  que define RRHH desde INASI (tabla `corte_recibos` en `mysql2`, historial de
  solo alta: la vigente es la de mayor `id`). Ver `App\Services\ReciboVisibilidad`.
  Si no se puede leer la fecha, no se muestra ningun recibo (falla cerrado).
- La condicion se aplica en los **tres** lugares que leen recibos: `Recibos`,
  `ReciboDetalle` y `ReciboPDFController`. No agregar un cuarto sin el filtro.

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

# Comandos propios
php artisan push:evaluaciones   # push de evaluaciones de desempeno nuevas (agendado cada hora)
php artisan push:evaluaciones --solo-marcar   # marca el historial como notificado sin enviar
                                              # (correr UNA vez antes de activar el cron)
```

---

## Notas de Seguridad

- Las contrasenas en `CLAVEWEB` son **texto plano** — es una limitacion conocida del sistema legado INASI. No cambiar sin coordinacion con el sistema origen.
- La encriptacion de sesiones esta **deshabilitada** (`SESSION_ENCRYPT=false`).
- No hay rate limiting explicito en las rutas (fuera del throttle global de Laravel).
- **Toda consulta a recibos tiene que acotarse al legajo de la sesion.** Las
  rutas de recibo reciben el numero por URL: si la consulta no filtra por
  `Auth::user()->LEGAJO`, un empleado puede leer el recibo de otro tanteando
  numeros. Aplica a `Recibos`, `ReciboDetalle` y `ReciboPDFController`.

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
