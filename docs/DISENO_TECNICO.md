# Diseño técnico documentado — Archivo Galáctico

| Dato | Valor |
|---|---|
| Sistema | Gestión de Personajes Star Wars · prueba técnica de MAU by Contatech |
| Documento de origen | `DT-MAU-001`, v1.0, 14/11/2026, «MAU_DISENO_TECNICO_ARQUITECTURA_PARA_CODEX.md» |
| Estado de este documento | Diseño contrastado con el código de `main` el 15/11/2026 |
| Proyecto MODULA | `83195dda-d8ca-4e44-9c0f-84e5cbf79a07` |

Este documento incorpora las decisiones del diseño original y describe la implementación que existe hoy. No sustituye el PDF de MAU ni es una exportación de requisitos de MODULA. Cuando una decisión cambió después del diseño v1.0, se indica expresamente. Para una vista breve, consultar [ARQUITECTURA.md](ARQUITECTURA.md); para instalación, versiones exactas y resultados de pruebas, consultar el [README](../README.md).

## 1. Fuentes, alcance y evolución

La prioridad funcional establecida para el proyecto es: PDF original de MAU → requisitos y casos de uso vigentes aprobados en MODULA → diseño técnico original → decisiones menores de implementación. La exportación disponible al iniciar el MVP correspondía a la revisión 97, con 35 requisitos y 5 casos de uso aprobados. El README registra el cierre posterior de MODULA con 38 requisitos y 5 casos; esa exportación final **no está incorporada al repositorio**, por lo que este documento no pretende reproducir su lista ni asignar nuevas claves de trazabilidad.

PER (gestión de personajes) y SWA (importación SWAPI) son responsabilidades funcionales dentro de **una sola aplicación Laravel**. El sistema ofrece CRUD local, importación externa, paginación, formularios Inertia y una galería visual. No implementa autenticación, API REST independiente, microservicios, búsqueda, filtros ni despliegue. Los archivos de `users`, `jobs` y `cache` que conserva el esqueleto de Laravel no constituyen funcionalidades de la aplicación.

## 2. Stack y dependencias

| Capa | Implementación | Referencia en el repositorio |
|---|---|---|
| Servidor | PHP 8.3 y Laravel 12 | `composer.json`, `composer.lock` |
| Persistencia | MySQL 8, migraciones y Eloquent | `database/migrations`, `app/Models/Personaje.php` |
| Navegación | Rutas web Laravel e Inertia.js | `routes/web.php`, `resources/js/app.js` |
| Interfaz | Vue 3 Composition API, `<script setup>` y `useForm()` | `resources/js/Pages`, `resources/js/Components` |
| Estilos y assets | Tailwind CSS 4, CSS propio, Vite 8; Barlow Condensed y Manrope empaquetadas | `resources/css/app.css`, `vite.config.js`, `package-lock.json` |
| Pruebas | PHPUnit/Feature con MySQL de pruebas y `Http::fake()` | `phpunit.xml`, `tests/Feature` |

El proyecto se creó con `laravel new` sin Breeze, Jetstream ni otro starter kit. Las versiones resueltas se fijan en los archivos lock; el README consigna las verificadas en el equipo de desarrollo. Docker no forma parte de esta instalación.

## 3. Arquitectura de ejecución

```text
Navegador: Vue 3 + Inertia useForm + CSS/Tailwind
       │  formularios, enlaces Inertia, sesión y CSRF
       ▼
routes/web.php ──► middleware web
                       ├──► Form Requests en escrituras ──► controladores
                       └──► lecturas ─────────────────────► controladores
                                                           ├──► Personaje (Eloquent) ──► MySQL
                                                           └──► SwapiService ──► SWAPI people HTTPS
                                                                                 └──► SWAPI planets HTTPS (opcional)

Navegador ──► Star Wars Gallery JPEG (solo presentación opcional)
```

`PersonajeController` atiende el CRUD y entrega páginas Inertia. `ImportacionSwapiController` valida el ID, comprueba duplicados, invoca `SwapiService` y persiste el resultado. `GuardarPersonajeRequest` e `ImportarPersonajeRequest` son las fronteras de validación de entrada. La consulta de imágenes ocurre directamente en el navegador y no interviene en la importación ni en MySQL. No se añadió una capa Repository para una sola entidad.

## 4. Mapa de archivos principales

