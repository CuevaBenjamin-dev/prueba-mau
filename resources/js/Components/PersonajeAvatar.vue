<script setup>
import { computed, ref, watch } from 'vue';
import { characterImageUrl } from '../lib/characterImages';

const props = defineProps({ personaje: { type: Object, required: true } });
const source = computed(() => characterImageUrl(props.personaje));
const failed = ref(false);
const showImage = computed(() => Boolean(source.value) && !failed.value);

watch(source, () => { failed.value = false; });
</script>

<template>
  <div class="holo-portrait" :role="showImage ? undefined : 'img'" :aria-label="showImage ? undefined : `Avatar holográfico de ${personaje.nombre}`">
    <div class="holo-avatar" aria-hidden="true">
      <div class="holo-avatar__halo"></div>
      <div class="holo-avatar__head"></div>
      <div class="holo-avatar__shoulders"></div>
      <span class="holo-avatar__mark">✦</span>
    </div>
    <img
      v-if="showImage"
      :src="source"
      :alt="`Retrato de ${personaje.nombre}`"
      class="holo-portrait__image"
      loading="lazy"
      decoding="async"
      width="400"
      height="550"
      referrerpolicy="no-referrer"
      @error="failed = true"
    />
    <div class="holo-portrait__scrim" aria-hidden="true"></div>
    <span class="holo-portrait__index" aria-hidden="true">{{ personaje.origen_swapi ? `SW-${String(personaje.swapi_id ?? '').padStart(2, '0')}` : 'LOCAL' }}</span>
  </div>
</template>
