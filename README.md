# Control de préstamo de equipo — Laboratorio de Cómputo del CESBA

Sistema web para llevar el control del equipo que el laboratorio de cómputo presta a los alumnos.
Proyecto de clase de la **Licenciatura en Sistemas Computacionales**, hecho con **Laravel 13** y **PHP 8.3**.

---

## 1. Qué es y qué problema resuelve

El laboratorio del CESBA tiene unos 30 equipos (laptops, proyectores, kits de Arduino,
osciloscopios, equipo de redes) y atiende a unos 300 alumnos. Lo atienden dos personas: el
encargado y un auxiliar.

**Hoy** el alumno firma en una hoja de papel. Resultado: nadie sabe con certeza qué está prestado
ni desde cuándo, y se pierde equipo.

**Con este sistema:**

| Quién | Qué puede hacer |
|---|---|
| Encargado (`admin`) | Todo: catálogo, padrón de alumnos, préstamos, devoluciones y tablero. |
| Auxiliar (`auxiliar`) | Presta y recibe equipo, consulta catálogo y alumnos. **No** da de alta ni de baja. |
| Alumno | Entra desde su celular, escribe su matrícula y ve qué debe. **Sin cuenta ni contraseña.** |

### Las tres reglas del laboratorio

Están escritas una sola vez en el código y se validan siempre, también si alguien manda la
petición con `curl` saltándose la pantalla:

1. **Equipo restringido por semestre.** Cada equipo tiene un `semestre_minimo`. Un alumno de
   semestre menor no puede recibirlo. → `app/Rules/SemestreSuficienteParaEquipo.php`
2. **Sin adeudos vencidos.** Un alumno con un préstamo vencido no se lleva nada más hasta
   regresarlo. → `app/Rules/AlumnoSinAdeudoVencido.php`
3. **Solo equipo disponible.** No se presta lo que está prestado, en reparación o dado de baja.
   → `app/Rules/EquipoDisponibleParaPrestamo.php`

Además: al prestar, el equipo pasa solo a *prestado*; al devolver **con daño** NO vuelve a
*disponible*, se va a *en reparación*.

---

## 2. Requisitos

| Necesitas | Versión | Cómo lo consigues |
|---|---|---|
| PHP | 8.3 o superior | Viene con Laragon |
| Composer | 2.x | https://getcomposer.org |
| MySQL o MariaDB | cualquiera reciente | Viene con Laragon |
| Un navegador | — | Chrome, Edge o Firefox |

**No necesitas Node.js ni npm.** Este proyecto usa Tailwind CSS desde un CDN, así que no hay
nada que compilar. Lo único que sí necesitas es **conexión a internet** para que el navegador
descargue Tailwind y la tipografía.

Extensiones de PHP que deben estar activas (Laragon las trae encendidas): `pdo_mysql`, `mbstring`,
`openssl`, `tokenizer`, `xml`, `fileinfo`, `curl`, `zip`.

---

## 3. Instalación paso a paso con Laragon

### Paso 1 — Copia el proyecto a la carpeta de Laragon

Descomprime el ZIP dentro de `C:\laragon\www\`. Debe quedar así:

```
C:\laragon\www\laboratorio\
```

### Paso 2 — Enciende Laragon

Abre Laragon y pulsa **Start All**. Deben quedar en verde Apache (o Nginx) y MySQL.

### Paso 3 — Crea la base de datos vacía en phpMyAdmin

El sistema **no** crea la base solo: hay que crearla a mano una vez.

1. En Laragon, pulsa **Menu → MySQL → phpMyAdmin** (o abre `http://localhost/phpmyadmin`).
2. Usuario `root`, contraseña vacía.
3. En la barra de arriba, pestaña **Bases de datos**.
4. En «Crear base de datos» escribe exactamente:

   ```
   laboratorio_cesba
   ```

5. En el desplegable de cotejamiento elige **`utf8mb4_unicode_ci`**
   (así se guardan bien los acentos y la ñ).
