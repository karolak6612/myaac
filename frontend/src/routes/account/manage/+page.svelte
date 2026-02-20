<script>
  import { onMount } from 'svelte';
  import { user, loggedIn, loading } from '$lib/stores/auth.svelte';
  import { goto } from '$app/navigation';
  import { base } from '$app/paths';

  /** @type {{ accountData: any, players: any[], error: string }} */
  let state = $state({
      accountData: null,
      players: [],
      error: ''
  });

  $effect(() => {
    if (!loading.value && !loggedIn.value) {
        goto(`${base}/account/login`);
    }
  });

  onMount(async () => {
    try {
      const response = await fetch(`${base}/account/manage?api=1`);
      if (response.ok) {
        const data = await response.json();
        if (data.logged) {
          state.accountData = data.account;
          state.players = data.players || [];
          user.value = data.account;
          loggedIn.value = true;
        } else {
            loggedIn.value = false;
            goto(`${base}/account/login`);
        }
      } else {
        state.error = 'Failed to load account data.';
      }
    } catch (e) {
      console.error(e);
      state.error = 'Failed to load account data.';
    }
  });

  async function logout() {
    try {
      // Fetch CSRF token first
      const res = await fetch(`${base}/account/csrf?api=1`);
      let csrf_token = '';
      if (res.ok) {
          const data = await res.json();
          csrf_token = data.csrf_token;
      } else {
          state.error = 'Failed to logout: could not fetch CSRF token.';
          return;
      }

      const formData = new FormData();
      formData.append('csrf_token', csrf_token);

      const response = await fetch(`${base}/account/logout?api=1`, {
          method: 'POST',
          body: formData,
          credentials: 'same-origin'
      });
      if (response.ok) {
        loggedIn.value = false;
        user.value = null;
        goto(`${base}/`);
      } else {
          try {
              const data = await response.json();
              state.error = data.message || 'Failed to logout.';
          } catch (e) {
              state.error = 'Failed to logout.';
          }
      }
    } catch (e) {
      console.error(e);
      state.error = 'Failed to logout.';
    }
  }
</script>

{#if loading.value}
  <p>Loading...</p>
{:else if loggedIn.value && state.accountData}
  <h1>Welcome, {state.accountData.name}</h1>
  <button on:click={logout}>Logout</button>

  <h2>Account Status</h2>
  <p>Status: {state.accountData.is_premium ? 'Premium' : 'Free Account'}</p>
  <p>Premium Days: {state.accountData.prem_days}</p>
  <p>Email: {state.accountData.email}</p>

  <h2>Characters</h2>
  {#if state.players.length === 0}
    <p>No characters created yet.</p>
  {:else}
    <ul>
      {#each state.players as player}
        <li>
          <strong>{player.name}</strong> - Level {player.level}, {player.vocation}
          <br/>
          <small>Town: {player.town_id}</small>
        </li>
      {/each}
    </ul>
  {/if}
{:else if state.error}
  <p class="text-red-500">{state.error}</p>
{/if}