| Responsabilidad | Archivo(s) |
|---|---|
| Rutas y arranque | `routes/web.php`, `bootstrap/app.php` |
| CRUD PER | `app/Http/Controllers/PersonajeController.php`, `app/Http/Requests/GuardarPersonajeRequest.php` |
| Importación SWA | `app/Http/Controllers/ImportacionSwapiController.php`, `app/Http/Requests/ImportarPersonajeRequest.php`, `app/Services/SwapiService.php` |
| Modelo y esquema | `app/Models/Personaje.php`, `database/migrations/2026_11_14_000001_create_personajes_table.php` |
| Páginas | `resources/js/Pages/Personajes/Index.vue`, `Crear.vue`, `Editar.vue` |
| Componentes | `AppLayout.vue`, `PersonajeForm.vue`, `PersonajeTable.vue`, `PersonajeCard.vue`, `PersonajeAvatar.vue`, `ImportModal.vue` en `resources/js/Components` |
| Imagen y transición | `resources/js/lib/characterImages.js`, `resources/js/personajeTransition.js` |
| Integración Inertia/Vite | `app/Http/Middleware/HandleInertiaRequests.php`, `resources/views/app.blade.php`, `resources/js/app.js`, `vite.config.js` |
| Pruebas | `tests/Feature/PersonajesCrudTest.php`, `tests/Feature/ImportarSwapiTest.php` |

## 5. Modelo de datos e invariantes

La migración de `personajes` define **12 columnas**:

| Columna | Tipo y restricción de tabla | Regla de aplicación |
|---|---|---|
| `id` | `bigint unsigned`, clave primaria autoincremental | ID local; no se usa para construir URL de imagen. |
| `nombre` | `varchar(100)` obligatorio | Texto de 3 a 100 caracteres. |
| `altura` | entero nullable | Centímetros; 1 a 300 si se proporciona. |
| `masa` | entero nullable | Kilogramos; 1 a 500 si se proporciona. |
| `color_cabello` | `varchar(100)` nullable | Texto opcional de hasta 100 caracteres. |
| `color_ojos` | `varchar(100)` nullable | Texto opcional de hasta 100 caracteres. |
| `genero` | `varchar(100)` obligatorio | `masculino`, `femenino` o `n/a` mediante validación. |
| `planeta_origen` | `varchar(100)` nullable | Editable localmente; importación lo enriquece solo si obtiene un nombre válido. |
| `origen_swapi` | booleano, predeterminado `false` | Lo establece el servidor según el origen. |
| `swapi_id` | entero nullable, índice `UNIQUE` | Identificador externo de una importación. |
| `created_at`, `updated_at` | timestamps de Laravel | Mantenimiento por Eloquent. |

En un alta manual, el controlador fuerza `origen_swapi=false` y `swapi_id=null`, aunque el cliente envíe otros valores. En edición solo usa los campos validados y conserva los metadatos de procedencia. Al importar, el controlador fija `origen_swapi=true` y el ID solicitado. El índice `UNIQUE` protege también frente a solicitudes concurrentes.

## 6. Contrato de rutas web

| Verbo | Ruta | Acción |
|---|---|---|
| GET | `/personajes` | Listado Inertia, ordenado por `id`, `paginate(10)`. |
| GET | `/personajes/crear` | Formulario de alta. |
| POST | `/personajes` | Validar y crear un personaje manual. |
| GET | `/personajes/{personaje}/editar` | Formulario precargado mediante binding Eloquent. |
| PUT | `/personajes/{personaje}` | Validar y actualizar el mismo registro. |
| DELETE | `/personajes/{personaje}` | Eliminar después de la confirmación del navegador. |
| POST | `/personajes/importar` | Importar un ID SWAPI entre 1 y 83. |

`GET /` redirige a `/personajes`. Laravel devuelve 404 para un personaje inexistente. Las acciones de escritura redirigen al listado y comparten mensajes flash con Inertia. La paginación la realiza MySQL/Laravel; el número de columnas de la galería no altera el límite de diez registros.

## 7. Validación de entrada

`GuardarPersonajeRequest` aplica las mismas reglas a POST y PUT: `nombre` obligatorio, string de 3–100; `altura` nullable, entero de 1–300; `masa` nullable, entero de 1–500; `color_cabello`, `color_ojos` y `planeta_origen` nullable, string de máximo 100; `genero` obligatorio y limitado a `masculino`, `femenino`, `n/a`. `origen_swapi` y `swapi_id` no forman parte de los datos validados para creación o edición manual.

