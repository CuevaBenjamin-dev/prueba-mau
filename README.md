# Sistema de Gestión de Personajes Star Wars

Aplicación web local para registrar, consultar, actualizar, eliminar e importar personajes desde [SWAPI](https://swapi.py4e.com/documentation). Fue creada con `laravel new` sin starter kit. PER (gestión) y SWA (importación) son áreas funcionales de una sola aplicación Laravel.

## Información del Candidato

- Nombre: **[Completar nombre completo]**
- Email: **[Completar email]**
- Teléfono: **[Completar teléfono]**
- Usuario de GitHub: **CuevaBenjamin-dev**

## Requisitos del Sistema

- PHP 8.3 con extensiones `curl`, `fileinfo`, `mbstring`, `openssl`, `pdo_mysql` y `zip` (el PDF exige PHP 8.1+).
- Composer 2, Node.js 22.12+ o 24+, npm y MySQL 8+.
- Un puerto local libre para servir la aplicación.

Versiones verificadas en el entorno de desarrollo: PHP **8.3.36**, Composer **2.8.10**, Node.js **24.18.0**, MySQL **8.0.38**, Laravel **12.72.0**, Vue **3.5.45**, `inertiajs/inertia-laravel` **3.7.0**, `@inertiajs/vue3` **3.11.0**, Tailwind CSS **4.3.5** y Vite **8.3.8**. Las versiones exactas de dependencias están en `composer.lock` y `package-lock.json`. `composer.json` fija la plataforma de resolución en PHP 8.3.36 para evitar dependencias que exijan PHP 8.4.

## Instalación

1. Clonar el repositorio:

   ```bash
   git clone https://github.com/CuevaBenjamin-dev/prueba-mau.git
   cd star-wars-personajes
   ```

   En la entrega local actual, entrar directamente en la carpeta `star-wars-personajes`.

2. Instalar dependencias:

   ```bash
   composer install
   npm ci
   ```

3. Crear la base de datos en MySQL 8 (ajustar usuario, contraseña, host y puerto a la instalación local):

   ```sql
   CREATE DATABASE star_wars_personajes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE DATABASE star_wars_personajes_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

4. Copiar `.env.example` a `.env`, editar `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` y generar la clave:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   En PowerShell se puede usar `Copy-Item .env.example .env`. No subir `.env` al repositorio. La instancia MySQL aislada usada para la validación local de este workspace escucha en **127.0.0.1:3307**; `.env.example` usa el puerto estándar **3306**.

5. Crear las tablas, compilar y arrancar:

   ```bash
   php artisan migrate
   npm run build
   php artisan serve
   ```

   Abrir `http://localhost:8000/personajes`. Si `artisan serve` no puede enlazar el puerto en Windows, se puede ejecutar `php -S 127.0.0.1:50123 -t public` y abrir `http://127.0.0.1:50123/personajes`. Para desarrollo de assets, `npm run dev` es opcional; `npm run build` ya produce los archivos necesarios.

6. Ejecutar pruebas automatizadas contra la base **separada** `star_wars_personajes_test`:

   ```bash
   php artisan test
   php artisan route:list --path=personajes
   ```

   `phpunit.xml` selecciona MySQL y la base de pruebas. Su host, puerto, usuario y contraseña se toman del entorno local. Nunca apuntar `DB_DATABASE` y la base de pruebas al mismo nombre: las pruebas usan `RefreshDatabase`.

## Funcionalidades Implementadas

- Tabla `personajes` con 12 columnas, `swapi_id` nullable y UNIQUE, y `origen_swapi` con valor predeterminado falso.
- Las seis rutas CRUD pedidas por MAU; formularios de creación y edición con validación de servidor y cliente; origen e ID SWAPI preservados al editar; confirmación visible para eliminar.
- Listado con diez personajes por página, origen visible, estado vacío y diseño adaptable a móvil.
- Modal para POST `/personajes/importar` con ID entero de 1 a 83, espera visible y bloqueo de envíos repetidos.
- Importación desde `https://swapi.py4e.com/api/people/{id}/`: mapeo de nombre, altura, masa, colores y género; `unknown` de género se guarda como `n/a`; medidas inválidas o fuera de rango quedan en `null`; `planeta_origen` queda en `null` sin consultar `homeworld`.
- Duplicados bloqueados antes de la solicitud externa y mediante índice UNIQUE en MySQL. 404, 5xx, timeout, fallo de conexión y respuestas incompatibles devuelven mensajes controlados sin crear registros.
- Componentes Vue reutilizables: `AppLayout`, `PersonajeForm`, `PersonajeTable` e `ImportModal`.

No se implementaron funcionalidades bonus. No hay autenticación, API REST separada ni despliegue. Esta aplicación se ejecuta localmente con PHP y MySQL; no tiene Dockerfile ni archivo Compose y no inicia contenedores propios.

## Tecnologías

Laravel 12 y PHP 8.3 en el servidor; MySQL 8 con Eloquent; Vue 3 con Composition API y `<script setup>`; Inertia.js y `useForm()`; Tailwind CSS 4; Vite 8. La comunicación de formularios usa rutas web Laravel, sesión, CSRF y redirecciones Inertia.

## Notas

- El CRUD local no depende de la disponibilidad de SWAPI. El cliente HTTP usa `connectTimeout(3)` y `timeout(7)` segundos, sin solicitudes a dominios aportados por el navegador.
- Los tests de SWAPI usan `Http::fake()` y `Http::preventStrayRequests()`; no consumen la API real. Durante la revisión manual en navegador, SWAPI no estuvo disponible y el modal mostró el mensaje controlado; la creación y edición locales siguieron funcionando.
- El texto `unknown` y `n/a` no son equivalentes en significado. El esquema local admite solo `masculino`, `femenino` y `n/a`; por decisión funcional aprobada, ambos valores externos se representan como `n/a`.
- La advertencia de trazabilidad `RF-PER-015: sin caso de uso` en MODULA no elimina las seis rutas CRUD. El proyecto funcional vigente es la revisión 97, con 35 requisitos y 5 casos de uso aprobados.
- Antes de entregar el enlace a MAU, completar los datos del candidato, revisar la declaración de IA y los marcadores de experiencia personal, comprobar que `.env` no se incluya y repetir la instalación y las pruebas desde un clon limpio. El envío por email queda pendiente de autorización.

## Decisiones Técnicas

1. **Organización del código backend.** `PersonajeController` concentra el CRUD y delega validaciones a `GuardarPersonajeRequest`. `ImportacionSwapiController` orquesta la importación, `ImportarPersonajeRequest` valida el ID y `SwapiService` maneja HTTP, transformación y validación de datos externos. Eloquent representa la tabla. No se agregó Repository Pattern para una sola entidad.
2. **Componentes Vue.** `PersonajeForm` sirve para crear y editar, `PersonajeTable` muestra acciones y datos en escritorio/móvil, `ImportModal` contiene el flujo SWAPI y `AppLayout` unifica la estructura visual. Esto evita duplicar formularios y presentación.
3. **Manejo de errores de SWAPI.** El ID se valida antes del HTTP; el dominio de la URL es fijo. Hay tiempos máximos de conexión/respuesta, distinción de 404 y fallos temporales, verificación de JSON y de los campos antes de escribir. Un duplicado no genera nueva consulta; el índice UNIQUE cubre importaciones concurrentes. Los fallos vuelven como mensajes de validación en el modal.
4. **Validaciones.** Los Form Requests validan entrada manual e ID en el servidor. El servicio valida el dato externo transformado antes de persistir. HTML añade comprobaciones básicas de campos obligatorios y límites para respuesta inmediata, pero no sustituye la validación del backend.
5. **Mayor dificultad.** **[Completar personalmente: describir la dificultad real experimentada y cómo se resolvió; no atribuir al candidato una experiencia no confirmada.]**
6. **Uso de Inteligencia Artificial.** Se usó **OpenAI Codex** para crear la estructura, código Laravel/Vue, pruebas y esta documentación a partir del PDF, la especificación MODULA y el diseño técnico aportados. Se validó el resultado con migración MySQL, `php artisan route:list`, pruebas Feature con `Http::fake()`, `npm run build` y revisión manual del navegador. **[El candidato debe revisar y ajustar esta declaración según su propio trabajo y verificar que puede explicar y modificar el código.]**
7. **Mejoras futuras.** Después de cumplir el MVP, podrían añadirse búsqueda, filtros y pruebas de accesibilidad más amplias. No forman parte de la entrega actual.
8. **Autoevaluación.** **[Completar personalmente una nota del 1 al 10 y justificar el aspecto más fuerte tras la revisión final.]**
9. **Tiempo invertido.** **[Completar personalmente las horas reales de trabajo.]**

## Verificación realizada

En el entorno local descrito: migración MySQL ejecutada, `php artisan route:list --path=personajes` mostró las seis rutas CRUD y la ruta de importación, `php artisan test --compact` terminó con **15 pruebas aprobadas y 148 aserciones**, y `npm run build` compiló los assets. En navegador se comprobó creación, edición, confirmación de borrado, modal, mensaje controlado ante fallo real de SWAPI y presentación móvil/escritorio. La disponibilidad futura de SWAPI y el comportamiento en otros equipos requieren comprobación en cada entorno.
