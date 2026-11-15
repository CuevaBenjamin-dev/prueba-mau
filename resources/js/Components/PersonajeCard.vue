<script setup>
import { computed, onMounted, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import PersonajeAvatar from './PersonajeAvatar.vue';
import { canAnimatePersonajeTransition, editOriginUrl, returningPersonajeId } from '../personajeTransition';

const props = defineProps({
  personaje: { type: Object, required: true },
  deletingId: { type: Number, default: null },
});
const emit = defineEmits(['delete']);
const animateEdit = ref(false);
const editing = ref(false);
const returning = computed(() => returningPersonajeId.value === props.personaje.id);

onMounted(() => {
  animateEdit.value = canAnimatePersonajeTransition();
});

function prepareEdit() {
  if (animateEdit.value) {
    editOriginUrl.value = window.location.pathname + window.location.search;
    editing.value = true;
  }
}

function finishEdit() {
  editing.value = false;
}

function displayMeasure(value, unit) {
  return value == null ? '—' : `${value} ${unit}`;
}
</script>

<template>
  <article class="holo-card" :class="[personaje.origen_swapi ? 'holo-card--swapi' : 'holo-card--manual', { 'holo-card--editing': editing || returning }]">
    <PersonajeAvatar :personaje="personaje" />
    <div class="holo-card__body">
      <div class="holo-card__identity">
        <h2 class="holo-card__name" :title="personaje.nombre">{{ personaje.nombre }}</h2>
        <span class="holo-card__origin" :class="personaje.origen_swapi ? 'holo-card__origin--swapi' : 'holo-card__origin--manual'">{{ personaje.origen_swapi ? 'SWAPI' : 'Manual' }}</span>
      </div>
      <dl class="holo-card__facts">
        <div><dt>Altura</dt><dd>{{ displayMeasure(personaje.altura, 'cm') }}</dd></div>
        <div><dt>Masa</dt><dd>{{ displayMeasure(personaje.masa, 'kg') }}</dd></div>
        <div><dt>Género</dt><dd class="capitalize">{{ personaje.genero ?? '—' }}</dd></div>
      </dl>
      <div class="holo-card__actions">
        <Link :href="`/personajes/${personaje.id}/editar`" class="holo-action holo-action--edit" :view-transition="animateEdit" :on-before="prepareEdit" :on-finish="finishEdit">Editar</Link>
        <button type="button" :disabled="deletingId === personaje.id" class="holo-action holo-action--delete" :aria-label="`Eliminar a ${personaje.nombre}`" @click="emit('delete', personaje)">{{ deletingId === personaje.id ? 'Eliminando…' : 'Eliminar' }}</button>
      </div>
    </div>
  </article>
</template>
