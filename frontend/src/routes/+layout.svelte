<script>
  import '../app.css';
  import { onMount } from 'svelte';
  import { user, loggedIn, loading } from '$lib/stores/auth.svelte';
  import { base } from '$app/paths';
  import Navigation from '$lib/components/Navigation.svelte';

  let { children } = $props();

  /** @param {any} obj */
  function isValidUser(obj) {
      if (!obj || typeof obj !== 'object') return false;
      return (
          typeof obj.id === 'number' &&
          typeof obj.name === 'string' &&
          typeof obj.email === 'string'
      );
  }

  onMount(async () => {
    loading.value = true;
    try {
      const response = await fetch(`${base}/account/manage?api=1`);
      if (response.ok) {
        const data = await response.json();
        if (data.logged && isValidUser(data.account)) {
          user.value = data.account;
          loggedIn.value = true;
        } else {
            if (data.logged) {
                console.warn('Invalid user data received', data.account);
            }
            loggedIn.value = false;
            user.value = null;
        }
      } else {
          loggedIn.value = false;
          user.value = null;
      }
    } catch (e) {
      console.error(e);
      loggedIn.value = false;
      user.value = null;
    } finally {
        loading.value = false;
    }
  });
</script>

<Navigation />

<main class="container mx-auto p-4">
  {@render children()}
</main>
