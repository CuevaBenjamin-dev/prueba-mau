# Sistema de Gestión de Personajes Star Wars

Aplicación web local para registrar, consultar, actualizar, eliminar e importar personajes desde [SWAPI](https://swapi.py4e.com/documentation). Fue creada con `laravel new` sin starter kit. PER (gestión) y SWA (importación) son áreas funcionales de una sola aplicación Laravel.

## Documentación del proyecto

Los requisitos se definieron y aprobaron en MODULA, con apoyo del método CLARO para mantener su trazabilidad. La exportación vigente de la revisión 117 reúne **38 requisitos y 5 casos de uso aprobados**, organizados en las áreas funcionales PER y SWA.

- [Requisitos y análisis funcional](docs/funcional/README.md)
- [Arquitectura técnica](docs/ARQUITECTURA.md)
- [Diseño técnico documentado](docs/DISENO_TECNICO.md)

Los resultados de pruebas y ejecución se registran más adelante en este README.

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
- Galería galáctica responsive con diez personajes por página: diez tarjetas verticales en una fila desde 1600 px de ancho, menos columnas en pantallas menores, acciones apiladas y paginación superior.
- Modal para POST `/personajes/importar` con ID entero de 1 a 83, espera visible y bloqueo de envíos repetidos.
- Importación desde `https://swapi.py4e.com/api/people/{id}/`: mapeo de nombre, altura, masa, colores y género; `unknown` y `none` de género se guardan como `n/a`; medidas inválidas o fuera de rango quedan en `null`. Si `homeworld` apunta a un planeta válido de SWAPI, se consulta su nombre con una petición HTTP secundaria y controlada; si el dato o la conexión fallan, `planeta_origen` permanece `null` sin bloquear la importación.
- Duplicados bloqueados antes de la solicitud externa y mediante índice UNIQUE en MySQL. 404, 5xx, timeout, fallo de conexión y respuestas incompatibles devuelven mensajes controlados sin crear registros.
- Componentes Vue reutilizables: `AppLayout`, `PersonajeForm`, `PersonajeTable`, `PersonajeCard`, `PersonajeAvatar` e `ImportModal`.

### Imágenes de personajes

SWAPI sigue siendo la única fuente de los datos guardados en MySQL. Las fotografías son una mejora visual opcional: el navegador construye `https://starwarsgallery.netlify.app/assets/people/{id}.jpg` únicamente para registros importados con `origen_swapi=true` y `swapi_id` entero válido entre 1 y 83. El ID local y el nombre nunca se usan para asociar imágenes. El avatar holográfico aparece en registros manuales, mientras carga la imagen y cuando la fuente externa devuelve 404 o falla. La importación y el CRUD no dependen de esa galería. No se guardan fotografías ni referencias por personaje en la base de datos o en el repositorio; solo se conserva el patrón de URL en el código.

En escritorio, el archivo usa un fondo negro carbón y un encabezado compacto con el título centrado. La galería se integra en la página sin contorno exterior ni adornos flotantes. Cada tarjeta mantiene su borde, ocupa la altura útil del viewport y superpone nombre, datos y acciones sobre la fotografía. Una capa de la misma imagen completa el fondo; la fotografía nítida se funde con ella y un degradado progresivo oscurece la parte inferior para leer el contenido. El avatar holográfico conserva el mismo tratamiento cuando no hay fotografía. Desde 1600 px, diez tarjetas caben en una fila; si hay menos, conservan el ancho de esa columna sin estirarse. La composición anterior de las tarjetas permanece en anchos inferiores a 1024 px.

### Dirección visual cinematográfica

La interfaz usa carbón `#111112` y superficies `#1b1b1e`/`#222225`, texto cálido `#f1eee8`, oro envejecido `#c4a46a` para los acentos y azul metálico apagado `#789baa` como apoyo. **Barlow Condensed** define los títulos y la identidad del encabezado; **Manrope** mantiene legibles formularios, botones y datos. Ambas fuentes se empaquetan localmente mediante Fontsource bajo la licencia [SIL Open Font License 1.1](https://openfontlicense.org/). Se actualizaron encabezado, controles, formularios, modal y estados de interacción; la galería conserva las diez columnas, las proporciones, el degradado y las acciones apiladas de las tarjetas aprobadas.

La [documentación de Star Wars Gallery](https://starwarsgallery.netlify.app/swapi-image-api/) presenta el servicio como gratuito para proyectos personales, educativos y de portafolio, sin garantía de disponibilidad. También indica que las imágenes proceden de Wookieepedia y que los personajes e imágenes de Star Wars tienen derechos de Lucasfilm Ltd.; no declara que sean de dominio público ni concede una licencia comercial. Esta prueba técnica local usa referencias remotas con ese alcance. Antes de cualquier publicación o uso comercial, revisar los permisos de las imágenes y sustituir o desactivar esta fuente si corresponde. En la comprobación del 15 de noviembre de 2026, los IDs 1, 2 y 4 devolvieron JPEG 200 de 400×550 px; el ID 17 y el 999 devolvieron 404. La disponibilidad futura puede cambiar.

Fuera de la galería visual, sus imágenes opcionales y el enriquecimiento opcional del planeta de origen, no se implementaron otras funcionalidades adicionales. No hay autenticación, API REST separada ni despliegue. Esta aplicación se ejecuta localmente con PHP y MySQL; no tiene Dockerfile ni archivo Compose y no inicia contenedores propios.

## Tecnologías

Laravel 12 y PHP 8.3 en el servidor; MySQL 8 con Eloquent; Vue 3 con Composition API y `<script setup>`; Inertia.js y `useForm()`; Tailwind CSS 4; Vite 8. La comunicación de formularios usa rutas web Laravel, sesión, CSRF y redirecciones Inertia.

## Notas

- El CRUD local no depende de la disponibilidad de SWAPI. El cliente HTTP usa `connectTimeout(3)` y `timeout(7)` segundos, sin solicitudes a dominios aportados por el navegador.
- Los tests de SWAPI usan `Http::fake()` y `Http::preventStrayRequests()`; no consumen la API real. Por eso se requiere además una comprobación HTTPS real después de configurar PHP. La primera revisión manual mostró «SWAPI no está disponible» debido a un `cURL error 60` local: faltaba el paquete CA en PHP 8.3. Windows `curl.exe` respondió HTTP 200; al configurar `curl.cainfo` y `openssl.cafile`, Laravel recibió HTTP 200 y transformó a Luke Skywalker correctamente. El error no era un ID inválido ni una caída de SWAPI.
- `unknown`, `none` y `n/a` tienen significados distintos en SWAPI; el esquema local solo admite `masculino`, `femenino` y `n/a`. Para compatibilidad, esos tres valores externos se representan como `n/a`. Otros géneros no reconocidos siguen provocando error controlado.
- **Enriquecimiento opcional del planeta:** solo se consulta un enlace `homeworld` que coincida exactamente con el dominio y patrón de planetas de SWAPI. La consulta tiene timeout, no sigue redirecciones y nunca introduce dependencia para el CRUD. Si el nombre devuelto es vacío, `unknown`, `none`, `n/a` o supera 100 caracteres, se almacena `null`. **Los personajes importados antes de este cambio conservarán su `planeta_origen` existente**; no se modifican datos antiguos de forma automática.
- **Trazabilidad MODULA:** la revisión 97, usada para construir el MVP, contenía 35 requisitos y 5 casos de uso aprobados. El proyecto de requisitos se cerró posteriormente con 38 requisitos y 5 casos de uso aprobados. El enriquecimiento opcional del planeta y la normalización de `none` se incorporaron después del MVP; esta iteración no modifica MODULA.
- La advertencia histórica de trazabilidad `RF-PER-015: sin caso de uso` en MODULA no elimina las seis rutas CRUD.
- Antes de entregar el enlace a MAU, confirmar la estimación personal de horas, comprobar que `.env` no se incluya y repetir la instalación y las pruebas desde un clon limpio. El envío por email queda pendiente de autorización.

### Actualización futura de planetas anteriores

La tabla no registra si `planeta_origen` de un personaje importado fue editado manualmente. Por eso **no es seguro** completar automáticamente todos los registros con `origen_swapi = true` y `planeta_origen IS NULL`: ese filtro también podría incluir valores que una persona dejó vacíos deliberadamente. Un procedimiento seguro requiere copia de respaldo, listado previo de candidatos, revisión de su historial o confirmación manual de los IDs autorizados, consulta de SWAPI con las mismas validaciones y vista previa de cada cambio. La actualización se ejecutaría solo para esa lista revisada, en una transacción y con una condición que vuelva a comprobar `origen_swapi = true`, `planeta_origen IS NULL` y que el registro no haya cambiado desde la revisión. Registrar cada resultado y omitir los demás. Sin historial o revisión humana no puede garantizarse la protección de ediciones manuales antiguas.

## Decisiones Técnicas

1. **Organización del código backend.** `PersonajeController` concentra el CRUD y delega validaciones a `GuardarPersonajeRequest`. `ImportacionSwapiController` orquesta la importación, `ImportarPersonajeRequest` valida el ID y `SwapiService` maneja HTTP, transformación y validación de datos externos. Eloquent representa la tabla. No se agregó Repository Pattern para una sola entidad.
2. **Componentes Vue.** `PersonajeForm` sirve para crear y editar, `PersonajeTable` distribuye `PersonajeCard` en la galería responsive, `PersonajeAvatar` gestiona la fotografía y el respaldo, `ImportModal` contiene el flujo SWAPI y `AppLayout` unifica la estructura visual. Esto evita duplicar formularios y presentación.
3. **Manejo de errores de SWAPI.** El ID se valida antes del HTTP; el dominio de la URL es fijo. Hay tiempos máximos de conexión/respuesta, distinción de 404 y fallos temporales, verificación de JSON y de los campos antes de escribir. Un duplicado no genera nueva consulta; el índice UNIQUE cubre importaciones concurrentes. Un fallo `cURL error 60` se comunica como problema de configuración TLS local y se registra con su detalle técnico; los demás fallos de conexión conservan el mensaje temporal. Los errores vuelven al modal sin escritura parcial. Si `homeworld` es válido se solicita de forma secundaria el nombre del planeta; los fallos de esa consulta opcional solo dejan `planeta_origen` en `null`. Un valor de género `none` se normaliza como `n/a` para el enum local.
4. **Validaciones.** Los Form Requests validan entrada manual e ID en el servidor. El servicio valida el dato externo transformado antes de persistir. HTML añade comprobaciones básicas de campos obligatorios y límites para respuesta inmediata, pero no sustituye la validación del backend.
5. **Mayor dificultad.** El principal desafío fue diagnosticar por qué la importación real desde SWAPI fallaba, a pesar de que las pruebas con respuestas simuladas funcionaban. El servidor de Laravel utilizaba PHP 8.3 sin un paquete de certificados CA configurado y la solicitud HTTPS fallaba con `cURL error 60`. Comparé los resultados del navegador, `curl.exe` y Laravel Tinker para aislar la diferencia de entorno; con apoyo de Codex se configuraron `curl.cainfo` y `openssl.cafile` sin desactivar la verificación TLS. Después se comprobó la importación real y se añadió una prueba específica. La lección fue que una buena definición funcional no sustituye la verificación del entorno donde se ejecuta el software.
6. **Uso de Inteligencia Artificial.** Utilicé **ChatGPT** para analizar los requisitos del PDF, organizar las decisiones y definir la arquitectura. Para documentar la parte funcional empleé **MODULA**, una aplicación que desarrollé con apoyo de Codex para organizar, relacionar, revisar y aprobar requisitos y casos de uso. La complementé con mi **método CLARO**, un flujo de trabajo propio que convierte las decisiones revisadas con ChatGPT en archivos JSON estructurados para actualizar MODULA de forma controlada. Repetí el ciclo de exportar, revisar y corregir hasta cerrar la especificación. Después, **OpenAI Codex** implementó el sistema Laravel/Vue y las pruebas a partir de esa especificación y del diseño técnico. Supervisé el trabajo, revisé GitHub y realicé pruebas manuales en navegador, PowerShell y Tinker; también contrastamos migraciones MySQL, pruebas Feature, compilación y la importación real desde SWAPI. Declaro expresamente el uso de IA y mi responsabilidad de comprender y explicar la solución.
7. **Mejoras futuras.** Después de cumplir el MVP, podrían añadirse búsqueda, filtros y pruebas de accesibilidad más amplias. No forman parte de la entrega actual.
8. **Autoevaluación.** **8/10.** Considero que la solución cumple el MVP de MAU y prioriza resolver el problema de forma clara, funcional y verificable. Valoro especialmente la organización del trabajo, las validaciones y la capacidad de detectar y corregir fallos en las pruebas reales. Todavía puede mejorar en experiencia de usuario y acabado visual; también reconozco oportunidades de aprendizaje al colaborar y mantener proyectos dentro de un equipo de desarrollo.
9. **Tiempo invertido.** **Aproximadamente 7–8 horas efectivas (estimación, sin cronómetro).** Organicé el trabajo en cuatro fases: definición funcional con ChatGPT y MODULA, diseño técnico, implementación asistida por Codex y verificación con pruebas, corrección de errores y mejoras visuales. La especificación previa y la IA agilizaron la construcción inicial. Dediqué tiempo adicional a comprobar SWAPI en condiciones reales, corregir la configuración de certificados TLS y, especialmente, refinar la galería cinematográfica con fotografías de Star Wars Gallery. Aproveché el plazo disponible para mejorar la presentación sin alterar los requisitos obligatorios.

## Verificación realizada

### Comprobaciones de etapas anteriores

En el entorno local descrito: migración MySQL ejecutada, `php artisan route:list --path=personajes` mostró las seis rutas CRUD y la ruta de importación, `php artisan test --compact` terminó con **16 pruebas aprobadas y 151 aserciones**, y `npm run build` compiló los assets. En navegador se comprobó creación, edición, confirmación de borrado, modal y presentación móvil/escritorio. Después de corregir el almacén CA, una solicitud HTTPS real ejecutada por `SwapiService` devolvió a Luke Skywalker con altura 172 y género masculino. La disponibilidad futura de SWAPI y el comportamiento en otros equipos requieren comprobación en cada entorno.

En la revisión anterior, la primera página mostró diez tarjetas en cinco columnas y dos filas en Chrome de escritorio, con paginación visible; la segunda mostró la undécima. Se observó la fotografía externa de varios personajes y el avatar de un personaje manual. La creación y edición manuales mostraron mensajes de éxito; la importación HTTP real de un ID nuevo conservó `origen_swapi=true` y `planeta_origen=null`, y el segundo intento devolvió el error de duplicado. Los registros temporales de esas comprobaciones se eliminaron. La automatización del navegador se detuvo al abrir la confirmación de borrado; la prueba visual del respaldo por fallo de imagen y la revisión de todos los tamaños de pantalla quedaron pendientes en esa etapa. Las 16 pruebas Feature siguieron aprobadas y Vite compiló tras los cambios.

El refinamiento visual posterior distribuye las diez tarjetas en una sola fila desde 1600 px, apila Editar y Eliminar, muestra las fotografías con `object-fit: contain`, sitúa la paginación junto al contador y presenta la fotografía y el formulario de edición en dos columnas de escritorio. Las transiciones de las tarjetas respetan `prefers-reduced-motion`. La lógica de datos y las rutas no cambiaron. En Chrome se observaron diez tarjetas en una fila a 1600 y 1920 px, cuatro columnas a 1366 px, dos a 768 px y una a 390 px, sin desbordamiento horizontal visible. La página 2 mostró el undécimo personaje; edición mostró el retrato completo de un personaje SWAPI y el avatar de un personaje manual. No se simuló un fallo de la fuente de imágenes ni se activó `prefers-reduced-motion` en el navegador durante esta revisión.

Tras integrar localmente el PR #1, `php artisan test --compact` terminó con **20 pruebas aprobadas y 167 aserciones** en la base separada `star_wars_personajes_test`; `npm run build` compiló y `php artisan route:list --path=personajes` mantuvo las siete rutas. En la aplicación local se importó IG-88 (SWAPI 23) con género `n/a` y planeta `null` porque el planeta remoto es desconocido. Leia Organa (SWAPI 5) se importó con `planeta_origen = Alderaan` desde la consulta secundaria real. Repetir el ID 23 mostró el error de duplicado y no creó otro registro. Con 17 personajes, la galería entregó diez tarjetas en la primera página y siete en la segunda a 1600 × 900 px. Se creó, editó y eliminó un personaje manual temporal; la cancelación de su primer borrado conservó el registro. Las pruebas con `Http::fake()` comprobaron que una respuesta 503 o un timeout del planeta no bloquean la importación. No se modificaron los planetas de personajes ya existentes.

### Comprobación final de esta versión (15 de noviembre de 2026)

`npm run build` terminó correctamente; `php artisan route:list --path=personajes` mostró las **seis rutas CRUD y la ruta de importación**; `php artisan test --compact` finalizó con **20 pruebas aprobadas y 167 aserciones**. Antes de las pruebas se comprobó que `.env` apunta a `star_wars_personajes`, `phpunit.xml` a `star_wars_personajes_test`, que no existe `.env.testing` y que no hay caché de configuración. Las pruebas Feature usan `RefreshDatabase` únicamente bajo la configuración de testing.

En Chrome DevTools se revisó el listado a **390 × 844**, **375 × 812** y **320 × 568 px**: una columna, sin desbordamiento horizontal, tarjetas y datos legibles, fotografías JPEG cargadas (ancho natural de 400 px) y acciones táctiles de 44 px. Con 11 registros, la página 1 mostró diez tarjetas y la página 2 una. A 320 px se comprobó el formulario de creación con todos los campos y la validación de nombre mínimo; un registro manual temporal se creó, editó, mostró el avatar y mantuvo el origen Manual. Cancelar su eliminación lo conservó; confirmar la eliminación borró solo ese registro. En el modal se observaron el rechazo del ID 84, el estado «Importando…» con envío bloqueado y el error de duplicado del ID 44. A 320 × 400 px, como simulación de una altura reducida por teclado, el modal permaneció dentro del viewport y permitió desplazarse hasta sus botones. `prefers-reduced-motion: reduce` dejó la transición de tarjeta en 0 s. A **1600 × 900 px** se conservaron diez tarjetas en una fila y el formulario de edición en dos columnas. Estas son comprobaciones con emulación de Chrome; no se verificó en un dispositivo físico ni se volvió a importar un ID nuevo desde SWAPI en esta iteración.
