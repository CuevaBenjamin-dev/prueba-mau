<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
  <div class="min-h-screen bg-[#f5f7fb]">
    <header class="border-b border-slate-200 bg-white">
      <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-5 sm:px-6">
        <Link href="/personajes" class="flex items-center gap-3 text-slate-900 no-underline">
          <span class="flex size-10 items-center justify-center rounded-xl bg-slate-900 text-xl text-amber-300" aria-hidden="true">✦</span>
          <span><strong class="block text-base leading-tight tracking-tight">Archivo Galáctico</strong><small class="text-xs font-medium tracking-[.16em] text-slate-500 uppercase">Personajes Star Wars</small></span>
        </Link>
        <span class="hidden rounded-full border border-slate-200 px-3 py-1 text-xs font-semibold tracking-wide text-slate-500 sm:inline">MAU · Prueba técnica</span>
      </div>
    </header>
    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-12"><slot /></main>
    <footer class="mx-auto max-w-6xl px-4 pb-8 text-xs text-slate-400 sm:px-6">Gestión local de personajes · Datos importados desde SWAPI</footer>
  </div>
</template>
