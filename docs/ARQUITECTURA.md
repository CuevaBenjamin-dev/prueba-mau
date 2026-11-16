# Arquitectura técnica — estado implementado

Este resumen describe la aplicación Laravel/Vue existente. El [diseño técnico documentado](DISENO_TECNICO.md) desarrolla las decisiones, el modelo, los flujos, las pruebas y los cambios respecto al diseño original `DT-MAU-001` del 14/11/2026. El [README](../README.md) contiene la instalación y los resultados de verificación.

## Alcance y fuentes

PER gestiona personajes y SWA importa desde SWAPI. Son responsabilidades funcionales de **una sola aplicación Laravel 12**, con rutas web e Inertia.js; comparten `Personaje` y la tabla MySQL `personajes`. No hay API REST separada, autenticación, microservicios ni Docker obligatorio.

El PDF de MAU fija las obligaciones. La exportación MODULA usada al iniciar el MVP era la revisión 97 con 35 requisitos y 5 casos de uso aprobados. El README registra el cierre posterior del proyecto funcional con 38 requisitos y 5 casos; **la exportación final no está en este repositorio**. Por ello, este resumen documenta el código actual y no pretende reproducir la especificación funcional completa ni reemplazarla.

```text
Navegador: Vue 3 + Inertia useForm + Tailwind/CSS
       │ rutas web, sesión, CSRF y mensajes flash
       ▼
Middleware web ──► Form Requests en escrituras ──► controladores Laravel
                                                   ├──► Personaje (Eloquent) ──► MySQL
                                                   └──► SwapiService ──► SWAPI people HTTPS
                                                                         └──► SWAPI planets HTTPS (opcional)

Navegador ──► Star Wars Gallery (fotografías opcionales)
```

## Rutas y persistencia

`routes/web.php` declara GET `/personajes`, GET `/personajes/crear`, POST `/personajes`, GET `/personajes/{personaje}/editar`, PUT `/personajes/{personaje}`, DELETE `/personajes/{personaje}` y POST `/personajes/importar`. `GET /` redirige al listado. Eloquent resuelve los IDs existentes y Laravel devuelve 404 para los inexistentes.

`PersonajeController` ordena por `id` y usa `paginate(10)`. La migración define las doce columnas solicitadas: `id`, `nombre`, `altura`, `masa`, `color_cabello`, `color_ojos`, `genero`, `planeta_origen`, `origen_swapi`, `swapi_id`, `created_at`, `updated_at`. `swapi_id` es nullable y `UNIQUE`; `origen_swapi` tiene predeterminado `false`. El alta manual fuerza `origen_swapi=false` y `swapi_id=null`. La edición solo aplica campos validados y conserva la procedencia.

`GuardarPersonajeRequest` valida nombre de 3–100 caracteres, altura entera opcional 1–300, masa entera opcional 1–500, género obligatorio entre `masculino`, `femenino`, `n/a` y textos opcionales de hasta 100 caracteres. `ImportarPersonajeRequest` valida ID entero 1–83. La interfaz añade validación HTML, pero el servidor es la frontera de confianza.

## Importación SWAPI

`ImportacionSwapiController` rechaza primero un `swapi_id` duplicado. `SwapiService` consulta `https://swapi.py4e.com/api/people/{id}/` con 3 s máximos de conexión y 7 s de espera total. Comprueba estado, estructura JSON y campos requeridos antes de devolver datos para una sola inserción. Un 404, un fallo remoto, timeout, problema de conexión o respuesta incompatible produce un error controlado. La colisión concurrente MySQL 1062 del mismo ID se convierte en mensaje de duplicado; no se sobrescriben registros existentes.

El servicio traduce `male→masculino`, `female→femenino`, y `n/a`, `unknown` o `none→n/a`. Solo conserva altura y masa enteras dentro de sus rangos; los valores desconocidos, no enteros o fuera de rango quedan `null`. **Cambio posterior al diseño original:** si `homeworld` coincide exactamente con una URL canónica de planetas de SWAPI, se solicita de forma secundaria su nombre, sin seguir redirecciones. URL inválida, planeta desconocido, respuesta incorrecta o fallo secundario dejan `planeta_origen=null` y no bloquean la importación. Ninguna consulta externa participa en el CRUD local.

