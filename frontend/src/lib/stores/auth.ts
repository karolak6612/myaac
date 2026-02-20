import { writable } from 'svelte/store';

export const user = writable<any>(null);
export const loggedIn = writable(false);
export const loading = writable(true);
