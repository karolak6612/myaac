<script>
  import { onMount } from 'svelte';
  import { base } from '$app/paths';

  let monsters = [];
  let error = '';
  let loading = true;

  onMount(async () => {
    try {
      const response = await fetch(`${base}/monsters?api=1`);
      if (response.ok) {
        const data = await response.json();
        monsters = data.monsters || [];
      } else {
        error = 'Failed to load creatures.';
      }
    } catch (e) {
      console.error(e);
      error = 'Failed to load creatures.';
    } finally {
        loading = false;
    }
  });
</script>

<h1 class="text-2xl font-bold mb-4">Creatures Library</h1>

{#if loading}
  <p>Loading...</p>
{:else if error}
  <p style="color: red">{error}</p>
{:else}
  <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
    {#each monsters as monster}
      <a href="/library/creatures/{monster.name}" class="border rounded p-4 flex flex-col items-center hover:bg-gray-50 transition">
        <img src="{monster.img_link}" alt="{monster.name}" class="w-16 h-16 object-contain mb-2" />
        <span class="font-bold text-center">{monster.name}</span>
        <span class="text-sm text-gray-500">Exp: {monster.experience}</span>
      </a>
    {/each}
  </div>
{/if}