### Certificados TLS en Windows

El PHP 8.3 instalado mediante WinGet en el entorno de desarrollo no tenía inicialmente un almacén CA configurado. Laravel fallaba con `cURL error 60: unable to get local issuer certificate`, mientras `curl.exe` de Windows respondía HTTP 200 al usar Schannel. `scripts/configurar-php-local.ps1` prepara `.local/cacert.pem` y `.local/php.ini` con `curl.cainfo` y `openssl.cafile`, manteniendo TLS activo. Estos archivos son locales y están ignorados por Git. En otro equipo se debe configurar su propio PHP y comprobar la conexión HTTPS real; `Http::fake()` no prueba TLS.

## Interfaz, imágenes y responsive

`Index.vue`, `Crear.vue` y `Editar.vue` son páginas Inertia. `PersonajeForm.vue` comparte `useForm()` para POST/PUT. `ImportModal.vue` usa otro `useForm()`, muestra errores, carga y evita envíos repetidos. `PersonajeTable.vue` mantiene su nombre por continuidad, pero presenta una **galería de `PersonajeCard.vue` en todos los tamaños**. `AppLayout.vue` comparte cabecera y pie. La eliminación requiere `window.confirm()` antes de la petición DELETE; el middleware de Inertia comparte mensajes flash.

`PersonajeAvatar.vue` usa `characterImages.js` para construir una URL de Star Wars Gallery **solo** cuando el registro procede de SWAPI y su `swapi_id` es un entero válido. La fotografía se carga en el navegador, sin guardarse en MySQL ni en el repositorio; si no existe o falla, permanece el avatar holográfico. Su disponibilidad no condiciona el CRUD ni la importación.

La cuadrícula usa 1 columna por defecto; 2 desde 640 px, 3 desde 1024, 4 desde 1100, 5 desde 1500 y 10 desde 1600. Laravel sigue entregando **diez registros por página** independientemente del ancho. El retrato móvil y el de edición priorizan `object-fit: contain`; en tarjetas amplias se usa una composición cinematográfica con capas, degradado y fotografía. La edición presenta imagen y formulario en dos columnas desde 1024 px. El modal tiene desplazamiento interno si el viewport es bajo y los controles móviles tienen altura mínima de 44 px. Las transiciones respetan `prefers-reduced-motion`.

### Tiempo de recarga local

El servidor PHP integrado se inició inicialmente sin OPcache. En esa máquina, `/up` tardó aproximadamente 0,25–0,35 s y el listado 0,39–0,67 s, frente a 29 ms de consulta paginada a MySQL y unos 2 ms de archivos estáticos. Con OPcache activo, las respuestas calientes del listado se midieron en aproximadamente 0,09–0,19 s. El script local habilita OPcache y `opcache.revalidate_freq=0` para desarrollo; hay que reiniciar el servidor tras regenerar `.local/php.ini`. Son mediciones históricas de ese entorno, no una garantía para otros equipos.

## Verificación y límites documentales

Las pruebas Feature usan MySQL `star_wars_personajes_test`, `RefreshDatabase`, `Http::fake()` y `Http::preventStrayRequests()`. Cubren CRUD, paginación 10+1, límites, procedencia, transformación SWAPI, planeta opcional, duplicados, colisión UNIQUE y fallos externos. `npm run build` verifica los componentes Vue y `php artisan route:list --path=personajes` verifica el contrato de rutas. El README distingue los resultados históricos de la última ejecución; actualizar este archivo no ejecuta de nuevo esas pruebas.

El diseño original v1.0 proponía `planeta_origen=null` sin segunda petición y tabla o tarjetas responsive. Esas decisiones fueron ampliadas posteriormente con planeta opcional, género `none`, galería, imágenes externas y avatar de respaldo. El diseño técnico completo y la evolución están explicados en [DISENO_TECNICO.md](DISENO_TECNICO.md). La advertencia histórica `RF-PER-015: sin caso de uso` no reduce las seis rutas CRUD.
