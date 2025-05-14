
# Dbalance_API

Este proyecto se centra en la creación de una API diseñada para gestionar registros de gastos e Ingresos, la API ofrece un conjunto de endpoints que posibilitan diversas operaciones entre las que se incluyen la obtención de un listado completo de gastos e Ingresos, la recuperación de información detallada de un gasto e ingreso específico, la creación de nuevos registros de gastos e ingresos, la actualización de información existente sobre un gasto e ingreso y la eliminación de registros de gastos e ingresos previamente almacenados.

De igual forma, tambien se agrego un Auth para autenticar a los usuarios que van a hacer su registro y posterior login con sus respectivas validaciones, en el login, se agregó unas opciones para inciar sesión con google (Aun no implementado) y una funcion para que el usuario pueda recuperar su contraseña con una validación de su correo electronico que haya registrado.

## Pre-requisitos 📋

Para la correcta ejecución de este proyecto, necesitas tener las siguientes tecnologías instaladas en tu ordenador.
* PHP ^8.1
* Composer 2
* MySQL o PostgreSQL

## Instalación 🔧

1. Clona este proyecto.
```bash
git clone https://github.com/Pipebc20/Dbalance_API.git
```

2. Instala las dependencias de PHP con composer.
```bash
composer install
```

3. Crea una nueva base de datos con tu gestor de base de datos preferido. Como sugerencia podrías crear una base de datos llamada `Dbalance_API`.

4. Crea una copia del archivo env.example, renombralo como .env y configura las variables de entorno correspondientes, preferiblemente las variables para la conexión a la base de datos.
```json
APP_NAME=Dbalance_API
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql o postgresql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tu_base_de_datos
DB_USERNAME=tu_username
DB_PASSWORD=tu_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

5. Genera una APP_KEY.
```bash
php artisan key:generate
```

6. Ejecuta las migraciones y los seeders.
```bash
php artisan migrate:fresh --seed
```

7. Ejecuta el proyecto laravel.
```bash
php artisan serve
```

## Construido con 🛠️

- [PHP 8.1](https://www.php.net/releases/8.1/es.php)
- [Laravel 10](https://laravel.com/docs/10.x)
- [Composer 2.6.5](https://getcomposer.org/)
- [MySQL 8.2.0](https://dev.mysql.com/downloads/mysql/)
- [PostgreSQL 10](https://www.postgresql.org/)


## Endpoints 🔗

A continuación, se detallan los endpoints disponibles en la API:

| Método  | Ruta                        | Descripción                                                                                    |
|---------|-----------------------------|------------------------------------------------------------------------------------------------|
| POST    | /api/register               | Registra un nuevo usuario con los datos enviados en la solicitud.                              |
| POST    | /api/login                  | Autentica al usuario y devuelve un token de acceso.                                            |
| POST    | /api/password/email         | Envía un correo con el enlace para restablecer la contraseña del usuario.                      |
| GET     | /api/ingresos               | Devuelve un array de ingresos.                                                                 |
| POST    | /api/ingresos               | Crea un ingreso utilizando la información enviada dentro del `body` de la solicitud.           |
| GET     | /api/ingresos/`{income}`    | Devuelve el objeto de ingreso con el `id` especificado.                                        |
| PUT     | /api/ingresos/`{income}`    | Actualiza el ingreso con el `id` especificado utilizando los datos del `body` de la solicitud. |
| DELETE  | /api/ingresos/`{income}`    | Elimina el ingreso con el `id` especificado.                                                   |
| GET     | /api/gastos                 | Devuelve un array de gastos.                                                                   |
| POST    | /api/gastos                 | Crea un gasto utilizando la información enviada dentro del `body` de la solicitud.             |
| GET     | /api/gastos/`{expense}`     | Devuelve el objeto de gasto con el `id` especificado.                                          |
| PUT     | /api/gastos/`{expense}`     | Actualiza el gasto con el `id` especificado utilizando los datos del `body` de la solicitud.   |
| DELETE  | /api/gastos/`{expense}`     | Elimina el gasto con el `id` especificado.                                                     |

Nota: La API de DBalance está organizada en rutas públicas y rutas protegidas, con el objetivo de asegurar correctamente las operaciones 
según el contexto del usuario.

🔓 Rutas Públicas (sin autenticación)
Estas rutas son accesibles sin necesidad de un token. Están destinadas a funciones básicas como el registro, el inicio de sesión y el restablecimiento de contraseña, permitiendo al usuario autenticarse y acceder al sistema:

POST /api/register → Permite registrar un nuevo usuario.

POST /api/login → Autentica al usuario y devuelve un token.

POST /api/password/email → Envía un correo para restablecer la contraseña.

Estas rutas no requieren autenticación, por lo que están disponibles para cualquier usuario desde el exterior del sistema.

🔐 Rutas Protegidas (requieren autenticación con Sanctum)
Una vez autenticado, el usuario puede acceder a todas las funcionalidades principales de la aplicación mediante rutas protegidas que sí requieren un token válido. 
Estas rutas están agrupadas con el middleware auth:sanctum para garantizar seguridad:

📊 Ingresos: CRUD completo para registrar, visualizar, modificar y eliminar ingresos.

💸 Gastos: CRUD completo para registrar, visualizar, modificar y eliminar gastos.

👤 Usuario y sesión:

GET /api/user → Devuelve los datos del usuario autenticado.

POST /api/logout → Cierra la sesión y revoca el token actual.

Este enfoque asegura que solo los usuarios autenticados puedan interactuar con datos sensibles o personales, mientras que se mantiene la facilidad de acceso para operaciones iniciales como el registro y la autenticación.


## Autor 🖋️
- [@Pipebc20](https://github.com/Pipebc20)
