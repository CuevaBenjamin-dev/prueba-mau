import { ref } from 'vue';

export const editOriginUrl = ref('/personajes');
export const returningPersonajeId = ref(null);

export function canAnimatePersonajeTransition() {
  return Boolean(document.startViewTransition)
    && window.matchMedia('(min-width: 1024px) and (prefers-reduced-motion: no-preference)').matches;
}
