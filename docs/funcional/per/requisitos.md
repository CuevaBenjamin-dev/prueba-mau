# Requisitos — PER · Gestión de Personajes

[Índice funcional](../README.md) · [Análisis funcional PER](analisis-funcional.md)

Fuente: exportación MODULA revisión global 117. Todos los elementos siguientes están aprobados y muestran su revisión vigente. PER comparte la persistencia con SWA, pero no realiza consultas HTTP a SWAPI.

## MVP obligatorio de MAU

### RF-PER-001

**Nombre:** Modelo de datos de personajes

**Tipo:** Funcional (`functional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** Persistir personajes en MySQL, en tabla personajes, con id PK; nombre string obligatorio; altura integer nullable en cm; masa integer nullable en kg; color_cabello, color_ojos y planeta_origen strings nullable; genero obligatorio; origen_swapi boolean default false; swapi_id integer nullable unique; created_at y updated_at.

**Justificación registrada:** PDF pp. 1-2: BASE DE DATOS.

**Criterios de aceptación:**

1. La migración crea la tabla personajes con los doce campos solicitados.
2. nombre es obligatorio; genero acepta únicamente masculino, femenino o n/a.
3. swapi_id permite null y no admite repetir un valor no nulo.
4. created_at y updated_at se registran y actualizan según la operación.

**Casos de uso vinculados en MODULA:** [CU-PER-001](analisis-funcional.md#cu-per-001), [CU-SWA-001](../swa/analisis-funcional.md#cu-swa-001).

### RF-PER-002

**Nombre:** Crear personaje manualmente

**Tipo:** Funcional (`functional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** Permitir registrar un personaje sin consultar SWAPI desde POST /personajes; validar en el servidor los campos y persistir el nuevo registro.

**Justificación registrada:** PDF pp. 2-3: CRUD COMPLETO, crear personaje.

**Criterios de aceptación:**

1. Un formulario válido crea exactamente un registro y permite consultarlo en /personajes.
2. El registro manual usa origen_swapi=false y swapi_id=null.
3. Una solicitud inválida no genera un personaje.

**Casos de uso vinculados en MODULA:** [CU-PER-002](analisis-funcional.md#cu-per-002).

### RF-PER-003

**Nombre:** Listar personajes

**Tipo:** Funcional (`functional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** GET /personajes muestra personajes persistidos, tanto manuales como de SWAPI, con nombre, altura, masa, genero y origen visible Manual/SWAPI.

**Justificación registrada:** PDF pp. 2-3: GET /personajes, Página Lista de Personajes.

**Criterios de aceptación:**

1. Cada fila o tarjeta presenta nombre, altura, masa, género y origen.
2. Un personaje importado se distingue de uno manual.
3. La página sigue funcionando sin personajes y muestra un estado vacío comprensible.

**Casos de uso vinculados en MODULA:** [CU-PER-001](analisis-funcional.md#cu-per-001), [CU-PER-004](analisis-funcional.md#cu-per-004).

### RF-PER-004

**Nombre:** Paginar de diez en diez

**Tipo:** Funcional (`functional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** Mostrar 10 personajes como máximo por página y controles de paginación funcionales en /personajes.

**Justificación registrada:** PDF pp. 2-3: paginación de 10 por página.

**Criterios de aceptación:**

1. Con 11 personajes, la página 1 presenta 10 y la página 2 presenta 1.
2. Los controles permiten navegar entre páginas conservando la lista coherente.
3. Una página válida sin resultados no provoca error.

**Casos de uso vinculados en MODULA:** [CU-PER-001](analisis-funcional.md#cu-per-001).

### RF-PER-005

**Nombre:** Acceder al formulario de creación

**Tipo:** Funcional (`functional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** GET /personajes/crear muestra un formulario con todos los campos de personaje y botones Guardar y Cancelar.

**Justificación registrada:** PDF pp. 2-3: formulario de creación.

**Criterios de aceptación:**

1. El botón Crear Nuevo de la lista abre /personajes/crear.
2. El formulario permite editar nombre, altura, masa, color_cabello, color_ojos, genero y planeta_origen; los metadatos de origen e identificadores se muestran como informativos cuando corresponda.
3. Cancelar regresa a la lista sin crear un registro.

**Casos de uso vinculados en MODULA:** [CU-PER-002](analisis-funcional.md#cu-per-002).

### RF-PER-006

**Nombre:** Acceder al formulario de edición

**Tipo:** Funcional (`functional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** GET /personajes/{id}/editar presenta la información actual del personaje seleccionado en un formulario precargado.

**Justificación registrada:** PDF pp. 2-3: formulario de edición.

**Criterios de aceptación:**

1. Editar abre el formulario del personaje correcto.
2. Los campos aparecen con los valores persistidos del personaje.
3. Cancelar no modifica los datos.

**Casos de uso vinculados en MODULA:** [CU-PER-003](analisis-funcional.md#cu-per-003).

### RF-PER-007

**Nombre:** Actualizar personaje

**Tipo:** Funcional (`functional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** PUT /personajes/{id} valida y actualiza un personaje existente sin crear otro registro; ofrece feedback del resultado.

**Justificación registrada:** PDF p. 2: PUT /personajes/{id}.

**Criterios de aceptación:**

1. Editar y enviar datos válidos actualiza el personaje y conserva su id, origen_swapi y swapi_id.
2. Errores de validación impiden guardar cambios inválidos.
3. Si no existe el personaje, la aplicación muestra una respuesta de error controlada.

**Casos de uso vinculados en MODULA:** [CU-PER-003](analisis-funcional.md#cu-per-003).

### RF-PER-008

**Nombre:** Eliminar personaje con confirmación

**Tipo:** Funcional (`functional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** DELETE /personajes/{id} elimina el personaje indicado solamente después de una confirmación visible al usuario.

**Justificación registrada:** PDF pp. 2-3: DELETE y confirmación de eliminación.

**Criterios de aceptación:**

1. Eliminar solicita confirmación.
2. Cancelar la confirmación conserva el personaje.
3. Confirmar elimina el registro y refresca la lista con mensaje visible.

**Casos de uso vinculados en MODULA:** [CU-PER-004](analisis-funcional.md#cu-per-004).

### RF-PER-009

**Nombre:** Validar nombre

**Tipo:** Funcional (`functional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** Exigir nombre como texto con longitud entre 3 y 100 caracteres, en backend y validación frontend básica.

**Justificación registrada:** PDF p. 2: reglas de validación del nombre.

**Criterios de aceptación:**

1. Nombre vacío, de 1 o 2 caracteres, o de más de 100 se rechaza.
2. Nombre de 3 o de 100 caracteres se acepta si el resto es válido.
3. Se muestra un mensaje de validación comprensible.

**Casos de uso vinculados en MODULA:** [CU-PER-002](analisis-funcional.md#cu-per-002), [CU-PER-003](analisis-funcional.md#cu-per-003).

### RF-PER-010

**Nombre:** Validar altura

**Tipo:** Funcional (`functional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** Aceptar altura vacía o un entero positivo en centímetros de máximo 300.

**Justificación registrada:** PDF p. 2: reglas de validación de altura.

**Criterios de aceptación:**

1. Altura null o ausente es válida.
2. Alturas 1 y 300 son válidas.
3. Altura 0, negativa, decimal, no numérica o mayor a 300 se rechaza.

**Casos de uso vinculados en MODULA:** [CU-PER-002](analisis-funcional.md#cu-per-002), [CU-PER-003](analisis-funcional.md#cu-per-003).

### RF-PER-011

**Nombre:** Validar masa

**Tipo:** Funcional (`functional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** Aceptar masa vacía o un entero positivo en kilogramos de máximo 500.

**Justificación registrada:** PDF p. 2: reglas de validación de masa.

**Criterios de aceptación:**

1. Masa null o ausente es válida.
2. Masas 1 y 500 son válidas.
3. Masa 0, negativa, decimal, no numérica o mayor a 500 se rechaza.

**Casos de uso vinculados en MODULA:** [CU-PER-002](analisis-funcional.md#cu-per-002), [CU-PER-003](analisis-funcional.md#cu-per-003).

### RF-PER-012

**Nombre:** Validar género

**Tipo:** Funcional (`functional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** Exigir genero con uno de tres valores exactos: masculino, femenino o n/a.

**Justificación registrada:** PDF p. 2: reglas de validación del género.

**Criterios de aceptación:**

1. Los tres valores enumerados son aceptados.
2. Valor vacío u otro valor se rechaza.
3. El control frontend presenta las opciones admitidas.

**Casos de uso vinculados en MODULA:** [CU-PER-002](analisis-funcional.md#cu-per-002), [CU-PER-003](analisis-funcional.md#cu-per-003).

### RF-PER-013

**Nombre:** Validar campos de texto opcionales

**Tipo:** Funcional (`functional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** color_cabello, color_ojos y planeta_origen permiten null; cuando se ingresan, no exceden 100 caracteres.

**Justificación registrada:** PDF pp. 1-2: definición de campos y límite de textos.

**Criterios de aceptación:**

1. Los tres campos admiten estar vacíos.
2. Valores de hasta 100 caracteres se aceptan.
3. Valores de 101 o más caracteres se rechazan en backend con mensaje de error.

**Casos de uso vinculados en MODULA:** [CU-PER-002](analisis-funcional.md#cu-per-002), [CU-PER-003](analisis-funcional.md#cu-per-003).

### RF-PER-014

**Nombre:** Presentar errores de formulario

**Tipo:** Funcional (`functional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** En crear y editar, realizar comprobación frontend de campos obligatorios y mostrar las validaciones del backend junto al formulario.

**Justificación registrada:** PDF p. 3: validación frontend y errores backend.

**Criterios de aceptación:**

1. Guardar y Actualizar muestran errores del servidor sin perder los datos válidos introducidos.
2. Se identifican los campos obligatorios faltantes.
3. El usuario puede corregir y volver a enviar.

**Casos de uso vinculados en MODULA:** [CU-PER-002](analisis-funcional.md#cu-per-002), [CU-PER-003](analisis-funcional.md#cu-per-003).

### RF-PER-015

**Nombre:** Rutas CRUD explícitas

**Tipo:** Funcional (`functional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** Exponer las seis rutas indicadas usando Laravel e Inertia: GET /personajes, GET /personajes/crear, POST /personajes, GET /personajes/{id}/editar, PUT /personajes/{id}, DELETE /personajes/{id}.

**Justificación registrada:** PDF p. 2: contrato de rutas.

**Criterios de aceptación:**

1. Las seis rutas responden con el verbo y operación esperados.
2. Las rutas de pantallas emplean Inertia.
3. Solicitudes inválidas no alteran datos.

**Casos de uso vinculados en MODULA:** ninguno de forma directa.

## Mejoras visuales adicionales aprobadas

Los requisitos siguientes amplían la presentación después del MVP. Su aprobación en MODULA no los convierte en exigencias originales del PDF MAU.

### RF-PER-016

**Nombre:** Galería visual de personajes y navegación superior

**Tipo:** Funcional (`functional`) · **Prioridad:** Media (`medium`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** Presentar en GET /personajes los registros existentes mediante tarjetas con nombre, altura, masa, género, origen Manual/SWAPI y acciones Editar y Eliminar apiladas. Conservar la consulta Laravel paginada en exactamente diez registros por página, el estado vacío y los controles de navegación. En escritorio amplio (desde aproximadamente 1600 px) distribuir hasta diez tarjetas en una fila, usando altura disponible del viewport cuando sea viable sin ocultar información. Ubicar contador, controles Anterior/números/Siguiente e indicador Página n de m en una misma franja superior. Con menos de diez registros no estirar artificialmente las tarjetas.

**Justificación registrada:** Mejora opcional de presentación, acordada después del MVP MAU; código verificado en PersonajeTable.vue, PersonajeCard.vue, Index.vue y reglas CSS de la rama feat/galeria-galactica, commit 0d3f8f4.

**Criterios de aceptación:**

1. Con 11 personajes, la primera página contiene 10 tarjetas y la segunda 1; no cambia paginate(10).
2. Cada tarjeta muestra nombre, altura, masa, género, procedencia y Editar/Eliminar funcionales.
3. Desde 1600 px de ancho, diez personajes pueden formar una única fila; por debajo el diseño adapta columnas y permite scroll cuando sea necesario.
4. Con una página se muestra el indicador sin enlaces de paginación inútiles; con cero personajes se presenta un estado vacío comprensible.
5. La confirmación de eliminación y los formularios siguen siendo los existentes.

**Excepciones o restricciones:**

- La restricción de una fila y de no desplazamiento aplica solo a escritorio con espacio suficiente; nunca se recortan datos por forzar viewport.
- La paginación depende de MySQL/Laravel; no dividir manualmente el total en el cliente.

**Casos de uso vinculados en MODULA:** [CU-PER-001](analisis-funcional.md#cu-per-001).

### RF-PER-017

**Nombre:** Retrato remoto opcional y avatar de respaldo

**Tipo:** Funcional (`functional`) · **Prioridad:** Media (`medium`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** En las tarjetas y en la edición, mostrar una fotografía complementaria a los datos del personaje, obtenida únicamente desde Star Wars Gallery a partir de swapi_id existente: https://starwarsgallery.netlify.app/assets/people/{id}.jpg. Construir URL solo si origen_swapi=true y swapi_id es entero válido 1..83. No asociar por nombre ni por id local. Usar retrato holográfico de respaldo para registros manuales, durante la carga y si la imagen falla o no existe; mantener acciones, campos y carga de datos funcionales. La consulta SWAPI original sigue siendo la única fuente de datos persistidos; las imágenes no se guardan en MySQL, no se agregan columnas, no condicionan importación ni CRUD.

**Justificación registrada:** Ampliación de galería de personajes implementada con characterImages.js y PersonajeAvatar.vue; no forma parte de las exigencias obligatorias del PDF MAU.

**Criterios de aceptación:**

1. ID SWAPI=5, origen_swapi=true: URL terminada en /people/5.jpg; no se emplea id local.
2. Personaje manual con swapi_id=null: se presenta avatar holográfico sin URL externa.
3. Imagen 404, bloqueada o inalcanzable: aparece avatar de respaldo sin fallar la tarjeta, el formulario de edición ni la importación.
4. La pantalla de edición muestra imagen completa a la izquierda y formulario sin alteraciones funcionales a la derecha en escritorio.
5. La caída de Star Wars Gallery no cambia ni elimina registros ni altera la consulta de SWAPI.

**Excepciones o restricciones:**

- Servicio externo de imágenes sin disponibilidad garantizada; puede faltar cualquier ID.
- Derechos de imágenes de Star Wars sujetos a sus titulares; documentar uso educativo/portafolio sin asumir dominio público ni licencia comercial.
- No descargar fotografías externas al repositorio ni hacer dependiente de ellas el backend.

**Casos de uso vinculados en MODULA:** [CU-PER-001](analisis-funcional.md#cu-per-001), [CU-PER-003](analisis-funcional.md#cu-per-003).

### RNF-PER-001

**Nombre:** Identidad cinematográfica, legibilidad y animaciones sobrias

**Tipo:** No funcional (`nonfunctional`) · **Prioridad:** Media (`medium`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** Aplicar identidad de interfaz de ciencia ficción cinematográfica al encabezado, listado, formularios y modal: superficies negro carbón (#111112, #1b1b1e, #222225), texto cálido (#f1eee8), acento oro envejecido (#c4a46a, #e2c48a) y azul metálico secundario (#789baa). Usar Barlow Condensed para títulos y Manrope para texto funcional, empaquetadas localmente con licencias de tipografía apropiadas. Situar «Archivo Galáctico» a la izquierda y «PERSONAJES STAR WARS» centrado en el encabezado, eliminar el borde exterior y adornos flotantes. Mostrar fotografía como fondo de tarjeta con degradado oscuro progresivo que permite leer datos y acciones. Conservar la composición de edición (retrato grande izquierda, formulario derecha), controles comprensibles, contraste, estados de error/éxito y transiciones discretas; respetar prefers-reduced-motion. El diseño de escritorio se valida principalmente a 1600x900 y 1920x1080, sin declarar terminada la revisión manual específica de móvil.

**Justificación registrada:** Diseño adicional posterior al MVP MAU, consolidado en commit 0d3f8f4 de feat/galeria-galactica, README y estilos app.css; la obligación de responsividad de MAU se conserva por separado en RNF-MAU-004.

**Criterios de aceptación:**

1. Las pantallas principales, edición, creación y modal comparten paleta y tipografías locales.
2. En escritorio amplio se conserva la legibilidad de cada tarjeta, el degradado fotográfico y los botones; el marco general exterior no reaparece.
3. Los estados de foco, hover, éxito y error siguen perceptibles y las acciones mantienen su significado.
4. Cuando el sistema solicita reducción de movimiento, las transiciones opcionales quedan reducidas o desactivadas.
5. Las imágenes remotas no son requisito para comprender o accionar la aplicación.

**Excepciones o restricciones:**

- No usar marcas, logotipos o material gráfico oficiales adicionales sin autorización.
- No eliminar ni sustituir RNF-MAU-004; la revisión manual exhaustiva de tablet/móvil no se ha declarado aprobada.
- No usar la estética como justificación para ocultar información, validaciones o botones.

**Casos de uso vinculados en MODULA:** [CU-PER-001](analisis-funcional.md#cu-per-001), [CU-PER-003](analisis-funcional.md#cu-per-003).
