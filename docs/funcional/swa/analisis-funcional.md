# Análisis funcional — SWA · Importación SWAPI

[Índice funcional](../README.md) · [Requisitos SWA](requisitos.md)

El caso de uso vigente aprobado describe la validación previa, consulta obligatoria de personaje, transformación, consulta opcional del planeta, persistencia única y recuperación ante errores. Una falla del planeta no invalida un personaje principal válido; una falla del personaje principal no deja registros parciales.

### CU-SWA-001

**Nombre:** Importar personaje desde SWAPI

**Estado:** Aprobado · **Revisión vigente y aprobada:** 3.

**Objetivo:** Obtener por ID y guardar un personaje remoto con datos transformados y procedencia.

**Actor principal:** Usuario de la aplicación.

**Precondiciones:** La aplicación web y su base de datos local están disponibles. SWAPI es un servicio externo que puede estar disponible o caído; su disponibilidad no es precondición para abrir el modal.

**Disparador:** El usuario pulsa Importar desde SWAPI.

**Flujo principal:**

1. El usuario abre el modal Importar desde SWAPI desde /personajes.
2. Introduce un ID entero 1..83 y pulsa Importar; la interfaz muestra estado de carga.
3. Laravel valida el ID y rechaza duplicados antes de enviar peticiones externas.
4. Consulta https://swapi.py4e.com/api/people/{id}/ con timeout y manejo de errores.
5. Transforma y valida los campos principales; male/female/n/a/unknown/none se mapean al enum local según la política aprobada.
6. Si homeworld refiere un planeta seguro de SWAPI, consulta opcionalmente /api/planets/{id}/ para obtener name válido; si falta o falla, planeta_origen queda null.
7. Guarda exactamente un personaje con origen_swapi=true y swapi_id único sin afectar los datos previos.
8. La interfaz informa el resultado y restablece el estado de carga; el nuevo registro figura en el listado.

**Flujos alternativos:**

- ID vacío, no entero o fuera de 1..83: rechazo sin petición HTTP.
- swapi_id ya importado: informa duplicado, no consulta ni crea registros.
- Valores altura/masa unknown, no enteros o fuera de rango: se guardan null si el resto es válido.
- gender=unknown o none: se guarda genero=n/a como compatibilidad explícita.
- homeworld ausente/desconocido/inválido o consulta de planeta fallida: importación continúa con planeta_origen=null.
- homeworld seguro con nombre de planeta válido: se persiste dicho nombre sin modificar campos existentes de otros personajes.

**Excepciones:**

- SWAPI personaje 404/5xx/timeout/red: se rechaza importación sin datos parciales.
- Datos principales malformados o género no reconocido: error controlado sin guardar.
- URL homeworld externa o manipulada: no se consulta; planeta_origen=null.
- SWAPI planeta 404/5xx/timeout o respuesta sin name válido: no bloquea importación; planeta_origen=null.
- UNIQUE colisiona por concurrencia: no se guarda duplicado y se informa error amigable.
- El indicador de carga siempre vuelve al estado normal; el CRUD local permanece operativo.

**Postcondiciones:** Tras éxito existe un único personaje nuevo con origen_swapi=true, swapi_id único y planeta_origen con nombre validado si se pudo obtener, o null si no. En un error principal no se persisten datos parciales. No se modifican personajes ya existentes.

**Criterios de aceptación:**

1. IDs 1 y 83 válidos y 0,84,vacío y decimales inválidos según contrato original.
2. ID 23 IG-88 se importa con genero=n/a cuando SWAPI devuelve none.
3. Luke Skywalker con homeworld planets/1/ guarda planeta_origen=Tatooine si esa respuesta contiene dicho nombre.
4. El planeta ausente, desconocido, malformado o con error HTTP produce null sin bloquear el personaje.
5. No hay solicitudes a dominios externos arbitrarios en homeworld ni redirecciones seguidas.
6. Los duplicados no se crean y fallos del GET principal siguen bloqueando sin registro parcial.
7. Se mantiene la carga y retroalimentación de la UI.

**Requisitos vinculados en MODULA:** [RF-PER-001](../per/requisitos.md#rf-per-001), [RF-SWA-001](requisitos.md#rf-swa-001), [RF-SWA-002](requisitos.md#rf-swa-002), [RF-SWA-003](requisitos.md#rf-swa-003), [RF-SWA-004](requisitos.md#rf-swa-004), [RF-SWA-005](requisitos.md#rf-swa-005), [RF-SWA-006](requisitos.md#rf-swa-006), [RF-SWA-007](requisitos.md#rf-swa-007), [RF-SWA-008](requisitos.md#rf-swa-008), [RF-SWA-009](requisitos.md#rf-swa-009).
