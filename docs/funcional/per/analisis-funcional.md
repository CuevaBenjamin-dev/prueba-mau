# Análisis funcional — PER · Gestión de Personajes

[Índice funcional](../README.md) · [Requisitos PER](requisitos.md)

Los cuatro casos de uso siguientes corresponden a los elementos vigentes aprobados de MODULA. El actor principal es «Usuario de la aplicación». Los flujos y criterios expresan la especificación; el README principal contiene la evidencia de ejecución.

## Reglas comunes

- La lista se ordena y pagina desde Laravel en grupos de diez; el número de columnas visuales no cambia ese límite.
- Crear y editar comparten reglas de nombre, altura, masa, género y textos opcionales. Una validación fallida no modifica el registro.
- Crear manualmente fija `origen_swapi=false` y `swapi_id=null`; editar conserva `id`, `origen_swapi` y `swapi_id`.
- La imagen externa es opcional. Su ausencia se resuelve con avatar y no bloquea consulta, edición o eliminación.
- El término «fila» del disparador aprobado de CU-PER-004 se conserva como texto de origen; la presentación vigente usa tarjetas.

## Casos de uso aprobados

### CU-PER-001

**Nombre:** Consultar y paginar personajes

**Estado:** Aprobado · **Revisión vigente y aprobada:** 2.

**Objetivo:** Consultar personajes existentes, conocer su procedencia y navegar de diez en diez.

**Actor principal:** Usuario de la aplicación.

**Precondiciones:** El sistema está iniciado y permite acceder a /personajes.

**Disparador:** El usuario abre la ruta /personajes.

**Flujo principal:**

1. El usuario abre GET /personajes; Laravel consulta registros ordenados y paginados, máximo diez por página.
2. La interfaz presenta hasta diez tarjetas con nombre, altura, masa, género, origen y acciones Editar/Eliminar, sin alterar los datos.
3. Para origen_swapi=true y swapi_id válido se intenta mostrar retrato externo por ID; para personaje manual, foto en carga o fallo, se muestra avatar holográfico.
4. En escritorio amplio la galería distribuye hasta diez tarjetas en una fila, con imagen de fondo, degradado para legibilidad y botones apilados; conserva distribución adaptable en anchos inferiores.
5. Sobre las tarjetas se muestran contador, navegación de páginas (si corresponde) e indicador Página n de m.
6. El usuario elige otra página y Laravel/Inertia carga sus registros, manteniendo paginación de diez.

**Flujos alternativos:**

- No hay registros: estado vacío con indicación de crear o importar.
- Hay 1–10 personajes: una página, controles innecesarios ocultos o inactivos, indicador visible.
- Existen menos de diez tarjetas en la última página: conservar tamaño sin estirarlas.
- Retrato no disponible o personaje manual: avatar holográfico funcional.

**Excepciones:**

- Consulta de base de datos fallida: error gestionado sin mostrar datos falsos.
- Star Wars Gallery no responde o imagen 404: no afecta listado ni operaciones; se utiliza avatar de respaldo.

**Postcondiciones:** No se alteran los personajes por consultar la lista.

**Criterios de aceptación:**

1. Con 11 registros se muestran 10 tarjetas en página 1 y 1 en página 2.
2. Cada tarjeta muestra campos de MAU y procedencia correcta, con acciones operables.
3. Desde 1600 px y altura suficiente caben diez tarjetas en una fila; el diseño no sacrifica información en resoluciones menores.
4. La ausencia de imagen externa nunca impide consultar o cambiar de página.
5. Los controles de paginación están en la franja superior y no se altera la consulta del backend.