6. Pulsa **Crear**. La base aparece vacía en la lista de la izquierda. Es lo correcto: las tablas
   las crea Laravel en el paso 6.

### Paso 4 — Abre una terminal en la carpeta del proyecto

En Laragon: **Menu → Terminal**, y luego:

```bash
cd C:\laragon\www\laboratorio
```

### Paso 5 — Instala las dependencias y prepara el archivo de configuración

```bash
composer install
cp .env.example .env
php artisan key:generate
```

> `composer install` descarga Laravel a la carpeta `vendor/` (tarda un par de minutos la primera vez).
> `copy .env.example .env` crea tu archivo de configuración personal.
> `php artisan key:generate` genera la llave de cifrado de la aplicación.

Abre `.env` y comprueba que diga:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laboratorio_cesba
DB_USERNAME=root
DB_PASSWORD=
```

### Paso 6 — Crea las tablas y siembra los datos de ejemplo

```bash
php artisan migrate --seed
```

Esto crea las cuatro tablas y las llena con 30 equipos, 300 alumnos, los dos usuarios y un
historial de préstamos **que ya incluye cuatro vencidos**, para que el tablero se vea con datos
desde el primer minuto.

### Paso 7 — Levanta el servidor

```bash
php artisan serve
```

Abre **http://127.0.0.1:8000** en el navegador.

> Si prefieres la URL bonita de Laragon (`http://laboratorio.test`), pulsa **Menu → Apache →
> Reload** y Laragon la crea sola apuntando a `laboratorio/public`. En ese caso no necesitas
> `php artisan serve`.

### Si algo sale mal

Vuelve a empezar la base de datos desde cero sin perder nada más:

```bash
php artisan migrate:fresh --seed
```

---

## 4. Credenciales sembradas

Entra en **http://127.0.0.1:8000/entrar**

| Rol | Correo | Contraseña |
|---|---|---|
| Encargado del laboratorio (`admin`) | `encargado@cesba.edu.mx` | `laboratorio123` |
| Auxiliar de mostrador (`auxiliar`) | `auxiliar@cesba.edu.mx` | `laboratorio123` |

### Matrículas para probar la consulta pública (sin login, en **http://127.0.0.1:8000/**)

| Matrícula | Qué verás |
|---|---|
| `SC202402` | Un préstamo **vencido** con 11 días de atraso |
| `SC202401` | Equipo prestado, dentro del plazo |
| `SC202407` | No debe nada |
| `ZZ999999` | Matrícula que no existe (para ver el mensaje de error) |

### Para probar la regla del semestre en clase

Intenta prestar el **osciloscopio `LAB-3007`** (requiere 6.º semestre) a **`SC202407`**
(Valeria, 1.er semestre). El sistema lo rechaza con un mensaje que explica exactamente por qué.

---

## 5. Estructura de carpetas, explicada desde cero

Si nunca has visto un proyecto de Laravel, esto es lo que hay y para qué sirve.
**Las cuatro carpetas que de verdad vas a tocar están marcadas con ★.**

