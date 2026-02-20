<script>
  import { loggedIn, user } from '$lib/stores/auth';
</script>

<nav class="bg-gray-800 text-white p-4">
  <div class="container mx-auto flex justify-between items-center">
    <a href="/" class="text-xl font-bold">MyAAC</a>
    <ul class="flex space-x-4">
      <li><a href="/" class="hover:text-gray-300">Home</a></li>
      <li><a href="/highscores" class="hover:text-gray-300">Highscores</a></li>
      <li><a href="/guilds" class="hover:text-gray-300">Guilds</a></li>
      <li><a href="/library/creatures" class="hover:text-gray-300">Creatures</a></li>
      <li><a href="/library/spells" class="hover:text-gray-300">Spells</a></li>
      <li><a href="/forum" class="hover:text-gray-300">Forum</a></li>

      {#if $loggedIn}
        <li><a href="/account/manage" class="hover:text-gray-300">Account</a></li>
        {#if $user && $user.is_admin}
          <li><a href="/admin" class="hover:text-gray-300">Admin</a></li>
        {/if}
      {:else}
        <li><a href="/account/login" class="hover:text-gray-300">Login</a></li>
        <li><a href="/account/register" class="hover:text-gray-300">Register</a></li>
      {/if}
    </ul>
  </div>
</nav>
