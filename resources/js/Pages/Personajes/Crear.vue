<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '../../Components/AppLayout.vue';
import PersonajeForm from '../../Components/PersonajeForm.vue';
</script>

<template>
  <Head title="Crear personaje" />
  <AppLayout>
    <div class="mx-auto max-w-3xl">
      <Link href="/personajes" class="galaxy-back-link">← Volver a personajes</Link>
      <p class="galaxy-eyebrow mt-7">✦ Registro manual</p>
      <h1 class="galaxy-page-title mt-2">Crear personaje</h1>
      <p class="galaxy-subtitle mb-7 mt-2">Los campos con * son obligatorios. Las medidas pueden dejarse vacías.</p>
      <PersonajeForm />
    </div>
  </AppLayout>
</template>
