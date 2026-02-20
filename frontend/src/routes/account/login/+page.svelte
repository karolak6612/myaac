<script>
  import { goto } from '$app/navigation';
  import { user, loggedIn } from '$lib/stores/auth';

  let account_login = '';
  let password_login = '';
  let error = '';

  async function handleSubmit() {
    error = '';
    const formData = new FormData();
    formData.append('account_login', account_login);
    formData.append('password_login', password_login);

    try {
      const response = await fetch('/account/login_api?api=1', {
        method: 'POST',
        body: formData
      });

      if (!response.ok) {
        const err = await response.json();
        error = err.errors ? err.errors.join(', ') : 'Login failed';
        return;
      }

      const data = await response.json();
      if (data.logged) {
        loggedIn.set(true);
        user.set(data.account);
        goto('/account/manage');
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
    <p style="color: red">{error}</p>
  {/if}
</form>
