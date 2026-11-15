<script setup>
import { computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ personaje: { type: Object, default: null }, returnNavigation: { type: Object, default: null } });
const editing = computed(() => Boolean(props.personaje));
const form = useForm({
  nombre: props.personaje?.nombre ?? '',
  altura: props.personaje?.altura ?? '',
  masa: props.personaje?.masa ?? '',
  color_cabello: props.personaje?.color_cabello ?? '',
  color_ojos: props.personaje?.color_ojos ?? '',
  genero: props.personaje?.genero ?? '',
  planeta_origen: props.personaje?.planeta_origen ?? '',
});

const fields = [
  { key: 'nombre', label: 'Nombre', required: true, min: 3, max: 100, placeholder: 'Ej. Luke Skywalker' },
  { key: 'altura', label: 'Altura (cm)', type: 'number', min: 1, max: 300, placeholder: 'Ej. 172' },
  { key: 'masa', label: 'Masa (kg)', type: 'number', min: 1, max: 500, placeholder: 'Ej. 77' },
  { key: 'color_cabello', label: 'Color de cabello', max: 100, placeholder: 'Ej. rubio' },
  { key: 'color_ojos', label: 'Color de ojos', max: 100, placeholder: 'Ej. azul' },
  { key: 'planeta_origen', label: 'Planeta de origen', max: 100, placeholder: 'Ej. Tatooine' },
];

function submit() {
  form.transform((data) => ({
    ...data,
    altura: data.altura === '' ? null : data.altura,
    masa: data.masa === '' ? null : data.masa,
    color_cabello: data.color_cabello === '' ? null : data.color_cabello,
    color_ojos: data.color_ojos === '' ? null : data.color_ojos,
    planeta_origen: data.planeta_origen === '' ? null : data.planeta_origen,
  }));
  if (editing.value) form.put(`/personajes/${props.personaje.id}`, { preserveScroll: true });
  else form.post('/personajes', { preserveScroll: true });
}
</script>

<template>
  <form class="galaxy-form p-5 sm:p-8" :class="{ 'galaxy-form--edit': editing }" @submit.prevent="submit">
    <div v-if="editing" class="galaxy-provenance mb-7 flex flex-wrap items-center gap-2 rounded-xl px-4 py-3 text-sm">
      <span class="galaxy-provenance__label font-semibold">Procedencia:</span>
      {{ personaje.origen_swapi ? `SWAPI #${personaje.swapi_id}` : 'Manual' }}
      <span class="galaxy-provenance__hint">· El identificador y el origen se conservan al editar.</span>
    </div>
    <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
      <div v-for="field in fields" :key="field.key" :class="field.key === 'nombre' ? 'sm:col-span-2' : ''">
        <label :for="field.key" class="mb-1.5 block text-sm font-semibold text-slate-100">{{ field.label }} <span v-if="field.required" class="text-amber-300">*</span></label>
        <input :id="field.key" v-model="form[field.key]" :type="field.type ?? 'text'" :placeholder="field.placeholder" :required="field.required" :min="field.type === 'number' ? field.min : undefined" :max="field.type === 'number' ? field.max : undefined" :minlength="field.type ? undefined : field.min" :maxlength="field.type ? undefined : field.max" :aria-invalid="Boolean(form.errors[field.key])" :aria-describedby="form.errors[field.key] ? `${field.key}-error` : undefined" class="galaxy-field" />
        <p v-if="form.errors[field.key]" :id="`${field.key}-error`" class="mt-1.5 text-sm text-rose-200" role="alert">{{ form.errors[field.key] }}</p>
      </div>
      <div>
        <label for="genero" class="mb-1.5 block text-sm font-semibold text-slate-100">Género <span class="text-amber-300">*</span></label>
        <select id="genero" v-model="form.genero" required :aria-invalid="Boolean(form.errors.genero)" class="galaxy-field">
          <option value="" disabled>Seleccionar género</option>
          <option value="masculino">Masculino</option><option value="femenino">Femenino</option><option value="n/a">N/A</option>
        </select>
        <p v-if="form.errors.genero" class="mt-1.5 text-sm text-rose-200" role="alert">{{ form.errors.genero }}</p>
      </div>
    </div>
    <div class="mt-8 flex flex-col-reverse gap-3 border-t border-sky-200/15 pt-6 sm:flex-row sm:justify-end">
      <Link v-bind="returnNavigation ?? { href: '/personajes' }" class="galaxy-button galaxy-button--outline">Cancelar</Link>
      <button type="submit" :disabled="form.processing" class="galaxy-button galaxy-button--gold">{{ form.processing ? 'Guardando…' : editing ? 'Actualizar personaje' : 'Guardar personaje' }}</button>
    </div>
  </form>
</template>
