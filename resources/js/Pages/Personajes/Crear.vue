<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '../../Components/AppLayout.vue';
import PersonajeForm from '../../Components/PersonajeForm.vue';
</script>

<template>
  <Head title="Crear personaje" />
  <AppLayout>
    <div class="mx-auto max-w-3xl">
      <Link href="/personajes" class="text-sm font-semibold text-sky-300 hover:text-white hover:underline">← Volver a personajes</Link>
      <p class="galaxy-eyebrow mt-7">✦ Registro manual</p>
      <h1 class="mt-2 text-3xl font-black tracking-tight text-white">Crear personaje</h1>
      <p class="mb-7 mt-2 text-sm text-slate-300">Los campos con * son obligatorios. Las medidas pueden dejarse vacías.</p>
      <PersonajeForm />
    </div>
  </AppLayout>
</template>