```
laboratorio/
├── app/                        ★ Todo el código PHP que escribimos nosotros
│   ├── Enums/                    Listas cerradas de valores (estados, roles, condiciones)
│   ├── Exceptions/               Errores propios del negocio
│   ├── Http/
│   │   ├── Controllers/          Reciben la petición y deciden qué vista mostrar
│   │   ├── Middleware/           Filtros que la petición cruza antes del controlador
│   │   └── Requests/             Validación de TODO lo que manda el usuario
│   ├── Models/                   Una clase por tabla: Equipo, Alumno, Prestamo, Usuario
│   ├── Policies/                 Quién puede hacer qué con cada modelo
│   ├── Providers/                Configuración que se aplica al arrancar la aplicación
│   ├── Rules/                    Las tres reglas de negocio del laboratorio
│   └── Services/                 Operaciones de varios pasos (prestar, devolver, tablero)
│
├── bootstrap/                    Arranque del framework. Aquí se registran los middlewares.
│   └── app.php
│
├── config/                       Configuración (base de datos, sesión, autenticación…)
│
├── database/                   ★ Todo lo relativo a la base de datos
│   ├── factories/                Generadores de datos falsos para las pruebas
│   ├── migrations/               La forma de las tablas, versionada paso a paso
│   └── seeders/                  Datos iniciales realistas
│
├── lang/                       ★ Traducciones
│   ├── es.json                   Frases sueltas (paginación)
│   └── es/                       validation.php, pagination.php, auth.php
│
├── public/                       La ÚNICA carpeta que ve internet. Aquí vive index.php.
│
├── resources/views/            ★ Las pantallas (archivos .blade.php)
│   ├── components/               Piezas reutilizables: botón, tarjeta, campo, insignia…
│   │   └── layouts/              Los dos esqueletos: app (privado) y publico
│   ├── alumnos/ equipos/ prestamos/ devoluciones/ vencidos/ consulta/ sesion/ errors/
│   └── tablero.blade.php
│
├── routes/web.php              ★ El mapa: qué URL va a qué controlador
│
├── storage/                      Archivos que genera el sistema: caché, logs, vistas compiladas
│   └── logs/laravel.log          ← AQUÍ se ven los errores cuando algo falla
│
├── tests/                      ★ Las pruebas automáticas
│   ├── Feature/                  Prueban pantallas completas, con petición HTTP
│   └── Unit/                     Prueban una clase aislada
│
├── vendor/                       Laravel y sus dependencias. NO se toca, NO va al repositorio.
├── .env                          TU configuración (contraseñas). NO va al repositorio.
├── .env.example                  Plantilla de .env. Esta sí va al repositorio.
└── composer.json                 Lista de dependencias del proyecto
```

### Las tres reglas de oro de la estructura

1. **Nada de lógica en las vistas.** Una vista `.blade.php` solo acomoda datos que ya le llegaron.
   Si ves una consulta a la base de datos dentro de un `.blade.php`, está mal.
2. **Controladores delgados.** Un controlador recibe, pide y responde. Si un método pasa de
   15 líneas, probablemente algo debería estar en un *Service* o en el *Model*.
3. **Toda entrada se valida en un Form Request.** Sin excepciones, ni siquiera en rutas públicas.

---

## 6. El recorrido de una petición, de principio a fin

Seguimos lo que pasa cuando el auxiliar pulsa **«Entregar equipo»**.

```
NAVEGADOR
   │  POST /prestamos
   │  alumno_id=12  equipo_id=7  fecha_compromiso=2026-10-14  _token=…
   ▼
public/index.php ─────────── el único archivo que recibe TODAS las peticiones
   ▼
bootstrap/app.php ────────── arranca Laravel y registra los middlewares
   ▼
MIDDLEWARES
   ├─ VerifyCsrfToken ─────── ¿viene el _token de esta sesión? Si no → 419
   ├─ StartSession ────────── carga la sesión del usuario
   └─ auth ────────────────── ¿hay alguien dentro? Si no → redirige a /entrar
   ▼
routes/web.php ───────────── busca la ruta: POST /prestamos → PrestamoController@store
   ▼
RegistrarPrestamoRequest ─── ¡AQUÍ ESTÁ LA PARTE IMPORTANTE!
   │   1. authorize()  → consulta PrestamoPolicy::create(). ¿Este rol puede prestar?
   │   2. rules()      → valida campo por campo:
   │        · alumno_id  existe + AlumnoSinAdeudoVencido      (regla 2)
   │        · equipo_id  existe + EquipoDisponibleParaPrestamo (regla 3)
   │                             + SemestreSuficienteParaEquipo (regla 1)
   │        · fecha_compromiso  entre hoy y dentro de un mes
   │
   │   Si algo falla → el controlador NUNCA se ejecuta.
   │   Laravel regresa a /prestamos/create con los errores y con lo ya escrito.
   ▼
PrestamoController@store ─── solo coordina (8 líneas):
   │   busca el Equipo y el Alumno, y llama al servicio
   ▼
ServicioDePrestamos::prestar()
   │   DB::transaction( ── o pasan las dos cosas, o no pasa ninguna
   │       lockForUpdate()  ← bloquea la fila del equipo contra dos auxiliares a la vez
   │       verificarReglas() ← segunda línea de defensa, por si se llama desde otro lado
   │       crea el Prestamo
   │       cambia el Equipo a estado "prestado"
   │   )
   ▼
to_route('prestamos.show', $prestamo)->with('exito', '…')
   │   redirige con un mensaje guardado en la sesión
   ▼
GET /prestamos/{prestamo} ── Laravel ve {prestamo} en la URL, busca el registro
   │                         y lo inyecta ya cargado (route model binding).
   │                         Si no existe → 404 automático.
   ▼
resources/views/prestamos/show.blade.php
   │   <x-layouts.app> dibuja el esqueleto
   │   <x-alertas>     muestra el mensaje verde de éxito
   │   el resto acomoda los datos que le pasó el controlador
   ▼
HTML → NAVEGADOR
```

