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
  form.post('/personajes/importar', {
    preserveScroll: true,
    onSuccess: () => { form.reset(); form.clearErrors(); emit('close'); },
  });
}
</script>

<template>
  <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4" @click.self="close" @keydown.esc="close">
    <section role="dialog" aria-modal="true" aria-labelledby="import-title" class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl sm:p-8">
      <div class="flex items-start justify-between gap-4"><div><p class="text-xs font-bold tracking-[.18em] text-blue-700 uppercase">Conexión SWAPI</p><h2 id="import-title" class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Importar personaje</h2></div><button type="button" :disabled="form.processing" aria-label="Cerrar modal" class="rounded-lg p-1 text-2xl leading-none text-slate-400 hover:text-slate-800" @click="close">×</button></div>
      <p class="mt-3 text-sm leading-6 text-slate-500">Ingresa un ID de personaje entre 1 y 83. Consultaremos SWAPI y añadiremos el registro a tu archivo.</p>
      <form class="mt-7" @submit.prevent="submit">
        <label for="swapi-id" class="mb-1.5 block text-sm font-semibold text-slate-700">ID de SWAPI</label>
        <input id="swapi-id" ref="input" v-model="form.id" type="number" min="1" max="83" step="1" required :aria-invalid="Boolean(form.errors.id)" :aria-describedby="form.errors.id ? 'swapi-error' : undefined" placeholder="Ej. 1" class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" />
        <p v-if="form.errors.id" id="swapi-error" class="mt-2 text-sm text-rose-700" role="alert">{{ form.errors.id }}</p>
        <div class="mt-7 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"><button type="button" :disabled="form.processing" class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50" @click="close">Cancelar</button><button type="submit" :disabled="form.processing" class="flex items-center justify-center gap-2 rounded-xl bg-blue-700 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-800"><span v-if="form.processing" class="size-4 animate-spin rounded-full border-2 border-white/40 border-t-white" aria-hidden="true"></span>{{ form.processing ? 'Importando…' : 'Importar' }}</button></div>
      </form>
    </section>
  </div>
</template>
