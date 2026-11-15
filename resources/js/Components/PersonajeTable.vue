<script setup>
import PersonajeCard from './PersonajeCard.vue';

defineProps({ personajes: { type: Array, required: true }, deletingId: { type: Number, default: null } });
const emit = defineEmits(['delete']);
</script>

<template>
  <div v-if="personajes.length === 0" class="galaxy-empty" role="status">
    <span class="galaxy-empty__symbol" aria-hidden="true">✦</span>
    <h2 class="text-xl font-bold text-slate-100">Todavía no hay personajes</h2>
    <p class="mt-2 text-sm text-slate-300">Crea uno manualmente o importa un personaje desde SWAPI.</p>
  </div>
  <div v-else class="character-grid" aria-label="Galería de personajes">
    <PersonajeCard v-for="personaje in personajes" :key="personaje.id" :personaje="personaje" :deleting-id="deletingId" @delete="emit('delete', $event)" />
  </div>
</template>
