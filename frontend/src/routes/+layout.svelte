<script>
  import '../app.css';
  import { onMount } from 'svelte';
  import { user, loggedIn, loading } from '$lib/stores/auth';
  import { base } from '$app/paths';
  import Navigation from '$lib/components/Navigation.svelte';

  onMount(async () => {
    loading.set(true);
    try {
      const response = await fetch(`${base}/account/manage?api=1`);
      if (response.ok) {
        const data = await response.json();
        if (data.logged) {
          user.set(data.account);
          loggedIn.set(true);
        } else {
            loggedIn.set(false);
            user.set(null);
        }
      } else {
          loggedIn.set(false);
          user.set(null);
      }
    } catch (e) {
      console.error(e);
      loggedIn.set(false);
      user.set(null);
    } finally {
        loading.set(false);
    }
  });
</script>

<Navigation />

<main class="container mx-auto p-4">
  <slot />
</main>
