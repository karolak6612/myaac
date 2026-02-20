import { writable } from 'svelte/store';

export interface User {
  id: number;
  name: string;
  email: string;
  created: number;
  prem_days: number;
  is_premium: boolean;
  is_admin: boolean;
  rlname: string;
  location: string;
  recovery_key_set: boolean;
  email_new_time: number;
  email_new: string;
}

export const user = writable<User | null>(null);
export const loggedIn = writable(false);
export const loading = writable(true);
