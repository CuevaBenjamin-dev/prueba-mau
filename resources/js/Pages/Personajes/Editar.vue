<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '../../Components/AppLayout.vue';
import PersonajeForm from '../../Components/PersonajeForm.vue';

defineProps({ personaje: { type: Object, required: true } });
</script>

<template>
  <Head :title="`Editar ${personaje.nombre}`" />
  <AppLayout>
    <div class="mx-auto max-w-3xl">
      <Link href="/personajes" class="text-sm font-semibold text-sky-300 hover:text-white hover:underline">← Volver a personajes</Link>
      <p class="galaxy-eyebrow mt-7">✦ Personaje #{{ personaje.id }}</p>
      <h1 class="mt-2 text-3xl font-black tracking-tight text-white">Editar personaje</h1>
      <p class="mb-7 mt-2 text-sm text-slate-300">Actualiza sus datos. La procedencia se mantiene.</p>
      <PersonajeForm :personaje="personaje" />
    </div>
  </AppLayout>
</template>