`ImportarPersonajeRequest` exige `id` entero entre 1 y 83. Los atributos HTML aportan respuesta inmediata, pero el servidor vuelve a validar. `PersonajeForm.vue` convierte entradas opcionales vacías a `null` antes de enviar. Inertia muestra errores por campo y estados de envío.

## 8. Importación SWAPI

1. El Form Request valida el ID. El controlador rechaza un `swapi_id` ya existente **antes** de llamar a SWAPI.
2. `SwapiService` realiza GET a `https://swapi.py4e.com/api/people/{id}/` con `acceptJson()`, `connectTimeout(3)` y `timeout(7)`. El dominio es fijo y solo interpola el entero validado.
3. Se exige un objeto JSON con `name`, `height`, `mass`, `hair_color`, `eye_color` y `gender`. Los datos transformados se validan antes de crear la fila; texto incompatible o fuera de límite provoca error controlado, sin truncamiento ni escritura parcial.
4. `male→masculino`, `female→femenino`; `n/a`, `unknown` y el valor `none` incorporado posteriormente → `n/a`. El texto de género se normaliza en mayúsculas/minúsculas. Otros valores se rechazan.
5. Altura y masa se conservan solo si representan enteros positivos dentro de 1–300 y 1–500. Valores desconocidos, decimales, inválidos o fuera de rango terminan en `null`, sin redondeo.
6. Después de validar el personaje, `homeworld` se acepta **solo** si coincide con `https://swapi.py4e.com/api/planets/{id}/`. La petición secundaria se hace a una URL reconstruida del mismo dominio, con timeout y `withoutRedirecting()`. Un nombre vacío, desconocido, `none`, `n/a` o de más de 100 caracteres se descarta. URL ajena, 404/5xx, respuesta incompatible o fallo de conexión dejan `planeta_origen=null` **sin impedir la importación**.
7. El controlador crea una sola fila con origen e ID externo. Una colisión MySQL 1062 del mismo `swapi_id` se convierte en mensaje de duplicado; no se usa `updateOrCreate` y una repetición no sobrescribe datos.

El 404 del personaje principal produce «no existe»; otros HTTP no exitosos o fallos de conexión producen mensaje de indisponibilidad. Un `cURL error 60` se identifica como problema local de certificados CA, se registra y se explica sin exponer una excepción cruda. La consulta secundaria del planeta registra sus fallos y retorna `null`. La falta de SWAPI no afecta al CRUD local.

## 9. Interfaz y navegación

`Index.vue` recibe el paginador Inertia, muestra contador, controles de página, acciones e inicia el modal. `PersonajeTable.vue` conserva el nombre histórico del componente, pero hoy distribuye una **galería** de `PersonajeCard.vue`; ya no renderiza una tabla HTML. `Crear.vue` y `Editar.vue` reutilizan `PersonajeForm.vue` con `useForm()`; la edición muestra fotografía o avatar a la izquierda en escritorio y el formulario a la derecha. `AppLayout.vue` unifica cabecera, contenido y pie.

`ImportModal.vue` usa su propio `useForm()`, presenta errores de backend, bloqueo de envíos repetidos y estado «Importando…». En móvil limita la altura a `100svh - 2rem` y permite desplazamiento interno para que los botones sigan accesibles con poco alto visible. La eliminación usa `window.confirm()` antes de `router.delete()`. `HandleInertiaRequests` comparte los mensajes flash `success` y `error`.

## 10. Fotografías opcionales y diseño responsive

`characterImages.js` construye `https://starwarsgallery.netlify.app/assets/people/{swapi_id}.jpg` únicamente si `origen_swapi` es `true` y `swapi_id` es un entero de 1 a 83. No usa el nombre ni el ID local. El navegador solicita la imagen; Laravel no la descarga ni la guarda. `PersonajeAvatar.vue` mantiene un avatar holográfico detrás de la imagen y lo deja visible en personajes manuales o ante fallo de carga, sin impedir Editar, Eliminar o importar. Las tarjetas usan carga diferida, dimensiones 400×550 y texto alternativo; el retrato de edición carga de inmediato.

La cuadrícula usa una columna por defecto, dos desde 640 px, tres desde 1024, cuatro desde 1100, cinco desde 1500 y diez desde 1600. En móvil se prioriza `object-fit: contain`; en escritorio la tarjeta superpone fotografía, degradado y datos sobre un fondo derivado de la misma imagen para el efecto cinematográfico. La edición grande muestra la foto completa con `object-fit: contain`. Los controles móviles tienen altura mínima de 44 px. Los efectos hover y de transición de vista se reducen o desactivan con `prefers-reduced-motion`; la transición entre tarjeta y edición solo se habilita desde 1024 px cuando el navegador la admite.

