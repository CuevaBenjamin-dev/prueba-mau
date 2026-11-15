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

Antes de empezar, disponer de **PHP 8.3**, **Composer**, **Node.js 22.12+** y **MySQL 8** en funcionamiento. MySQL Server debe estar instalado o accesible; Docker y MySQL Workbench no son obligatorios.

1. **Clonar el repositorio:**

   ```bash
   git clone https://github.com/CuevaBenjamin-dev/prueba-mau.git
   cd prueba-mau
   ```

2. **Instalar dependencias:**

   ```bash
   composer install
   npm install
   ```

3. **Configurar la base de datos:**

   Se presupone que **MySQL Server 8 ya está instalado y funcionando**, y que conoces el usuario y su contraseña. No necesitas Docker ni MySQL Workbench.

   **a. Abrir MySQL.** En PowerShell (o en una terminal) escribe:

   ```bash
   mysql -u root -p
   ```

   Introduce **la contraseña de tu instalación de MySQL** cuando se solicite; no se mostrará mientras escribes. `root` es un ejemplo: si tienes otro usuario con permisos para crear bases de datos, utilízalo. Si MySQL escucha en otro puerto, agrega `-h 127.0.0.1 -P PUERTO` al comando.

   **b. Crear la base.** Cuando aparezca `mysql>`, pega la instrucción siguiente **dentro de MySQL, no directamente en PowerShell**:

   ```sql
   CREATE DATABASE star_wars_personajes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

   Si aparece `Query OK`, escribe `exit;` para volver a la terminal.

   **c. Preparar Laravel.** En la carpeta clonada `prueba-mau`, ejecuta:

   ```bash
   cp .env.example .env
   ```

   Abre `.env` con un editor de texto (en Windows puedes ejecutar `notepad .env`) y completa la conexión con **los datos de tu MySQL**:

   ```ini
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=star_wars_personajes
   DB_USERNAME=root
   DB_PASSWORD=
   ```

   Modifica `DB_PORT`, `DB_USERNAME` y `DB_PASSWORD` según tu servidor. `DB_PASSWORD` contiene la **misma contraseña del usuario MySQL** que utilizaste para conectarte; no viene en el repositorio. Guarda el archivo y no lo publiques.

   **d. Generar la clave de Laravel.** De vuelta en la terminal de `prueba-mau`, ejecuta:

   ```bash
   php artisan key:generate
   ```

   Si `mysql` no se reconoce, comprueba que el cliente de MySQL esté instalado y disponible en el PATH; también puedes ejecutar SQL desde otra herramienta de administración del servidor.

4. **Ejecutar migraciones:**

   ```bash
   php artisan migrate
   ```

5. **Compilar assets:**

   ```bash
   npm run build
   ```

6. **Iniciar servidor:**

   ```bash
   php artisan serve
   ```

7. **Acceder:**

   http://localhost:8000/personajes

### Ayuda opcional para Windows y pruebas

- **Error de certificado HTTPS (`cURL error 60`) o extensiones faltantes en PHP para Windows:** si utilizas una instalación de PHP 8.3 por WinGet o ZIP que necesite configuración adicional, ejecuta desde la carpeta del proyecto:

  ```powershell
  $php83 = 'C:\ruta\a\PHP-8.3\php.exe'
  .\scripts\configurar-php-local.ps1 -PhpExecutable $php83
  $env:PHPRC = (Resolve-Path .local\php.ini).Path
  $env:PATH = "$(Split-Path $php83);$env:PATH"
  ```

  Sustituye `$php83` por la ruta real de PHP. El script configura certificados CA confiables sin desactivar TLS y activa OPcache para acelerar las recargas del servidor PHP local; `.local` está excluida de Git. Reinicia `php artisan serve` después de ejecutar el script. Si PHP ya tiene certificados válidos y OPcache activo, no necesitas este paso.

- **Pruebas automatizadas (opcional):** crea primero una base MySQL **separada** llamada `star_wars_personajes_test` y revisa la conexión de pruebas en `phpunit.xml` antes de ejecutar `php artisan test`. Las pruebas recrean tablas; nunca deben apuntar a la base principal. Para ver las rutas puedes usar `php artisan route:list --path=personajes`.

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
6. **Uso de Inteligencia Artificial.** Utilicé **ChatGPT** para analizar los requisitos del PDF, organizar las decisiones y definir la arquitectura. Para documentar la parte funcional empleé **MODULA**, una aplicación que desarrollé con apoyo de Codex para organizar, relacionar, revisar y aprobar requisitos y casos de uso. La complementé con mi **método CLARO**, un flujo de trabajo propio que convierte las decisiones revisadas con ChatGPT en archivos JSON estructurados para actualizar MODULA de forma controlada. Repetí el ciclo de exportar, revisar y corregir hasta cerrar la especificación. Después, **OpenAI Codex** implementó el sistema Laravel/Vue y las pruebas a partir de esa especificación y del diseño técnico. Supervisé el trabajo, revisé GitHub y realicé pruebas manuales en navegador, PowerShell y Tinker; también contrastamos migraciones MySQL, pruebas Feature, compilación y la importación real desde SWAPI. Declaro expresamente el uso de IA y mi responsabilidad de comprender y explicar la solución.
7. **Mejoras futuras.** Después de cumplir el MVP, podrían añadirse búsqueda, filtros y pruebas de accesibilidad más amplias. No forman parte de la entrega actual.
8. **Autoevaluación.** **8/10.** Considero que la solución cumple el MVP de MAU y prioriza resolver el problema de forma clara, funcional y verificable. Valoro especialmente la organización del trabajo, las validaciones y la capacidad de detectar y corregir fallos en las pruebas reales. Todavía puede mejorar en experiencia de usuario y acabado visual; también reconozco oportunidades de aprendizaje al colaborar y mantener proyectos dentro de un equipo de desarrollo.
9. **Tiempo invertido.** **Aproximadamente 4 horas efectivas (estimación).** Trabajé durante una franja aproximada de 5 horas y media, de 7:30 a. m. a 1:00 p. m. del 15 de noviembre de 2026, con pausas para actividades personales y sin registrar un cronómetro. Dediqué más tiempo a verificación y pruebas que a la definición funcional, el diseño técnico y la construcción asistida por Codex.

## Verificación realizada

En el entorno local descrito: migración MySQL ejecutada, `php artisan route:list --path=personajes` mostró las seis rutas CRUD y la ruta de importación, `php artisan test --compact` terminó con **16 pruebas aprobadas y 151 aserciones**, y `npm run build` compiló los assets. En navegador se comprobó creación, edición, confirmación de borrado, modal y presentación móvil/escritorio. Después de corregir el almacén CA, una solicitud HTTPS real ejecutada por `SwapiService` devolvió a Luke Skywalker con altura 172 y género masculino. La disponibilidad futura de SWAPI y el comportamiento en otros equipos requieren comprobación en cada entorno.
