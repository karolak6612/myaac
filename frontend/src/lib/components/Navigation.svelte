<script>
  import { loggedIn, user } from '$lib/stores/auth.svelte';
  import { base } from '$app/paths';
</script>

<nav class="bg-gray-800 text-white p-4">
  <div class="container mx-auto flex justify-between items-center">
    <a href="{base}/" class="text-xl font-bold">MyAAC</a>
    <ul class="flex space-x-4">
      <li><a href="{base}/" class="hover:text-gray-300">Home</a></li>
      <li><a href="{base}/highscores" class="hover:text-gray-300">Highscores</a></li>
      <li><a href="{base}/guilds" class="hover:text-gray-300">Guilds</a></li>
      <li><a href="{base}/library/creatures" class="hover:text-gray-300">Creatures</a></li>
      <li><a href="{base}/library/spells" class="hover:text-gray-300">Spells</a></li>
      <li><a href="{base}/forum" class="hover:text-gray-300">Forum</a></li>

      {#if loggedIn.value}
        <li><a href="{base}/account/manage" class="hover:text-gray-300">Account</a></li>
        {#if user.value && user.value.is_admin}
          <li><a href="{base}/admin" class="hover:text-gray-300">Admin</a></li>
        {/if}
      {:else}
        <li><a href="{base}/account/login" class="hover:text-gray-300">Login</a></li>
        <li><a href="{base}/account/register" class="hover:text-gray-300">Register</a></li>
      {/if}
    </ul>
  </div>
</nav>