La galería externa es **solo una fuente de presentación**. Su disponibilidad y condiciones de uso se describen en el README; ningún flujo de datos o CRUD depende de ella.

## 11. Seguridad y comportamiento operativo

Las peticiones de escritura pasan por rutas web Laravel con sesión y protección CSRF. Eloquent evita construir SQL con entrada libre. La URL principal de SWAPI es fija y la referencia secundaria de planeta se valida estrictamente antes de reconstruir otra URL. La restricción `UNIQUE` de MySQL complementa la comprobación previa del controlador. No existe autenticación ni autorización por usuario: el alcance de la prueba es una aplicación local, no un servicio público listo para exponer sin controles adicionales.

`.env`, certificados CA locales, `vendor`, `node_modules` y datos temporales están excluidos de Git. En Windows, `scripts/configurar-php-local.ps1` prepara una configuración PHP local con CA confiable y OPcache, sin desactivar la verificación TLS. El diagnóstico y los tiempos de recarga medidos en la máquina de desarrollo se documentan en [ARQUITECTURA.md](ARQUITECTURA.md). El README contiene los pasos reproducibles de MySQL, migración, compilación y servidor local.

## 12. Estrategia de pruebas y comprobación

`tests/Feature/PersonajesCrudTest.php` cubre las rutas, persistencia manual, preservación de origen, paginación 10+1, límites y 404. `tests/Feature/ImportarSwapiTest.php` usa `Http::fake()` y `Http::preventStrayRequests()` para cubrir mapeo, ID inválido, géneros incluido `none`, medidas desconocidas, planeta conocido/desconocido, URL ajena, fallo o timeout secundarios, duplicados, colisión UNIQUE, 404/5xx, conexión, TLS y datos malformados.

`phpunit.xml` fija `DB_CONNECTION=mysql` y `DB_DATABASE=star_wars_personajes_test`; las pruebas Feature usan `RefreshDatabase`. Antes de ejecutarlas debe comprobarse que esa base está separada de `star_wars_personajes` y que ninguna configuración local la reemplaza. `npm run build` verifica la compilación Vue/Vite. `php artisan route:list --path=personajes` comprueba el contrato de rutas. Los resultados ejecutados en cada etapa, incluido el último registro de 20 pruebas y 167 aserciones, están en el README; esta documentación no supone una nueva ejecución de la suite.

## 13. Cambios respecto al diseño original v1.0

| Tema | Diseño original del 14/11/2026 | Implementación documentada |
|---|---|---|
| Planeta importado | `null`, sin segunda petición | Consulta secundaria opcional al planeta canónico de SWAPI; fallo → `null` sin bloquear importación. |
| Género `none` | No contemplado en el mapeo inicial | `none→n/a`, además de `unknown→n/a`. |
| Listado | Tabla o tarjetas responsive | Galería de tarjetas con diez columnas desde 1600 px; paginación Laravel siempre de diez registros. |
| Imágenes | No formaban parte del MVP | Star Wars Gallery opcional en navegador, asociada por `swapi_id`, con avatar de respaldo. |
| Edición | Formulario compartido | Mismo formulario, con panel informativo de imagen en escritorio. |
| Estado documental MODULA | Exportación revisión 97: 35 requisitos y 5 casos | El README comunica cierre posterior de 38 requisitos y 5 casos; la exportación final no está en este repositorio. |

Estas mejoras no cambiaron los seis verbos/URLs CRUD, la ruta de importación, las doce columnas ni el límite de diez personajes por página. Para auditar formalmente cada requisito actual de MODULA se necesita incorporar o consultar su exportación final aprobada; no debe confundirse con el JSON histórico.

## 14. Mantenimiento del documento

Al cambiar rutas, esquema, reglas SWAPI, componentes, dependencias o comportamiento de imágenes, actualizar este documento y el resumen de [ARQUITECTURA.md](ARQUITECTURA.md) junto con el código. Mantener en el README las instrucciones de instalación y los resultados realmente ejecutados. El diseño original v1.0 permanece como antecedente histórico; sus indicaciones de «propuesta» o de flujo de trabajo no sustituyen el estado verificable del repositorio ni las instrucciones actuales del responsable.
