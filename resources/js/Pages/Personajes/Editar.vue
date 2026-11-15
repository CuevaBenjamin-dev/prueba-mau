<script setup>
import { computed, onMounted, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '../../Components/AppLayout.vue';
import PersonajeAvatar from '../../Components/PersonajeAvatar.vue';
import PersonajeForm from '../../Components/PersonajeForm.vue';
import { canAnimatePersonajeTransition, editOriginUrl, returningPersonajeId } from '../../personajeTransition';

const props = defineProps({ personaje: { type: Object, required: true } });
const animateReturn = ref(false);
let transitionStarted = false;

onMounted(() => {
  animateReturn.value = canAnimatePersonajeTransition();
});

function prepareReturn() {
  if (animateReturn.value) {
    transitionStarted = false;
    returningPersonajeId.value = props.personaje.id;
  }
}

function completeReturn(transition) {
  transitionStarted = true;
  transition.finished.then(
    () => { returningPersonajeId.value = null; },
    () => { returningPersonajeId.value = null; },
  );
}

function finishReturn() {
  if (!transitionStarted) returningPersonajeId.value = null;
}

const returnNavigation = computed(() => ({
  href: editOriginUrl.value,
  viewTransition: animateReturn.value ? completeReturn : false,
  onBefore: prepareReturn,
  onFinish: finishReturn,
}));
</script>

<template>
  <Head :title="`Editar ${personaje.nombre}`" />
  <AppLayout>
    <div class="mx-auto max-w-6xl">
      <Link v-bind="returnNavigation" class="text-sm font-semibold text-sky-300 hover:text-white hover:underline">← Volver a personajes</Link>
      <div class="edit-layout mt-4">
        <div class="edit-layout__portrait">
          <PersonajeAvatar :personaje="personaje" large />
        </div>
        <div class="min-w-0">
          <p class="galaxy-eyebrow">✦ Personaje #{{ personaje.id }}</p>
          <h1 class="mt-1 text-3xl font-black tracking-tight text-white">Editar personaje</h1>
          <p class="mb-3 mt-1 text-sm text-slate-300">Actualiza sus datos. La procedencia se mantiene.</p>
          <PersonajeForm :personaje="personaje" :return-navigation="returnNavigation" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