**Requisitos vinculados en MODULA:** [RF-PER-001](requisitos.md#rf-per-001), [RF-PER-003](requisitos.md#rf-per-003), [RF-PER-004](requisitos.md#rf-per-004), [RF-PER-016](requisitos.md#rf-per-016), [RF-PER-017](requisitos.md#rf-per-017), [RNF-PER-001](requisitos.md#rnf-per-001).

### CU-PER-002

**Nombre:** Crear personaje manual

**Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Objetivo:** Crear un personaje con reglas de validación obligatorias.

**Actor principal:** Usuario de la aplicación.

**Precondiciones:** El usuario está en /personajes.

**Disparador:** El usuario pulsa Crear Nuevo.

**Flujo principal:**

1. Se abre GET /personajes/crear.
2. El usuario completa los campos del formulario.
3. El frontend comprueba los campos requeridos.
4. useForm() envía POST /personajes.
5. El servidor valida y guarda el registro con origen manual.
6. La interfaz confirma el éxito y permite visualizarlo en el listado.

**Flujos alternativos:**

- El usuario pulsa Cancelar: vuelve al listado sin crear nada.
- Se dejan campos opcionales sin completar: se guardan como null cuando corresponda.

**Excepciones:**

- Nombre fuera de 3-100, altura o masa inválida, género fuera de dominio o texto demasiado largo: mostrar error sin guardar.

**Postcondiciones:** Existe un único personaje nuevo con origen_swapi=false y swapi_id=null.

**Criterios de aceptación:**

1. Una entrada válida se almacena.
2. La validación del servidor bloquea las entradas inválidas.

**Requisitos vinculados en MODULA:** [RF-PER-002](requisitos.md#rf-per-002), [RF-PER-005](requisitos.md#rf-per-005), [RF-PER-009](requisitos.md#rf-per-009), [RF-PER-010](requisitos.md#rf-per-010), [RF-PER-011](requisitos.md#rf-per-011), [RF-PER-012](requisitos.md#rf-per-012), [RF-PER-013](requisitos.md#rf-per-013), [RF-PER-014](requisitos.md#rf-per-014).

### CU-PER-003

**Nombre:** Editar personaje

**Estado:** Aprobado · **Revisión vigente y aprobada:** 2.

**Objetivo:** Modificar los datos de un personaje existente sin duplicarlo.

**Actor principal:** Usuario de la aplicación.

**Precondiciones:** Existe el personaje seleccionado.

**Disparador:** El usuario pulsa Editar en la tarjeta de un personaje.

**Flujo principal:**

1. El usuario activa Editar desde la tarjeta de un personaje y accede a GET /personajes/{id}/editar.
2. En escritorio, el retrato completo o el avatar holográfico aparece a la izquierda, y a la derecha el formulario con campos precargados y procedencia.
3. El usuario modifica únicamente campos editables y conserva id, swapi_id y origen_swapi.
4. El usuario envía el formulario mediante Inertia useForm con PUT /personajes/{id}.
5. Laravel valida y actualiza el mismo registro sin crear otro; muestra errores junto a los campos cuando corresponda.
6. Al guardar o cancelar, vuelve al listado y se muestra el resultado; las transiciones visuales opcionales respetan preferencias de movimiento.

**Flujos alternativos:**

- Cancelar regresa sin modificar el registro.
- Personaje manual, imagen 404 o red no disponible: la vista sigue operativa con avatar holográfico.
- En ancho reducido, las dos columnas pueden apilarse sin ocultar campos.

**Excepciones:**

- Validación fallida: no se persisten cambios y los errores se muestran en el formulario.
- Personaje inexistente: error controlado.
- Fallo de Star Wars Gallery: no afecta actualización ni navegación.

**Postcondiciones:** El personaje conserva su id y refleja los datos válidos nuevos.

**Criterios de aceptación:**

1. Edición actualiza el mismo personaje sin duplicarlo.
2. Formulario completo y errores de backend siguen disponibles junto a una imagen opcional.
3. En escritorio hay retrato a la izquierda y formulario a la derecha.
4. El origen y swapi_id se preservan al editar.
5. Si no se carga fotografía, puede editarse igualmente.

**Requisitos vinculados en MODULA:** [RF-PER-006](requisitos.md#rf-per-006), [RF-PER-007](requisitos.md#rf-per-007), [RF-PER-009](requisitos.md#rf-per-009), [RF-PER-010](requisitos.md#rf-per-010), [RF-PER-011](requisitos.md#rf-per-011), [RF-PER-012](requisitos.md#rf-per-012), [RF-PER-013](requisitos.md#rf-per-013), [RF-PER-014](requisitos.md#rf-per-014), [RF-PER-017](requisitos.md#rf-per-017), [RNF-PER-001](requisitos.md#rnf-per-001).

### CU-PER-004

**Nombre:** Eliminar personaje

**Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Objetivo:** Eliminar un personaje únicamente después de confirmar la acción.

**Actor principal:** Usuario de la aplicación.

**Precondiciones:** Existe un personaje en el listado.

**Disparador:** El usuario pulsa Eliminar en una fila.

**Flujo principal:**

1. Se abre un diálogo de confirmación.
2. El usuario confirma la eliminación.
3. Se envía DELETE /personajes/{id}.
4. El servidor elimina el registro indicado.
5. La lista se actualiza y muestra confirmación.

**Flujos alternativos:**

- El usuario cancela la confirmación: no se envía DELETE.

**Excepciones:**

- El personaje no existe o falla la eliminación: mostrar error controlado.

**Postcondiciones:** El personaje ya no aparece en la base de datos ni en el listado.

**Criterios de aceptación:**

1. Cancelar conserva el personaje.
2. Confirmar borra solo el personaje indicado.

**Requisitos vinculados en MODULA:** [RF-PER-003](requisitos.md#rf-per-003), [RF-PER-008](requisitos.md#rf-per-008).