**Lo que hay que llevarse de este recorrido:** la petición pasa por **cuatro filtros**
(middleware → authorize → rules → servicio) antes de que algo se escriba en la base de datos.
Cada filtro tiene un trabajo y solo uno.

---

## 7. Rutas principales

| Método | URL | Nombre | Quién entra |
|---|---|---|---|
| GET | `/` | `consulta.index` | **Público** — el alumno escribe su matrícula |
| POST | `/consulta` | `consulta.buscar` | **Público** — resultado de la consulta |
| GET / POST | `/entrar` | `sesion.create` / `sesion.store` | Público (invitados) |
| POST | `/salir` | `sesion.destroy` | Autenticado |
| GET | `/tablero` | `tablero` | Los dos roles |
| GET | `/vencidos` | `vencidos.index` | Los dos roles |
| GET | `/prestamos` | `prestamos.index` | Los dos roles |
| GET / POST | `/prestamos/create`, `/prestamos` | `prestamos.create` / `.store` | Los dos roles |
| GET | `/prestamos/{prestamo}` | `prestamos.show` | Los dos roles |
| GET / POST | `/prestamos/{prestamo}/devolver` | `devoluciones.create` / `.store` | Los dos roles |
| GET | `/equipos`, `/equipos/{equipo}` | `equipos.index` / `.show` | Los dos roles |
| GET POST PUT DELETE | resto de `/equipos` | `equipos.create` … `.destroy` | **Solo admin** |
| GET | `/alumnos`, `/alumnos/{alumno}` | `alumnos.index` / `.show` | Los dos roles |
| GET POST PUT DELETE | resto de `/alumnos` | `alumnos.create` … `.destroy` | **Solo admin** |

Para verlas todas:

```bash
php artisan route:list --except-vendor
```

---

## 8. Las pruebas automáticas

```bash
php artisan test
```

**66 pruebas, todas en verde.** Corren en una base SQLite en memoria, así que **no tocan tu base
de MySQL**: puedes ejecutarlas cuantas veces quieras sin miedo.

| Archivo | Qué cubre |
|---|---|
| `tests/Feature/AccesoTest.php` | Login, logout y que las rutas privadas redirijan a `/entrar` |
| `tests/Feature/ConsultaPublicaTest.php` | La consulta del alumno, incluida la privacidad |
| `tests/Feature/CatalogoDeEquipoTest.php` | Permisos de admin vs. auxiliar, unicidad, filtros |
| `tests/Feature/PrestamoTest.php` | **Las tres reglas de negocio**, cada una con su caso que pasa y su caso que falla |
| `tests/Feature/DevolucionTest.php` | Devolución bien / con daño y el estado resultante |
| `tests/Feature/TableroTest.php` | Conteos, ranking y listado de vencidos |
| `tests/Unit/ReglasDelLaboratorioTest.php` | Enums, scopes, atributos calculados y la regla de semestre aislada |

Para correr un solo archivo mientras trabajas:

```bash
php artisan test --filter=PrestamoTest
```

---

## 9. Problemas frecuentes

| Lo que ves | Por qué pasa | Cómo se arregla |
|---|---|---|
| `SQLSTATE[HY000] [1049] Unknown database 'laboratorio_cesba'` | No creaste la base en phpMyAdmin | Vuelve al **paso 3** de la instalación |
| `SQLSTATE[HY000] [2002] No connection could be made` | MySQL no está encendido | En Laragon pulsa **Start All** y revisa que MySQL esté en verde |
| `No application encryption key has been specified` | Falta la llave | `php artisan key:generate` |
| `Class "App\Models\Equipo" not found` | El autoload está desfasado | `composer dump-autoload` |
| La página se ve **sin estilos**, todo texto negro amontonado | Tailwind viene de un CDN y no hay internet | Conéctate a internet y recarga con **Ctrl+F5** |
| Página de error **419 · La página estuvo demasiado tiempo abierta** | El token CSRF caducó | Recarga la página y vuelve a enviar el formulario |
| Página de error **403 · No tienes acceso a esta sección** | Entraste como auxiliar a una pantalla de admin | Sal y entra con `encargado@cesba.edu.mx` |
| Cambias un archivo `.blade.php` y no se ve el cambio | Quedó una vista compilada vieja | `php artisan view:clear` |
| Cambias el `.env` y Laravel sigue con lo anterior | La configuración está en caché | `php artisan config:clear` |
| `Attempted to lazy load [equipo] on model [Prestamo]` | El controlador no precargó una relación que la vista usa | Agrega `->with('equipo')` o `$modelo->load('equipo')` en el controlador. **Es a propósito:** así se detecta el problema N+1 |
| `The POST method is not supported for route` | Un formulario sin `@method('PUT')` o `@method('DELETE')` | Agrégalo debajo de `@csrf` |
| Las migraciones fallan a la mitad y la base quedó rara | Pasa mientras se experimenta | `php artisan migrate:fresh --seed` |
| Quieres ver el error completo | El detalle está en el log | Abre `storage/logs/laravel.log` y mira el final del archivo |

---

## 10. Ocho ejercicios para el alumno

Van de menor a mayor. Después de cada uno, corre `php artisan test` para comprobar que no
rompiste nada, y **agrega tu propia prueba**.

### Ejercicio 1 — Un campo más en el equipo *(fácil)*

Agrega la columna `ubicacion` al equipo (por ejemplo «Estante B, repisa 2»).

Tendrás que tocar: una **migración nueva**, el `#[Fillable]` del modelo, el **Form Request**
(reglas, `messages()` y `attributes()`), la parcial `equipos/_formulario.blade.php` y la vista
`equipos/show.blade.php`.

> Lo que se aprende: cuántos archivos toca un campo, y por qué conviene que cada uno tenga un
> solo trabajo.

### Ejercicio 2 — Filtrar alumnos por carrera *(fácil)*

En `/alumnos`, agrega un `<select>` de carrera junto al buscador.

Pistas: un *query scope* nuevo en `App\Models\Alumno` (fíjate en `buscar()`), un `->when()` en
`AlumnoController@index` y recordar `->withQueryString()` para que el filtro sobreviva al cambiar
de página.

### Ejercicio 3 — Ordenar el catálogo por columna *(medio)*

Haz que los encabezados «Inventario», «Equipo» y «Estado» de `/equipos` sean enlaces que ordenen
ascendente y descendente.

Pista: la URL llevaría `?orden=tipo&dir=desc`. **Nunca** pases directo a `orderBy()` lo que venga
de la URL: valida contra una lista blanca de columnas permitidas, o abres un hueco de seguridad.

### Ejercicio 4 — Renovar un préstamo *(medio)*

Agrega un botón «Renovar 3 días más» en el detalle de un préstamo abierto.

