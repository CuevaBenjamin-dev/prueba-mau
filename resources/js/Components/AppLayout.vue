<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
  <div class="galaxy-app min-h-screen">
    <header class="galaxy-header">
      <div class="galaxy-header__inner mx-auto w-full max-w-[1900px] gap-4 px-4 py-3 sm:px-6 lg:px-8">
        <Link href="/personajes" class="galaxy-header__brand group flex min-w-0 items-center gap-3 text-slate-100 no-underline sm:gap-4">
          <span class="galaxy-emblem" aria-hidden="true">✦</span>
          <span class="min-w-0"><strong class="galaxy-header__brand-title block truncate">Archivo Galáctico</strong><small class="galaxy-header__subtitle block truncate text-[10px] font-semibold tracking-[.25em] uppercase sm:text-xs">Personajes Star Wars</small></span>
        </Link>
        <span class="galaxy-header__title">PERSONAJES STAR WARS</span>
        <span class="galaxy-header__badge hidden rounded-full px-4 py-1.5 text-xs font-semibold sm:inline">MAU · Prueba técnica</span>
      </div>
    </header>
    <main class="galaxy-panel mx-auto my-3 w-[calc(100%-1.5rem)] max-w-[1840px] px-4 py-4 sm:w-[calc(100%-3rem)] sm:px-6 lg:px-8"><slot /></main>
    <footer class="galaxy-footer mx-auto max-w-[1840px] px-6 pb-7 text-xs">Gestión local de personajes · Datos importados desde SWAPI</footer>
  </div>
</template>
