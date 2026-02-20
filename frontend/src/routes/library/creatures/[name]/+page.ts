
import type { PageLoad } from './$types';
import { base } from '$app/paths';

export const load: PageLoad = async ({ fetch, params }) => {
  const res = await fetch(`${base}/monsters/${encodeURIComponent(params.name)}?api=1`);
  if (res.ok) {
    const data = await res.json();
    return { monster: data.monster };
  }
  return { error: 'Failed to load creature.' };
};
