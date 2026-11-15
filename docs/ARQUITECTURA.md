# Arquitectura técnica

## Alcance

Una aplicación Laravel 12 con rutas web e Inertia. PER gestiona personajes y SWA importa datos desde SWAPI. Comparten el modelo `Personaje` y la tabla MySQL `personajes`. No hay API REST separada ni autenticación.

```text
Vue 3 (useForm) → rutas web → controladores → Form Requests
                                      ├→ Personaje (Eloquent) → MySQL
                                      └→ SwapiService → SWAPI HTTPS
```

## Rutas y persistencia

El contrato tiene GET `/personajes`, GET `/personajes/crear`, POST `/personajes`, GET `/personajes/{id}/editar`, PUT `/personajes/{id}`, DELETE `/personajes/{id}` y POST `/personajes/importar`. Los parámetros de personaje se resuelven con binding de Eloquent; un ID inexistente retorna 404. El listado ordena por `id` y pagina a diez.

La migración crea exactamente los doce campos solicitados: `id`, `nombre`, `altura`, `masa`, `color_cabello`, `color_ojos`, `genero`, `planeta_origen`, `origen_swapi`, `swapi_id`, `created_at` y `updated_at`. El índice UNIQUE nullable en `swapi_id` protege la duplicidad incluso bajo concurrencia. En un alta manual, los metadatos de origen se asignan en el servidor. La actualización usa solo campos validados y preserva esos metadatos.

## Importación

1. `ImportarPersonajeRequest` valida ID entero entre 1 y 83.
2. El controlador consulta duplicados locales antes del HTTP.
3. `SwapiService` solicita exclusivamente `https://swapi.py4e.com/api/people/{id}/` con esperas máximas de 3 s de conexión y 7 s totales.
4. El servicio distingue 404, fallos remotos y fallos de conexión; rechaza JSON malformado y datos incompatibles antes de crear el registro.
5. Se traducen `male`, `female`, `n/a` y `unknown` a los tres valores locales admitidos. Solo medidas enteras positivas dentro de 1..300 y 1..500 se conservan; el resto pasa a `null`. El planeta queda `null` sin consultar `homeworld`.
6. El controlador crea una sola fila y convierte una colisión MySQL 1062 del mismo `swapi_id` en error de validación legible. No se actualiza el personaje importado previamente.

### Certificados TLS en Windows

El PHP 8.3 instalado mediante WinGet en el entorno de desarrollo no tenía un almacén CA configurado. El archivo `.local/php.ini`, creado para habilitar extensiones, inicialmente omitió `curl.cainfo` y `openssl.cafile`. Así, la petición HTTPS de Laravel fallaba antes de recibir una respuesta con `cURL error 60: unable to get local issuer certificate`. `curl.exe` de Windows sí recibía HTTP 200 porque usa Schannel y las autoridades certificadoras del sistema. Pasar explícitamente a PHP el paquete CA de Mozilla publicado por curl hizo que la misma petición retornara HTTP 200.

`scripts/configurar-php-local.ps1` prepara `.local/cacert.pem` y `.local/php.ini` para el PHP indicado, con verificación TLS activa. El paquete CA y la configuración generada son locales e ignorados por Git. En otra máquina debe ejecutarse el script con la ruta de su PHP 8.3 y repetir la comprobación HTTPS real. El servicio ahora distingue el error de certificado de una caída de SWAPI y registra el detalle técnico sin mostrar una excepción cruda. Las pruebas `Http::fake()` no sustituyen esta comprobación porque no establecen una conexión TLS.

## Interfaz

`Index.vue`, `Crear.vue` y `Editar.vue` son páginas Inertia. `PersonajeForm.vue` reutiliza `useForm()` para POST y PUT. `ImportModal.vue` usa otro `useForm()` para POST de SWAPI y restablece el estado de carga. `PersonajeTable.vue` presenta tabla en escritorio y tarjetas en móvil. La eliminación requiere una confirmación nativa visible. `AppLayout.vue` comparte cabecera y contenedor.

### Tiempo de recarga local

En Windows, el servidor PHP integrado se inició inicialmente sin cargar OPcache. En cada actualización del navegador PHP debía procesar de nuevo el arranque de Laravel y sus dependencias. La ruta de salud `/up`, sin consulta de personajes, tardó aproximadamente 0,25–0,35 s; el listado tardó 0,39–0,67 s, mientras que la consulta paginada a MySQL midió 29 ms. Los archivos estáticos compilados respondieron en unos 2 ms y la página no solicita SWAPI al recargarse. Esto aisló el costo principal en el procesamiento PHP local.

Una comparación con el mismo proyecto y OPcache activo redujo las respuestas calientes del listado a aproximadamente 0,09–0,19 s. El script local ahora carga `php_opcache.dll`, activa `opcache.enable_cli` para el servidor PHP integrado y mantiene `opcache.revalidate_freq=0` para detectar cambios de código durante el desarrollo. Tras regenerar `.local/php.ini` es necesario reiniciar el proceso del servidor. Estos tiempos son mediciones de esta máquina y pueden variar con la carga del navegador y del sistema.

## Verificación

Las pruebas Feature corren en `star_wars_personajes_test` mediante MySQL y `RefreshDatabase`. `Http::fake()` y `Http::preventStrayRequests()` aíslan SWAPI. Cubren CRUD, rutas, paginación, límites, procedencia, transformación, duplicados, colisión UNIQUE y fallos externos. La compilación de Vite verifica los componentes Vue. Para resultados y comandos actuales, ver el README.

## Fuentes de requisitos

El PDF original de MAU prevalece. La exportación MODULA de proyecto `83195dda-d8ca-4e44-9c0f-84e5cbf79a07`, revisión 97, contiene 35 requisitos y 5 casos de uso vigentes aprobados; en todos coincide `approved_revision` con `revision`. Las revisiones históricas de la exportación no reemplazan esos elementos. El diseño técnico aportado guía decisiones de implementación cuando las dos fuentes anteriores no prescriben el detalle. La advertencia `RF-PER-015: sin caso de uso` no reduce el contrato de seis rutas CRUD.
