<script setup>
import { ref, watch, nextTick } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({ open: Boolean });
const emit = defineEmits(['close']);
const input = ref(null);
const form = useForm({ id: '' });

watch(() => props.open, async (open) => {
  if (open) { form.clearErrors(); await nextTick(); input.value?.focus(); }
});

function close() {
  if (!form.processing) { form.reset(); form.clearErrors(); emit('close'); }
}

function submit() {
  if (form.processing) return;
  form.post('/personajes/importar', {
    preserveScroll: true,
    onSuccess: () => { form.reset(); form.clearErrors(); emit('close'); },
  });
}
</script>

<template>
  <div v-if="open" class="galaxy-modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="close" @keydown.esc="close">
    <section role="dialog" aria-modal="true" aria-labelledby="import-title" class="galaxy-modal w-full max-w-md p-5 sm:p-7">
      <div class="flex items-start justify-between gap-4">
        <div><p class="galaxy-eyebrow">✦ Conexión SWAPI</p><h2 id="import-title" class="galaxy-page-title galaxy-page-title--modal mt-2">Importar personaje</h2></div>
        <button type="button" :disabled="form.processing" aria-label="Cerrar modal" class="galaxy-modal__close" @click="close">×</button>
      </div>
      <p class="mt-3 text-sm leading-6 text-slate-300">Ingresa un ID de personaje entre 1 y 83. Consultaremos SWAPI y añadiremos el registro a tu archivo.</p>
      <form class="mt-6" @submit.prevent="submit">
        <label for="swapi-id" class="mb-2 block text-sm font-semibold text-slate-100">ID de SWAPI</label>
        <input id="swapi-id" ref="input" v-model="form.id" type="number" inputmode="numeric" min="1" max="83" step="1" required :aria-invalid="Boolean(form.errors.id)" :aria-describedby="form.errors.id ? 'swapi-error' : undefined" placeholder="Ej. 1" class="galaxy-field" />
        <p v-if="form.errors.id" id="swapi-error" class="mt-2 text-sm font-medium text-rose-200" role="alert">{{ form.errors.id }}</p>
        <div class="mt-7 flex flex-col-reverse gap-3 border-t border-sky-200/15 pt-5 sm:flex-row sm:justify-end">
          <button type="button" :disabled="form.processing" class="galaxy-button galaxy-button--outline" @click="close">Cancelar</button>
          <button type="submit" :disabled="form.processing" class="galaxy-button galaxy-button--gold"><span v-if="form.processing" class="size-4 animate-spin rounded-full border-2 border-white/40 border-t-white" aria-hidden="true"></span>{{ form.processing ? 'Importando…' : 'Importar' }}</button>
        </div>
      </form>
    </section>
  </div>
</template>