Reglas que tú defines y que deben quedar en el código, no en tu cabeza: solo se renueva un
préstamo **no vencido**, y solo **una vez** (necesitarás una columna `renovado_en`). Hazlo con un
controlador propio, su Form Request y su entrada en la Policy.

### Ejercicio 5 — Exportar los vencidos a CSV *(medio)*

Un botón en `/vencidos` que descargue un `.csv` con matrícula, nombre, inventario, fecha
comprometida y días de atraso.

Pistas: `response()->streamDownload()`, y `->chunk(200)` para no cargar miles de filas en memoria
de un jalón. Acuérdate del BOM UTF-8 (`\xEF\xBB\xBF`) al principio, o Excel se come los acentos.

### Ejercicio 6 — Avisar por correo al alumno *(medio-difícil)*

Cuando un préstamo cumpla su fecha, manda un correo al alumno.

Pistas: un *Mailable*, un comando de consola con `php artisan make:command` y programarlo en
`routes/console.php` con `Schedule::command(...)->dailyAt('08:00')`. En desarrollo usa
`MAIL_MAILER=log` y lee los correos en `storage/logs/laravel.log`.

### Ejercicio 7 — Bitácora de cambios del equipo *(difícil)*

Registra cada cambio de estado de un equipo (quién, cuándo, de qué estado a cuál).

Pistas: una tabla `movimientos_equipo`, un **Observer** de Eloquent
(`php artisan make:observer`) escuchando el evento `updated`, y `$equipo->isDirty('estado')` para
reaccionar solo cuando el estado de verdad cambió. Muéstrala en la ficha del equipo.

### Ejercicio 8 — Límite de equipo simultáneo por alumno *(difícil)*

Cuarta regla de negocio: un alumno no puede tener más de **dos** equipos al mismo tiempo, y el
límite debe ser configurable (no un número escrito a la mitad del código).

Pistas: un archivo `config/laboratorio.php` con `'maximo_simultaneo' => env('LAB_MAXIMO', 2)`,
una regla nueva en `app/Rules/`, enchufarla en `RegistrarPrestamoRequest` **y también** en
`ServicioDePrestamos::verificarReglas()`. Escribe las pruebas **antes** del código: una para
el alumno con un equipo (pasa), una con dos (falla).

---

## 11. Decisiones de diseño que vale la pena comentar en clase

- **Tabla `usuarios` en español.** Laravel trae `users`. Cambiarla obliga a decirle a Laravel dos
  cosas: `#[Table(name: 'usuarios')]` y `getAuthPassword()` en `App\Models\Usuario`, más el modelo
  en `config/auth.php`. Es un buen ejemplo de que las convenciones del framework se pueden cambiar
  si sabes qué estás tocando.
- **Enums con comportamiento.** `CondicionDevolucion::estadoResultante()` guarda la regla «con daño
  → reparación» dentro del propio enum, no repartida en el controlador y la vista.
- **`Model::preventLazyLoading()`** en `AppServiceProvider`: la aplicación **falla a propósito** si
  una vista accede a una relación que el controlador no precargó. Es la forma más rápida de
  aprender a detectar el problema N+1.
- **Policy y middleware juntos.** El middleware `rol:admin` cierra el grupo de rutas; la Policy
  decide caso por caso (`no borres un equipo que está prestado`). Ocultar el botón con `@can` es
  cortesía con el usuario, nunca seguridad.
- **Tailwind por CDN.** Perfecto para el salón; en producción se compila con Vite para que el CSS
  pese poco y el sitio no dependa de internet.

---

## 12. Comandos de uso diario

```bash
php artisan serve                 # levanta el servidor
php artisan test                  # corre las 66 pruebas
php artisan migrate --seed        # crea tablas y siembra datos
php artisan migrate:fresh --seed  # borra TODO y vuelve a empezar
php artisan route:list --except-vendor   # el mapa de rutas
php artisan tinker                # consola interactiva para probar el código
php artisan view:clear            # limpia las vistas compiladas
php artisan config:clear          # limpia la configuración en caché
./vendor/bin/pint                 # da formato al código según el estándar de Laravel
```
