# Documentación funcional aprobada

**Proyecto:** Prueba Técnica MAU — Gestión de Personajes Star Wars (`83195dda-d8ca-4e44-9c0f-84e5cbf79a07`).

**Exportación MODULA:** 2026-11-16T03:36:10.010Z (UTC) · revisión global **117**.

## Alcance y origen

Desarrollar una aplicación web Full Stack para gestionar personajes de Star Wars mediante un CRUD completo y la importación desde SWAPI, cumpliendo los requisitos funcionales, técnicos y de entrega definidos en la prueba técnica de MAU by Contatech.

El PDF de MAU establece las obligaciones de la prueba. La especificación vigente fue definida y aprobada en MODULA; el método CLARO ayudó a estructurar y trasladar decisiones para su revisión. La aplicación implementada es un solo proyecto Laravel con dos áreas funcionales: **PER** (gestión, persistencia, validación y vistas) y **SWA** (consulta y transformación SWAPI). Ambas comparten `Personaje` y la tabla `personajes`; no son microservicios ni módulos físicos independientes.

Esta exportación contiene **38 requisitos vigentes aprobados** y **5 casos de uso vigentes aprobados**. En todos coincide la revisión aprobada con la revisión vigente. Los once `RNF-MAU` aplican al conjunto de la aplicación. Las mejoras voluntarias aprobadas de galería y fotografías aparecen como `RF-PER-016`, `RF-PER-017` y `RNF-PER-001`; los refinamientos SWAPI de revisión 3 incluyen la consulta opcional del planeta y la compatibilidad con `gender=none`. Esas mejoras no convierten a Star Wars Gallery en fuente de datos del personaje ni cambian las obligaciones originales de MAU.

Los textos de descripción, justificación, criterios, excepciones y flujos proceden de los elementos vigentes `requirements` y `use_cases`; no se extrajeron de `revisions`. Este índice documenta **qué** debe hacer el sistema; los análisis funcionales describen **cómo interactúa** el usuario; [arquitectura](../ARQUITECTURA.md) y [diseño técnico](../DISENO_TECNICO.md) explican **cómo está construido**. Los resultados de pruebas y ejecución se consultan en el [README principal](../../README.md), no se infieren de los criterios de aceptación.

## Documentos

| Documento | Contenido |
|---|---|
| [Requisitos PER](per/requisitos.md) | 17 requisitos funcionales y 1 no funcional del área de gestión. |
| [Análisis funcional PER](per/analisis-funcional.md) | Cuatro casos de uso de consulta, creación, edición y eliminación. |
| [Requisitos SWA](swa/requisitos.md) | Nueve requisitos funcionales de importación SWAPI. |
| [Análisis funcional SWA](swa/analisis-funcional.md) | Caso de uso de importación y sus flujos alternativos. |
| [Requisitos transversales](requisitos-transversales.md) | Once requisitos no funcionales que aplican a PER y SWA. |
| [Arquitectura técnica](../ARQUITECTURA.md) · [Diseño técnico](../DISENO_TECNICO.md) | Construcción y decisiones de implementación. |

## Trazabilidad registrada

MODULA registra **36 vínculos** entre casos de uso y requisitos, y **11 vínculos** entre requisitos. Cada requisito de PER/SWA muestra sus casos de uso vinculados; cada caso enumera sus requisitos relacionados. `RF-PER-015` no tiene vínculo directo con un caso de uso: su contrato de seis rutas CRUD sigue vigente. Los once requisitos `RNF-MAU` son globales y no se asignan artificialmente a un caso de uso concreto.

**Relaciones entre requisitos de la exportación vigente** (`relates`, sin inferir dependencias adicionales):

| Origen | Relación | Destino |
|---|---|---|
| [RF-SWA-006](swa/requisitos.md#rf-swa-006) | `relates` | [RF-PER-001](per/requisitos.md#rf-per-001) |
| [RF-PER-016](per/requisitos.md#rf-per-016) | `relates` | [RF-PER-004](per/requisitos.md#rf-per-004) |
| [RF-PER-016](per/requisitos.md#rf-per-016) | `relates` | [RF-PER-003](per/requisitos.md#rf-per-003) |
| [RF-PER-016](per/requisitos.md#rf-per-016) | `relates` | [RF-PER-008](per/requisitos.md#rf-per-008) |
| [RF-PER-017](per/requisitos.md#rf-per-017) | `relates` | [RF-SWA-005](swa/requisitos.md#rf-swa-005) |
| [RF-PER-017](per/requisitos.md#rf-per-017) | `relates` | [RF-PER-003](per/requisitos.md#rf-per-003) |
| [RF-PER-017](per/requisitos.md#rf-per-017) | `relates` | [RF-PER-006](per/requisitos.md#rf-per-006) |
| [RNF-PER-001](per/requisitos.md#rnf-per-001) | `relates` | [RF-PER-016](per/requisitos.md#rf-per-016) |
| [RNF-PER-001](per/requisitos.md#rnf-per-001) | `relates` | [RNF-MAU-004](requisitos-transversales.md#rnf-mau-004) |
| [RNF-PER-001](per/requisitos.md#rnf-per-001) | `relates` | [RNF-MAU-003](requisitos-transversales.md#rnf-mau-003) |
| [RNF-PER-001](per/requisitos.md#rnf-per-001) | `relates` | [RF-PER-017](per/requisitos.md#rf-per-017) |

**Alcance de la evidencia:** aprobación en MODULA significa especificación acordada; no equivale por sí sola a que una prueba de ejecución haya pasado. El README principal conserva los resultados verificados. El JSON bruto y sus snapshots históricos no forman parte de esta entrega.
