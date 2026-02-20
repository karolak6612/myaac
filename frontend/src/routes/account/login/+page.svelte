<script>
  import { goto } from '$app/navigation';
  import { user, loggedIn } from '$lib/stores/auth.svelte';
  import { onMount } from 'svelte';
  import { base } from '$app/paths';

  let account_login = '';
  let password_login = '';
  let error = '';
  let csrf_token = '';

  onMount(async () => {
    try {
        const res = await fetch(`${base}/account/csrf?api=1`);
        if (res.ok) {
            const data = await res.json();
            csrf_token = data.csrf_token;
        }
    } catch (e) {
        console.error('Failed to fetch CSRF token', e);
    }
  });

  async function handleSubmit() {
    error = '';
    const formData = new FormData();
    formData.append('account_login', account_login);
    formData.append('password_login', password_login);
    if (csrf_token) {
        formData.append('csrf_token', csrf_token);
    }

    try {
      const response = await fetch(`${base}/account/login_api?api=1`, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      });

      // Safe JSON parsing
      let data;
      const contentType = response.headers.get('content-type');
      if (contentType && contentType.indexOf('application/json') !== -1) {
          data = await response.json();
      } else {
          data = { errors: ['An error occurred.'] };
      }

      if (!response.ok) {
        error = data.errors ? data.errors.join(', ') : 'Login failed';
        return;
      }

      if (data.logged) {
        loggedIn.value = true;
        user.value = data.account;
        goto(`${base}/account/manage`);
      } else {
        error = data.errors ? data.errors.join(', ') : 'Login failed';
      }
    } catch (e) {
      console.error(e);
      error = 'An error occurred. Please try again.';
    }
  }
</script>

<h1>Login</h1>

<form on:submit|preventDefault={handleSubmit}>
  <div>
    <label for="account_login">Account Name/Number:</label>
    <input id="account_login" bind:value={account_login} required />
  </div>
  <div>
    <label for="password_login">Password:</label>
    <input id="password_login" type="password" bind:value={password_login} required />
  </div>
  <button type="submit">Login</button>

  {#if error}
    <p class="text-red-500">{error}</p>
  {/if}
</form>
