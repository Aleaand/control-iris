# Iris Aerospace - ERP de Gestión (Control Interno)

Repositorio correspondiente a **`control-iris`**, el motor administrativo y ERP (Enterprise Resource Planning) del ecosistema de Iris Aerospace.

## Descripción del Proyecto

`control-iris` es el cerebro operativo de la compañía. Se trata de un panel de control privado y robusto al que solo pueden acceder los empleados de la organización bajo un sistema de roles (Administrador y Gestor). 

Sus funcionalidades principales incluyen:
- **Gestión Logística:** Creación y mantenimiento del catálogo de Planetas, Naves Espaciales y Vuelos. Cuenta con protección contra borrado accidental (integridad referencial) y prevención de creación de vuelos con pérdidas económicas.
- **Finanzas y Auditoría:** Panel de ingresos netos, seguimiento de la depreciación de las naves y auditoría inmutable de tarifas (`price_logs`).
- **Sistema de Reembolsos:** Integración directa con Stripe para ejecutar devoluciones matemáticas parciales o totales de forma automatizada hacia la tarjeta del cliente.
- **CRM y Tareas Automáticas (Observers):** El sistema detecta eventos críticos (como la cancelación de un vuelo) y genera notificaciones/tareas urgentes en la bandeja de entrada del Gestor asignado.
- **Compliance y Emisión de Tickets:** Verificación de certificados legales (pasaportes) de los pasajeros y generación del *Ticket PDF Final* utilizando `dompdf`.

## Stack Tecnológico

El proyecto está desarrollado sobre un monolito estructurado y seguro, con reactividad moderna inyectada directamente desde el servidor:

- **Framework Backend:** [Laravel 12](https://laravel.com/) (PHP 8.2+)
- **Reactividad Frontend:** [Livewire 3](https://livewire.laravel.com/) (Componentes dinámicos sin necesidad de recargar la página, emulando una SPA).
- **Estilos:** [Tailwind CSS v4](https://tailwindcss.com/)
- **Base de Datos:** PostgreSQL
- **PDF y Facturación:** `barryvdh/laravel-dompdf`
- **Testing:** [Pest](https://pestphp.com/) / PHPUnit (Más de 130 aserciones para validar la precisión del motor de reembolsos).

## Estructura del Directorio

```text
control-iris/
├── app/                  # Lógica central: Modelos (Eloquent), Observers, Livewire Components
├── config/               # Configuraciones del framework y servicios externos
├── database/             # Migraciones (esquemas de BBDD) y Seeders
├── resources/            # Vistas (Blade Templates), estilos (CSS) y scripts
├── routes/               # Rutas protegidas (`web.php` con middlewares de roles)
├── tests/                # Pruebas automatizadas del Refund Engine (Pest)
└── composer.json         # Dependencias del ecosistema PHP
```

## Instalación y Configuración

Sigue estos pasos para levantar el ERP en tu entorno local:

### 1. Clonar el repositorio y acceder a la carpeta
```bash
git clone <url>
cd control-iris
```

### 2. Instalar las dependencias de PHP y Node
```bash
composer install
npm install
npm run build
```

### 3. Variables de Entorno
Copia el archivo de ejemplo para crear tu entorno local:
```bash
cp .env.example .env
php artisan key:generate
```
Configura la conexión a tu base de datos PostgreSQL (la misma que usan `iris-web` e `iris-api`) y las credenciales de Stripe en el `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=ep-nombre-servidor.neon.tech
DB_PORT=5432
DB_DATABASE=iris_db
DB_USERNAME=usuario_neon
DB_PASSWORD=tu_contraseña

STRIPE_SECRET=sk_test_...
```

### 4. Levantar el servidor
```bash
php artisan serve
```
El panel de control estará accesible en [http://localhost:8000](http://localhost:8000).

## Pruebas

El ERP cuenta con una suite de auditoría transaccional para garantizar que los márgenes de beneficio corporativo estén protegidos ante reembolsos complejos. 

Para ejecutar las pruebas lógicas:
```bash
php artisan test
```

## Enlaces Relacionados (Ecosistema Iris)

El monolito es el gestor central y comparte la misma base de datos relacional con:
1. **Frontend Público (`iris-web`)**: Portal del cliente donde ocurren las compras.
2. **API Bridge (`iris-api`)**: Servidor encargado de la validación inicial y enlace seguro de los pagos hacia la BBDD.

## Licencia / Autoría
Desarrollado por Alejandra para su proyecto de final. Todos los derechos reservados al contexto académico del proyecto.
