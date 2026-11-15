<script setup>
import { computed, ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '../../Components/AppLayout.vue';
import ImportModal from '../../Components/ImportModal.vue';
import PersonajeTable from '../../Components/PersonajeTable.vue';

const props = defineProps({ personajes: { type: Object, required: true } });
const page = usePage();
const modalOpen = ref(false);
const deletingId = ref(null);
const flash = computed(() => page.props.flash ?? {});

function remove(personaje) {
  if (!window.confirm(`¿Eliminar a ${personaje.nombre}? Esta acción no se puede deshacer.`)) return;
  deletingId.value = personaje.id;
  router.delete(`/personajes/${personaje.id}`, {
    preserveScroll: true,
    onFinish: () => { deletingId.value = null; },
  });
}
</script>

<template>
  <Head title="Personajes" />
  <AppLayout>
    <div class="mb-8 flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
      <div><p class="mb-2 text-xs font-bold tracking-[.18em] text-blue-700 uppercase">Base de datos local</p><h1 class="text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">Personajes</h1><p class="mt-2 text-sm text-slate-500">Consulta, administra e importa habitantes de la galaxia.</p></div>
      <div class="flex flex-col gap-3 sm:flex-row"><button type="button" class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50" @click="modalOpen = true">Importar desde SWAPI</button><Link href="/personajes/crear" class="rounded-xl bg-blue-700 px-5 py-3 text-center text-sm font-semibold text-white shadow-sm hover:bg-blue-800">+ Crear nuevo</Link></div>
    </div>
    <div v-if="flash.success" role="status" class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800">{{ flash.success }}</div>
    <div v-if="flash.error" role="alert" class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-medium text-rose-800">{{ flash.error }}</div>
    <div class="mb-4 flex items-center justify-between text-sm"><span class="font-semibold text-slate-700">{{ personajes.total }} {{ personajes.total === 1 ? 'personaje' : 'personajes' }}</span><span class="text-slate-500">Página {{ personajes.current_page }} de {{ personajes.last_page }}</span></div>
    <PersonajeTable :personajes="personajes.data" :deleting-id="deletingId" @delete="remove" />
    <nav v-if="personajes.last_page > 1" aria-label="Paginación de personajes" class="mt-6 flex flex-wrap justify-center gap-2">
      <template v-for="(link, index) in personajes.links" :key="index"><Link v-if="link.url" :href="link.url" preserve-scroll :aria-current="link.active ? 'page' : undefined" :class="link.active ? 'border-blue-700 bg-blue-700 text-white' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'" class="min-w-10 rounded-lg border px-3 py-2 text-center text-sm font-semibold" v-html="link.label" /><span v-else class="min-w-10 rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-center text-sm text-slate-400" v-html="link.label" /></template>
    </nav>
    <ImportModal :open="modalOpen" @close="modalOpen = false" />
  </AppLayout>
</template>
