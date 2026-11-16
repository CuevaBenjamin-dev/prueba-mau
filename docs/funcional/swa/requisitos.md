# Requisitos — SWA · Importación SWAPI

[Índice funcional](../README.md) · [Análisis funcional SWA](analisis-funcional.md)

Fuente: exportación MODULA revisión global 117. Los nueve requisitos están aprobados. `RF-SWA-003`, `RF-SWA-004` y `RF-SWA-009` se documentan en su **revisión vigente 3**; no se mezclan con versiones históricas. SWAPI es la fuente de datos persistidos; Star Wars Gallery solo proporciona imágenes opcionales a la interfaz de PER.

### RF-SWA-001

**Nombre:** Abrir modal de importación

**Tipo:** Funcional (`functional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** En /personajes, el botón Importar desde SWAPI abre un modal que contiene el campo ID, Importar, mensajes y estado de carga.

**Justificación registrada:** PDF p. 3: Modal Importar desde SWAPI.

**Criterios de aceptación:**

1. El botón abre el modal sin salir de /personajes.
2. El modal permite ingresar un ID y ejecutar importación.
3. La interfaz comunica éxito y error.

**Casos de uso vinculados en MODULA:** [CU-SWA-001](analisis-funcional.md#cu-swa-001).

### RF-SWA-002

**Nombre:** Validar ID de SWAPI

**Tipo:** Funcional (`functional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** POST /personajes/importar recibe un ID entero dentro del intervalo inclusivo 1..83.

**Justificación registrada:** PDF p. 2: ID del personaje entre 1 y 83.

**Criterios de aceptación:**

1. IDs 1 y 83 superan la validación local.
2. IDs 0, 84, decimales, texto y vacío son rechazados sin consultar SWAPI.
3. La interfaz informa al usuario por qué el ID no es válido.

**Casos de uso vinculados en MODULA:** [CU-SWA-001](analisis-funcional.md#cu-swa-001).

### RF-SWA-003

**Nombre:** Consultar API de personajes

**Tipo:** Funcional (`functional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 3.

**Descripción aprobada:** Consultar la fuente obligatoria de personaje https://swapi.py4e.com/api/people/{id}/ para ID 1..83, con timeout y manejo 404, 5xx y errores de conexión. Como complemento opcional, si la respuesta contiene homeworld con URL HTTPS canónica /api/planets/{id}/ del MISMO dominio de SWAPI, consultar exclusivamente esa ruta para obtener el nombre del planeta; no seguir redirecciones ni consultar dominios arbitrarios. La indisponibilidad del planeta no impide importar el personaje ni afecta el CRUD local.

**Justificación registrada:** PDF pp. 2-3 para consulta obligatoria; ampliación voluntaria solicitada por el responsable: resolver opcionalmente homeworld sin fuentes de datos alternativas.

**Criterios de aceptación:**

1. El GET de personaje conserva URL y validación de ID especificadas por MAU, sin consultar terceros.
2. Una URL canónica válida de homeworld permite un GET adicional /api/planets/{id}/ con timeout y sin redirecciones.
3. URLs homeworld ajenas, malformadas o no seguras nunca se solicitan (no SSRF).
4. Un error 404/5xx/timeout del personaje bloquea la importación sin persistencia.
5. Un error 404/5xx/timeout del planeta no bloquea importación: planeta_origen queda null.
6. En todos los resultados se recupera el estado de carga y el CRUD local permanece disponible.

**Excepciones o restricciones:**

- La disponibilidad externa de SWAPI y el recurso de planetas no está garantizada.
- La segunda petición solo es opcional: no seguir redirecciones, no usar URLs proporcionadas libremente y no reintentar indefinidamente.
- Los registros importados antes de la mejora no se actualizan automáticamente.

**Casos de uso vinculados en MODULA:** [CU-SWA-001](analisis-funcional.md#cu-swa-001).

### RF-SWA-004

**Nombre:** Transformar campos SWAPI

**Tipo:** Funcional (`functional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 3.

**Descripción aprobada:** Convertir name→nombre, height→altura, mass→masa, hair_color→color_cabello, eye_color→color_ojos, gender→genero. Para género: male→masculino, female→femenino, n/a→n/a, unknown→n/a y none→n/a; otros valores inesperados provocan error controlado. Altura y masa se representan como enteros solo si son positivas y caben en 1..300 y 1..500; de lo contrario null, sin redondear. Como enriquecimiento opcional, consultar la URL segura de homeworld del mismo SWAPI y guardar el nombre válido del planeta (máximo 100 caracteres) en planeta_origen; si falta, es desconocido o falla la consulta, null.

**Justificación registrada:** PDF pp. 2-3 fija mapeo obligatorio; soporte adicional del valor none observado en IG-88 y enriquecimiento opcional del campo nullable de planeta.

**Criterios de aceptación:**

1. Los géneros male/female/n/a/unknown/none se convierten al enum local; otros géneros siguen causando error sin escritura.
2. IG-88 (ID 23) con gender=none puede importarse como n/a, si los demás datos obligatorios son válidos.
3. Altura y masa fuera de rango/no enteras/desconocidas se guardan null sin inventar o redondear valores.
4. El nombre del planeta se guarda solo si homeworld apunta a URL segura de SWAPI y devuelve un nombre de 1 a 100 caracteres que no sea unknown, none o n/a.
5. Si no hay homeworld fiable o falla el GET opcional del planeta, planeta_origen=null y la importación sigue.
6. No hay migraciones nuevas ni modificación retroactiva de personajes ya guardados.

**Excepciones o restricciones:**

- none, unknown y n/a no son necesariamente equivalentes semánticamente; se consolidan en el único valor local disponible por decisión de compatibilidad.
- El nombre de planeta solo se usa si el recurso externo devuelve un valor representable; no inventar datos.
- No consultar planetas fuera de swapi.py4e.com.

**Casos de uso vinculados en MODULA:** [CU-SWA-001](analisis-funcional.md#cu-swa-001).

### RF-SWA-005

**Nombre:** Registrar procedencia de SWAPI

**Tipo:** Funcional (`functional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** Los personajes importados conservan origen_swapi=true y el swapi_id original; los datos se persisten en la misma tabla personajes.

**Justificación registrada:** PDF pp. 1-3: origen_swapi y swapi_id.

**Criterios de aceptación:**

1. Después de importar ID N, swapi_id=N y origen_swapi=true.
2. El personaje importado se visualiza con origen SWAPI en el listado.
3. No se introduce una segunda tabla de personajes de SWAPI.

**Casos de uso vinculados en MODULA:** [CU-SWA-001](analisis-funcional.md#cu-swa-001).

### RF-SWA-006

**Nombre:** Impedir importaciones repetidas

**Tipo:** Funcional (`functional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** No permitir importar más de una vez el mismo swapi_id; mantener restricción unique en base de datos, además de control en la aplicación.

**Justificación registrada:** PDF pp. 1-3: swapi_id unique y prohibición de duplicados.

**Criterios de aceptación:**

1. La primera importación válida crea un personaje.
2. Una segunda importación del mismo swapi_id no crea otro.
3. Ante una colisión concurrente de unicidad, se devuelve error controlado y no un fallo no gestionado.

**Casos de uso vinculados en MODULA:** [CU-SWA-001](analisis-funcional.md#cu-swa-001).

### RF-SWA-007

**Nombre:** Comunicar errores de SWAPI

**Tipo:** Funcional (`functional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** Mostrar mensajes comprensibles cuando SWAPI devuelve 404, un error del servidor, timeout o no es posible conectarse.

**Justificación registrada:** PDF pp. 2-4: manejo de errores externos y feedback.

**Criterios de aceptación:**

1. Los errores externos no generan registros parciales.
2. La pantalla muestra error visible y permite continuar usándola.
3. La aplicación no muestra un error técnico sin procesar al usuario.

**Casos de uso vinculados en MODULA:** [CU-SWA-001](analisis-funcional.md#cu-swa-001).

### RF-SWA-008

**Nombre:** Mostrar estado de carga de importación

**Tipo:** Funcional (`functional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** Durante la consulta a SWAPI, mostrar indicador de procesamiento y evitar envíos múltiples involuntarios.

**Justificación registrada:** PDF pp. 3-4: modal con loading state y mensajes.

**Criterios de aceptación:**

1. Al importar se muestra estado de carga.
2. El usuario recibe resultado visible al finalizar.
3. No quedan controles permanentemente bloqueados después de un error.

**Casos de uso vinculados en MODULA:** [CU-SWA-001](analisis-funcional.md#cu-swa-001).

### RF-SWA-009

**Nombre:** Conciliar datos externos con validaciones locales

**Tipo:** Funcional (`functional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 3.

**Descripción aprobada:** Antes de persistir validar nombre 3..100, textos opcionales máximo 100, altura y masa enteras en sus límites o null, y género reconocido traducido al enum local. Se admite explícitamente none→n/a como normalización externa adicional. Rechazar datos principales inválidos sin escritura parcial ni truncamientos. El nombre del planeta es enriquecimiento opcional mediante segunda consulta al homeworld seguro; normalizar null cuando sea inexistente, desconocido, inválido o haya error externo, sin convertir esa falla secundaria en rechazo de personaje.

**Justificación registrada:** Compatibilidad estricta de validación local con variantes de datos reales de SWAPI y política opcional de homeworld aprobada por el responsable.

**Criterios de aceptación:**

1. No se guardan datos principales fuera de las validaciones locales y no se relajan reglas de nombre/textos.
2. Un género distinto de male/female/n/a/unknown/none se rechaza con un error controlado.
3. planeta_origen permanece null si homeworld es inválido o la consulta secundaria falla.
4. La consulta del planeta nunca impide el guardado de un personaje cuyos campos obligatorios sean válidos.
5. La dependencia secundaria usa únicamente URLs reconstruidas con IDs validados del dominio permitido y no sigue redirecciones.
6. Registros importados previamente no se sobrescriben en forma implícita.

**Excepciones o restricciones:**

- La prueba técnica no obliga a consultar homeworld; la nueva conducta es ampliación funcional voluntaria.
- Una respuesta de planeta malformada no puede provocar una excepción no controlada o persistencia parcial.

**Casos de uso vinculados en MODULA:** [CU-SWA-001](analisis-funcional.md#cu-swa-001).
