# Requisitos no funcionales transversales — MAU

[Índice funcional](README.md) · [Requisitos PER](per/requisitos.md) · [Requisitos SWA](swa/requisitos.md)

Los once `RNF-MAU` tienen alcance global en MODULA y se relacionan con ambas áreas funcionales, PER y SWA. Se documentan una sola vez aquí; no son propiedad exclusiva de ningún módulo. La fecha, revisión, estado, descripción y criterios proceden de los elementos vigentes aprobados.

## Stack, frontend y reutilización

### RNF-MAU-001

**Nombre:** Tecnologías obligatorias

**Tipo:** No funcional (`nonfunctional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** Implementar el proyecto con PHP 8.1+ y Laravel 10+; Vue 3 Composition API; Inertia.js; MySQL; Tailwind CSS; Vite.

**Justificación registrada:** PDF p. 1: stack obligatorio.

**Criterios de aceptación:**

1. El repositorio utiliza el stack obligatorio y se ejecuta localmente.
2. No se reemplaza MySQL por SQLite ni Inertia por una SPA con API separada.

### RNF-MAU-002

**Nombre:** Convenciones obligatorias del frontend

**Tipo:** No funcional (`nonfunctional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** Usar &lt;script setup&gt; de Vue 3 y useForm() de Inertia en los formularios, evitando duplicar lógica de envío y errores.

**Justificación registrada:** PDF p. 4: requisitos frontend.

**Criterios de aceptación:**

1. Los formularios están implementados con useForm() de Inertia.
2. Los componentes Vue usan &lt;script setup&gt;.

### RNF-MAU-003

**Nombre:** Componentes Vue reutilizables

**Tipo:** No funcional (`nonfunctional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** Construir al menos dos componentes Vue reutilizables. Se proponen PersonajeForm.vue, PersonajeTable.vue e ImportModal.vue.

**Justificación registrada:** PDF p. 4: mínimo dos componentes reutilizables.

**Criterios de aceptación:**

1. Se identifican dos o más componentes Vue reutilizados o diseñados con props y responsabilidades claras.
2. El formulario de creación y edición comparte PersonajeForm.vue.

## Diseño adaptable y retroalimentación

### RNF-MAU-004

**Nombre:** Diseño adaptable a móvil

**Tipo:** No funcional (`nonfunctional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** Las pantallas y el modal funcionan con anchos reducidos manteniendo controles visibles y operables.

**Justificación registrada:** PDF pp. 4 y 8: responsive obligatorio.

**Criterios de aceptación:**

1. La lista, los formularios y el modal son usables en móvil y escritorio.
2. Los botones y mensajes no quedan fuera del viewport.

### RNF-MAU-005

**Nombre:** Mensajes y procesos visibles

**Tipo:** No funcional (`nonfunctional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** Mostrar indicadores de carga y mensajes de éxito o error en operaciones de creación, edición, eliminación e importación.

**Justificación registrada:** PDF p. 4: loading states y mensajes visibles.

**Criterios de aceptación:**

1. Cada operación de escritura comunica su resultado.
2. Las acciones asíncronas visibles disponen de indicador de carga.

## Backend y errores

### RNF-MAU-006

**Nombre:** Organización de backend y errores

**Tipo:** No funcional (`nonfunctional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** El backend aplica validaciones, manejo de errores, código legible y nombres de variables y métodos descriptivos, sin comentarios innecesarios.

**Justificación registrada:** PDF pp. 4 y 8: calidad del código y entrevista.

**Criterios de aceptación:**

1. Las validaciones críticas se realizan en el servidor.
2. No quedan errores no controlados en flujos esperados como ID inválido y 404.
3. Controllers y clases son comprensibles para explicar su funcionamiento durante la entrevista.

## Repositorio, configuración y documentación

### RNF-MAU-007

**Nombre:** Repositorio y archivos de configuración

**Tipo:** No funcional (`nonfunctional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** Entregar repositorio público GitHub/GitLab con commits descriptivos, .gitignore correcto y .env.example sin secretos.

**Justificación registrada:** PDF pp. 4-5: requisitos generales y repositorio.

**Criterios de aceptación:**

1. El repositorio es público y accesible.
2. No se versionan node_modules, vendor ni .env.
3. Los commits describen pasos del desarrollo y existe .env.example.

### RNF-MAU-008

**Nombre:** README de instalación reproducible

**Tipo:** No funcional (`nonfunctional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** El README incluye información de candidato, tecnologías, requisitos de sistema, instalación, configuración MySQL, migraciones, compilación, arranque y URL /personajes.

**Justificación registrada:** PDF pp. 5-6: README obligatorio.

**Criterios de aceptación:**

1. README informa requisitos PHP/Composer/Node/MySQL y pasos: clone, composer install, npm install, .env, key:generate, migrate, npm run build, artisan serve.
2. Un evaluador puede seguir instrucciones para ejecutar localmente.
3. Las funcionalidades implementadas y las opcionales quedan diferenciadas.

### RNF-MAU-009

**Nombre:** Respuestas técnicas y declaración de IA

**Tipo:** No funcional (`nonfunctional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** El README incluye las nueve respuestas sobre organización backend, componentes Vue, errores SWAPI, validaciones, mayor dificultad, uso de IA, mejoras futuras, autoevaluación y tiempo invertido.

**Justificación registrada:** PDF p. 7: preguntas técnicas y p. 8: honestidad.

**Criterios de aceptación:**

1. El README contiene nueve respuestas específicas y honestas.
2. Se explica cómo se validó el código generado o asistido por IA.
3. Se registra el tiempo de trabajo real y no uno inventado.

## Restricciones y entrega formal

### RNF-MAU-010

**Nombre:** Restricciones de implementación y entrega

**Tipo:** No funcional (`nonfunctional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** Desarrollar desde cero con laravel new, sin Breeze/Jetstream; no exigir login ni despliegue; entregar proyecto que funcione localmente.

**Justificación registrada:** PDF pp. 7 y 9: restricciones y preguntas frecuentes.

**Criterios de aceptación:**

1. No se usa starter kit prohibido ni se copia un proyecto completo existente.
2. La app no exige autenticación.
3. La aplicación corre en local; no necesita URL pública de producción.

### RNF-MAU-011

**Nombre:** Entrega según plazo y formato

**Tipo:** No funcional (`nonfunctional`) · **Prioridad:** Alta (`high`) · **Estado:** Aprobado · **Revisión vigente y aprobada:** 1.

**Descripción aprobada:** Remitir la solución con enlace al repositorio y datos solicitados al correo indicado en el PDF antes de las 09:00 del 19/11/2026.

**Justificación registrada:** PDF pp. 1 y 8-9: fecha y modo de entrega.

**Criterios de aceptación:**

1. Se prepara el correo con asunto Prueba Técnica - [nombre completo].
2. Se incluye repositorio, usuario de GitHub/GitLab, horas reales y comentarios pertinentes.
3. La entrega respeta el plazo establecido.
