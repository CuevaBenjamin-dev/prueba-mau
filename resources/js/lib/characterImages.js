const PEOPLE_IMAGE_BASE = 'https://starwarsgallery.netlify.app/assets/people';

export function characterImageUrl(personaje) {
  const id = personaje?.swapi_id;

  if (personaje?.origen_swapi !== true || !Number.isInteger(id) || id < 1 || id > 83) {
    return null;
  }

  return `${PEOPLE_IMAGE_BASE}/${id}.jpg`;
}
