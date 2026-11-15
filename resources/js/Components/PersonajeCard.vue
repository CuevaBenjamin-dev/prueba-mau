<script setup>
import { Link } from '@inertiajs/vue3';
import PersonajeAvatar from './PersonajeAvatar.vue';

const props = defineProps({
  personaje: { type: Object, required: true },
  deletingId: { type: Number, default: null },
});
const emit = defineEmits(['delete']);

function displayMeasure(value, unit) {
  return value == null ? '—' : `${value} ${unit}`;
}
</script>

<template>
  <article class="holo-card" :class="personaje.origen_swapi ? 'holo-card--swapi' : 'holo-card--manual'">
    <PersonajeAvatar :personaje="personaje" />
    <div class="holo-card__body">
      <div class="flex min-w-0 items-start justify-between gap-2">
        <h2 class="holo-card__name" :title="personaje.nombre">{{ personaje.nombre }}</h2>
        <span class="holo-card__origin" :class="personaje.origen_swapi ? 'holo-card__origin--swapi' : 'holo-card__origin--manual'">{{ personaje.origen_swapi ? 'SWAPI' : 'Manual' }}</span>
      </div>
      <dl class="holo-card__facts">
        <div><dt>Altura</dt><dd>{{ displayMeasure(personaje.altura, 'cm') }}</dd></div>
        <div><dt>Masa</dt><dd>{{ displayMeasure(personaje.masa, 'kg') }}</dd></div>
        <div><dt>Género</dt><dd class="capitalize">{{ personaje.genero ?? '—' }}</dd></div>
      </dl>
      <div class="holo-card__actions">
        <Link :href="`/personajes/${personaje.id}/editar`" class="holo-action holo-action--edit">Editar</Link>
        <button type="button" :disabled="deletingId === personaje.id" class="holo-action holo-action--delete" :aria-label="`Eliminar a ${personaje.nombre}`" @click="emit('delete', personaje)">{{ deletingId === personaje.id ? 'Eliminando…' : 'Eliminar' }}</button>
      </div>
    </div>
  </article>
</template>
