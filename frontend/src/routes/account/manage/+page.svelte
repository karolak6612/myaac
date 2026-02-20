<script>
  import { onMount } from 'svelte';
  import { user, loggedIn, loading } from '$lib/stores/auth';
  import { goto } from '$app/navigation';

  let accountData = null;
  let players = [];
  let error = '';

  $: if (!$loading && !$loggedIn) {
    goto('/account/login');
  }

  onMount(async () => {
    try {
      const response = await fetch('/account/manage?api=1');
      if (response.ok) {
        const data = await response.json();
        if (data.logged) {
          accountData = data.account;
          players = data.players || [];
          user.set(data.account);
          loggedIn.set(true);
        } else {
            loggedIn.set(false);
            goto('/account/login');
        }
      } else {
        error = 'Failed to load account data.';
      }
    } catch (e) {
      console.error(e);
      error = 'Failed to load account data.';
    }
  });

  async function logout() {
    try {
      const response = await fetch('/account/logout?api=1');
      if (response.ok) {
        loggedIn.set(false);
        user.set(null);
        goto('/');
      }
    } catch (e) {
      console.error(e);
    }
  }
</script>

{#if $loading}
  <p>Loading...</p>
{:else if $loggedIn && accountData}
  <h1>Welcome, {accountData.name}</h1>
  <button on:click={logout}>Logout</button>

  <h2>Account Status</h2>
  <p>Status: {accountData.is_premium ? 'Premium' : 'Free Account'}</p>
  <p>Premium Days: {accountData.prem_days}</p>
  <p>Email: {accountData.email}</p>

  <h2>Characters</h2>
  {#if players.length === 0}
    <p>No characters created yet.</p>
  {:else}
    <ul>
      {#each players as player}
        <li>
          <strong>{player.name}</strong> - Level {player.level}, {player.vocation}
          <br/>
          <small>Town: {player.town_id}</small>
        </li>
      {/each}
    </ul>
  {/if}
{:else if error}
  <p style="color: red">{error}</p>
{/if}
