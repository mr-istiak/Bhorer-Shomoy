export function slugify(value = '') {
  return value.toString().toLowerCase().normalize('NFKD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9\-_\s]/g, '').trim().replace(/[\s_]+/g, '-').replace(/\-+/g, '-').replace(/(^-+|-+$)/g, '');
}

/**
 * Creates a full URL given a slug, by prepending the application's main origin.
 * origin = 'https://bhorershomony.com';
 * @param slug The slug to convert to a full URL.
 * @returns The full URL.
 */
export function makeUrl(slug : string) {
  const origin = 'https://bhorershomony.com';
  return `${origin}/${slug}`;
}

export function useSlug() {
    return { slugify, makeUrl };
}
