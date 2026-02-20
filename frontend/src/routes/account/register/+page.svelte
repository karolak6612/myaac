<script>
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  import { base } from '$app/paths';

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
  let submitting = false;
  let csrf_token = '';

  onMount(async () => {
    try {
      const response = await fetch(`${base}/account/create?api=1`);
      if (response.ok) {
        const data = await response.json();
        config = data.config;
        countries = data.countries || {};
        csrf_token = data.csrf_token;
      }
    } catch (e) {
      console.error(e);
      error = 'Failed to load registration form configuration.';
    }
  });

  async function handleSubmit() {
    if (submitting) return;
    submitting = true;
    error = '';
    const form = new FormData();
    for (const key in formData) {
      // @ts-ignore
      const val = formData[key];
      if (typeof val === 'boolean') {
        if (val) {
          form.append(key, '1');
        }
      } else {
        form.append(key, val);
      }
    }
    form.append('save', '1');
    if (csrf_token) {
        form.append('csrf_token', csrf_token);
    }

    try {
      const response = await fetch(`${base}/account/create?api=1`, {
        method: 'POST',
        body: form
      });

      let data;
      const contentType = response.headers.get('content-type');
      if (contentType && contentType.indexOf('application/json') !== -1) {
          data = await response.json();
      } else {
          data = { errors: ['An error occurred.'] };
      }

      if (!response.ok) {
        error = data.errors ? Object.values(data.errors).join(', ') : 'Registration failed.';
        return;
      }

      if (data.success) {
        goto(`${base}/account/login`);
      } else {
        error = data.message || 'Registration failed.';
      }
    } catch (e) {
      console.error(e);
      error = 'An error occurred during registration. Please try again.';
    } finally {
        submitting = false;
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

  <button type="submit" disabled={submitting}>{submitting ? 'Registering...' : 'Register'}</button>

  {#if error}
    <p style="color: red">{error}</p>
  {/if}
</form>
