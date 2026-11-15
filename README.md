# Sistema de Gestión de Personajes Star Wars

Aplicación web local para registrar, consultar, actualizar, eliminar e importar personajes desde [SWAPI](https://swapi.py4e.com/documentation). Fue creada con `laravel new` sin starter kit. PER (gestión) y SWA (importación) son áreas funcionales de una sola aplicación Laravel.

## Información del Candidato

- Nombre: **Benjamín de Jesús Cueva Niño de Guzmán**
- Email: **cueva_dev@hotmail.com**
- Teléfono: **+51 923 880 101**
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
   cd prueba-mau
   ```

   En la entrega local actual, entrar directamente en la carpeta `star-wars-personajes`.

   **Windows con PHP 8.3 instalado por WinGet o ZIP:** preparar certificados y extensiones antes de usar Composer o iniciar Laravel. Sustituir la ruta por la de `php.exe` en el equipo:

   ```powershell
   $php83 = 'C:\ruta\a\PHP-8.3\php.exe'
   .\scripts\configurar-php-local.ps1 -PhpExecutable $php83
   $env:PHPRC = (Resolve-Path .local\php.ini).Path
   $env:PATH = "$(Split-Path $php83);$env:PATH"
   ```

   El script descarga por HTTPS el paquete CA publicado por curl y configura `curl.cainfo` y `openssl.cafile` en `.local/php.ini`. No desactiva la verificación TLS. `.local` está excluida de Git.

   Comprobar la conexión HTTPS real, sin guardar datos, antes de probar el modal:

   ```powershell
   & $php83 -c .local\php.ini artisan tinker --execute='echo app(App\Services\SwapiService::class)->obtenerPersonaje(1)["nombre"];'
   ```

   El resultado esperado, si SWAPI está disponible, es `Luke Skywalker`. Esta comprobación detecta problemas del almacén CA que `Http::fake()` no puede detectar. Referencias: [CA Extract de curl](https://curl.se/docs/caextract.html), [configuración cURL de PHP](https://www.php.net/manual/en/curl.configuration.php) y [configuración OpenSSL de PHP](https://www.php.net/manual/en/openssl.configuration.php).

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
- Los tests de SWAPI usan `Http::fake()` y `Http::preventStrayRequests()`; no consumen la API real. Por eso se requiere además una comprobación HTTPS real después de configurar PHP. La primera revisión manual mostró «SWAPI no está disponible» debido a un `cURL error 60` local: faltaba el paquete CA en PHP 8.3. Windows `curl.exe` respondió HTTP 200; al configurar `curl.cainfo` y `openssl.cafile`, Laravel recibió HTTP 200 y transformó a Luke Skywalker correctamente. El error no era un ID inválido ni una caída de SWAPI.
- El texto `unknown` y `n/a` no son equivalentes en significado. El esquema local admite solo `masculino`, `femenino` y `n/a`; por decisión funcional aprobada, ambos valores externos se representan como `n/a`.
- La advertencia de trazabilidad `RF-PER-015: sin caso de uso` en MODULA no elimina las seis rutas CRUD. El proyecto funcional vigente es la revisión 97, con 35 requisitos y 5 casos de uso aprobados.
- Antes de entregar el enlace a MAU, confirmar la estimación personal de horas, comprobar que `.env` no se incluya y repetir la instalación y las pruebas desde un clon limpio. El envío por email queda pendiente de autorización.

## Decisiones Técnicas

1. **Organización del código backend.** `PersonajeController` concentra el CRUD y delega validaciones a `GuardarPersonajeRequest`. `ImportacionSwapiController` orquesta la importación, `ImportarPersonajeRequest` valida el ID y `SwapiService` maneja HTTP, transformación y validación de datos externos. Eloquent representa la tabla. No se agregó Repository Pattern para una sola entidad.
2. **Componentes Vue.** `PersonajeForm` sirve para crear y editar, `PersonajeTable` muestra acciones y datos en escritorio/móvil, `ImportModal` contiene el flujo SWAPI y `AppLayout` unifica la estructura visual. Esto evita duplicar formularios y presentación.
3. **Manejo de errores de SWAPI.** El ID se valida antes del HTTP; el dominio de la URL es fijo. Hay tiempos máximos de conexión/respuesta, distinción de 404 y fallos temporales, verificación de JSON y de los campos antes de escribir. Un duplicado no genera nueva consulta; el índice UNIQUE cubre importaciones concurrentes. Un fallo `cURL error 60` se comunica como problema de configuración TLS local y se registra con su detalle técnico; los demás fallos de conexión conservan el mensaje temporal. Los errores vuelven al modal sin escritura parcial.
4. **Validaciones.** Los Form Requests validan entrada manual e ID en el servidor. El servicio valida el dato externo transformado antes de persistir. HTML añade comprobaciones básicas de campos obligatorios y límites para respuesta inmediata, pero no sustituye la validación del backend.
5. **Mayor dificultad.** El principal desafío fue diagnosticar por qué la importación real desde SWAPI fallaba, a pesar de que las pruebas con respuestas simuladas funcionaban. El servidor de Laravel utilizaba PHP 8.3 sin un paquete de certificados CA configurado y la solicitud HTTPS fallaba con `cURL error 60`. Comparé los resultados del navegador, `curl.exe` y Laravel Tinker para aislar la diferencia de entorno; con apoyo de Codex se configuraron `curl.cainfo` y `openssl.cafile` sin desactivar la verificación TLS. Después se comprobó la importación real y se añadió una prueba específica. La lección fue que una buena definición funcional no sustituye la verificación del entorno donde se ejecuta el software.
6. **Uso de Inteligencia Artificial.** Utilicé **ChatGPT** para contrastar el PDF de MAU —que ya detallaba gran parte del alcance—, organizar decisiones funcionales, elaborar el diseño técnico y preparar las instrucciones para Codex. Gestioné los requisitos y casos de uso en **MODULA**, una herramienta que desarrollé previamente con ayuda de Codex, mediante paquetes JSON del **método CLARO** y ciclos de exportación, revisión y corrección hasta aprobar la especificación. **OpenAI Codex** implementó la aplicación Laravel/Vue, las pruebas automatizadas y la corrección del problema TLS. Mi participación incluyó supervisar las decisiones, revisar el repositorio y realizar pruebas manuales en navegador, PowerShell y Tinker. Contrastamos los resultados con migraciones MySQL, pruebas Feature con `Http::fake()`, compilación de Vite e importación real desde SWAPI. Declaro explícitamente este uso de IA y asumo la responsabilidad de comprender y defender la solución entregada.
7. **Mejoras futuras.** Después de cumplir el MVP, podrían añadirse búsqueda, filtros y pruebas de accesibilidad más amplias. No forman parte de la entrega actual.
8. **Autoevaluación.** **8/10.** Considero que la solución cumple el MVP de MAU y prioriza resolver el problema de forma clara, funcional y verificable. Valoro especialmente la organización del trabajo, las validaciones y la capacidad de detectar y corregir fallos en las pruebas reales. Todavía puede mejorar en experiencia de usuario y acabado visual; también reconozco oportunidades de aprendizaje al colaborar y mantener proyectos dentro de un equipo de desarrollo.
9. **Tiempo invertido.** **Aproximadamente 4 horas efectivas (estimación).** Trabajé durante una franja aproximada de 5 horas y media, de 7:30 a. m. a 1:00 p. m. del 15 de noviembre de 2026, con pausas para actividades personales y sin registrar un cronómetro. Dediqué más tiempo a verificación y pruebas que a la definición funcional, el diseño técnico y la construcción asistida por Codex.

## Verificación realizada

En el entorno local descrito: migración MySQL ejecutada, `php artisan route:list --path=personajes` mostró las seis rutas CRUD y la ruta de importación, `php artisan test --compact` terminó con **16 pruebas aprobadas y 151 aserciones**, y `npm run build` compiló los assets. En navegador se comprobó creación, edición, confirmación de borrado, modal y presentación móvil/escritorio. Después de corregir el almacén CA, una solicitud HTTPS real ejecutada por `SwapiService` devolvió a Luke Skywalker con altura 172 y género masculino. La disponibilidad futura de SWAPI y el comportamiento en otros equipos requieren comprobación en cada entorno.
