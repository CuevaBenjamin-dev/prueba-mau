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

function pageLabel(link, index) {
  if (index === 0) return 'Anterior';
  if (index === props.personajes.links.length - 1) return 'Siguiente';
  return link.label;
}
</script>

<template>
  <Head title="Personajes" />
  <AppLayout>
    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
      <div>
        <p class="galaxy-eyebrow"><span aria-hidden="true">✦</span> Base de datos local</p>
        <h1 class="galaxy-page-title mt-1">Personajes</h1>
        <p class="galaxy-subtitle mt-1">Consulta, administra e importa habitantes de la galaxia.</p>
      </div>
      <div class="flex flex-col gap-2.5 sm:flex-row sm:flex-wrap">
        <button type="button" class="galaxy-button galaxy-button--outline" @click="modalOpen = true"><span aria-hidden="true">◇</span> Importar desde SWAPI</button>
        <Link href="/personajes/crear" class="galaxy-button galaxy-button--gold"><span aria-hidden="true">＋</span> Crear nuevo</Link>
      </div>
    </div>

    <div v-if="flash.error" role="alert" class="galaxy-alert galaxy-alert--error mt-5">{{ flash.error }}</div>

    <div class="galaxy-summary my-4 border-b border-sky-300/15 pb-3 text-sm">
      <div v-if="flash.success" role="status" class="galaxy-alert galaxy-alert--success galaxy-summary__notice">{{ flash.success }}</div>
      <span class="galaxy-counter"><span aria-hidden="true">✦</span> {{ personajes.total }} {{ personajes.total === 1 ? 'personaje' : 'personajes' }}</span>
      <nav v-if="personajes.last_page > 1" aria-label="Paginación de personajes" class="galaxy-summary__nav">
        <template v-for="(link, index) in personajes.links" :key="index">
          <Link v-if="link.url" :href="link.url" preserve-scroll :aria-current="link.active ? 'page' : undefined" :aria-label="index === 0 || index === personajes.links.length - 1 ? pageLabel(link, index) : `Página ${pageLabel(link, index)}`" class="galaxy-pagination" :class="link.active ? 'galaxy-pagination--active' : ''">{{ pageLabel(link, index) }}</Link>
          <span v-else class="galaxy-pagination galaxy-pagination--disabled" aria-disabled="true">{{ pageLabel(link, index) }}</span>
        </template>
      </nav>
      <span class="galaxy-page-indicator">Página {{ personajes.current_page }} de {{ personajes.last_page }}</span>
    </div>

    <PersonajeTable :personajes="personajes.data" :deleting-id="deletingId" @delete="remove" />
    <ImportModal :open="modalOpen" @close="modalOpen = false" />
  </AppLayout>
</template>
