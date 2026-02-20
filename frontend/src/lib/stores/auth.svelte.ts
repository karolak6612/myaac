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

export const user = $state({ value: null as User | null });
export const loggedIn = $state({ value: false });
export const loading = $state({ value: true });
