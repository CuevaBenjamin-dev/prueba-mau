<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '../../Components/AppLayout.vue';
import PersonajeForm from '../../Components/PersonajeForm.vue';
</script>

<template>
  <Head title="Crear personaje" />
  <AppLayout>
    <div class="mx-auto max-w-3xl"><Link href="/personajes" class="text-sm font-semibold text-blue-700 hover:underline">← Volver al listado</Link><p class="mt-8 text-xs font-bold tracking-[.18em] text-blue-700 uppercase">Registro manual</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Crear personaje</h1><p class="mb-7 mt-2 text-sm text-slate-500">Los campos con * son obligatorios. Las medidas pueden dejarse vacías.</p><PersonajeForm /></div>
  </AppLayout>
</template>
