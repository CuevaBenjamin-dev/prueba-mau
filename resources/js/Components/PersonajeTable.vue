<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({ personajes: { type: Array, required: true }, deletingId: { type: Number, default: null } });
const emit = defineEmits(['delete']);
</script>

<template>
  <div v-if="personajes.length === 0" class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
    <div class="mx-auto mb-4 flex size-14 items-center justify-center rounded-2xl bg-blue-50 text-2xl text-blue-700" aria-hidden="true">✦</div>
    <h2 class="text-xl font-bold text-slate-900">Todavía no hay personajes</h2>
    <p class="mt-2 text-sm text-slate-500">Crea uno manualmente o importa un personaje desde SWAPI.</p>
  </div>
  <div v-else class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="hidden overflow-x-auto md:block">
      <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 text-xs font-bold tracking-wide text-slate-500 uppercase"><tr><th class="px-6 py-4">Personaje</th><th class="px-5 py-4">Altura</th><th class="px-5 py-4">Masa</th><th class="px-5 py-4">Género</th><th class="px-5 py-4">Origen</th><th class="px-6 py-4 text-right">Acciones</th></tr></thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="personaje in personajes" :key="personaje.id" class="hover:bg-slate-50/70">
            <td class="px-6 py-5 font-semibold text-slate-900">{{ personaje.nombre }}</td><td class="px-5 py-5 text-slate-600">{{ personaje.altura == null ? '—' : `${personaje.altura} cm` }}</td><td class="px-5 py-5 text-slate-600">{{ personaje.masa == null ? '—' : `${personaje.masa} kg` }}</td><td class="px-5 py-5 text-slate-600 capitalize">{{ personaje.genero }}</td>
            <td class="px-5 py-5"><span :class="personaje.origen_swapi ? 'bg-violet-50 text-violet-700' : 'bg-slate-100 text-slate-600'" class="rounded-full px-3 py-1 text-xs font-semibold">{{ personaje.origen_swapi ? 'SWAPI' : 'Manual' }}</span></td>
            <td class="px-6 py-5 text-right"><div class="flex justify-end gap-3"><Link :href="`/personajes/${personaje.id}/editar`" class="font-semibold text-blue-700 hover:underline">Editar</Link><button type="button" :disabled="deletingId === personaje.id" class="font-semibold text-rose-700 hover:underline" @click="emit('delete', personaje)">{{ deletingId === personaje.id ? 'Eliminando…' : 'Eliminar' }}</button></div></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="divide-y divide-slate-100 md:hidden">
      <article v-for="personaje in personajes" :key="personaje.id" class="p-5">
        <div class="flex items-start justify-between gap-3"><h2 class="font-bold text-slate-900">{{ personaje.nombre }}</h2><span :class="personaje.origen_swapi ? 'bg-violet-50 text-violet-700' : 'bg-slate-100 text-slate-600'" class="rounded-full px-3 py-1 text-xs font-semibold">{{ personaje.origen_swapi ? 'SWAPI' : 'Manual' }}</span></div>
        <dl class="mt-4 grid grid-cols-3 gap-2 text-sm"><div><dt class="text-slate-400">Altura</dt><dd>{{ personaje.altura == null ? '—' : `${personaje.altura} cm` }}</dd></div><div><dt class="text-slate-400">Masa</dt><dd>{{ personaje.masa == null ? '—' : `${personaje.masa} kg` }}</dd></div><div><dt class="text-slate-400">Género</dt><dd class="capitalize">{{ personaje.genero }}</dd></div></dl>
        <div class="mt-5 flex gap-4 border-t border-slate-100 pt-4 text-sm"><Link :href="`/personajes/${personaje.id}/editar`" class="font-semibold text-blue-700">Editar</Link><button type="button" :disabled="deletingId === personaje.id" class="font-semibold text-rose-700" @click="emit('delete', personaje)">{{ deletingId === personaje.id ? 'Eliminando…' : 'Eliminar' }}</button></div>
      </article>
    </div>
  </div>
</template>
