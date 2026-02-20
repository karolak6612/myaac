<script>
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';

  let formData = {
    account: '',
    email: '',
    password: '',
    password_confirm: '',
    country: '',
    accept_rules: false
  };
  let config = {};
  let countries = {};
  let error = '';

  onMount(async () => {
    try {
      const response = await fetch('/account/create?api=1');
      if (response.ok) {
        const data = await response.json();
        config = data.config;
        countries = data.countries || {};
      }
    } catch (e) {
      console.error(e);
      error = 'Failed to load registration form configuration.';
    }
  });

  async function handleSubmit() {
    error = '';
    const form = new FormData();
    for (const key in formData) {
      form.append(key, formData[key]);
    }
    form.append('save', '1');

    try {
      const response = await fetch('/account/create?api=1', {
        method: 'POST',
        body: form
      });

      if (!response.ok) {
        const err = await response.json();
        error = err.errors ? Object.values(err.errors).join(', ') : 'Registration failed.';
        return;
      }

      const data = await response.json();
      if (data.success) {
        goto('/account/login');
      } else {
        error = data.message || 'Registration failed.';
      }
    } catch (e) {
      console.error(e);
      error = 'An error occurred during registration. Please try again.';
    }
  }
</script>

<h1>Register Account</h1>

<form on:submit|preventDefault={handleSubmit}>
  <div>
    {#if config.account_login_by_email}
      <label for="email">Email Address:</label>
      <input id="email" type="email" bind:value={formData.email} required />
    {:else}
      <label for="account">Account Name/Number:</label>
      <input id="account" bind:value={formData.account} required />

      <label for="email">Email Address:</label>
      <input id="email" type="email" bind:value={formData.email} required />
    {/if}
  </div>

  <div>
    <label for="password">Password:</label>
    <input id="password" type="password" bind:value={formData.password} required />
  </div>

  <div>
    <label for="password_confirm">Confirm Password:</label>
    <input id="password_confirm" type="password" bind:value={formData.password_confirm} required />
  </div>

  <div>
    <label for="country">Country:</label>
    <select id="country" bind:value={formData.country} required>
      <option value="">Select Country</option>
      {#each Object.entries(countries) as [code, name]}
        <option value={code}>{name}</option>
      {/each}
    </select>
  </div>

  <div>
    <label>
      <input type="checkbox" bind:checked={formData.accept_rules} required />
      I accept the rules.
    </label>
  </div>

  <button type="submit">Register</button>

  {#if error}
    <p style="color: red">{error}</p>
  {/if}
</form>
