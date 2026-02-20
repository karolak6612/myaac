<script lang="ts">
  import { onMount } from 'svelte';
  import { base } from '$app/paths';
  import DOMPurify from 'dompurify';

  interface Guild {
      name: string;
      logo: string;
      description: string;
  }

  let guilds: Guild[] = [];
  let error = '';
  let loading = true;

  onMount(async () => {
    try {
      const response = await fetch(`${base}/guilds?api=1`);
      if (response.ok) {
        const data = await response.json();
        guilds = Array.isArray(data.guilds) ? data.guilds : [];
      } else {
        error = 'Failed to load guilds.';
      }
    } catch (e) {
      console.error(e);
      error = 'Failed to load guilds.';
    } finally {
        loading = false;
    }
  });
</script>

<h1 class="text-2xl font-bold mb-4">Guilds</h1>

{#if loading}
  <p>Loading...</p>
{:else if error}
  <p class="text-red-500">{error}</p>
{:else if guilds.length === 0}
  <p>No guilds found.</p>
{:else}
  <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    {#each guilds as guild}
      <li class="border rounded p-4 shadow bg-white hover:bg-gray-50 transition">
        <a href="{base}/guilds/{encodeURIComponent(guild.name)}" class="flex flex-col items-center text-center">
          <img src="{base}/images/guilds/{guild.logo}" alt="{guild.name} logo" class="w-16 h-16 object-cover mb-2" />
          <h2 class="text-xl font-bold">{guild.name}</h2>
        </a>
        <div class="mt-2 text-sm text-gray-600">
            {@html DOMPurify.sanitize(guild.description)}
        </div>
      </li>
    {/each}
  </ul>
{/if}
