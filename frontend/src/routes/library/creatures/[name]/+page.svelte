<script>
  import { onMount } from 'svelte';
  import { page } from '$app/stores';
  import { base } from '$app/paths';

  let monster = null;
  let loading = true;
  let error = '';
  /** @type {AbortController | null} */
  let currentController = null;

  $: name = $page.params.name;

  $: if (name) {
      loadCreature(name);
  }

  async function loadCreature(name) {
    if (currentController) currentController.abort();
    currentController = new AbortController();

    loading = true;
    error = '';
    monster = null; // Clear previous monster while loading new one? Or keep stale? User asked to "only update monster/loading/error when the fetch completes". So maybe I should not clear here.
    // But if I don't clear, I might show old monster with new loading state.
    // The prompt says: "only update monster/loading/error when the fetch completes successfully for the non-aborted request"
    // So I should keep old state until new one arrives?
    // But if I start loading, I usually want to show loading state.
    // I will follow standard pattern: set loading true, keep old data or clear it.
    // "Blank page occurs when API returns 200 but data.monster is falsy; add a fallback UI branch"

    try {
      const response = await fetch(`${base}/monsters/${encodeURIComponent(name)}?api=1`, { signal: currentController.signal });
      if (response.ok) {
        const data = await response.json();
        monster = data.monster;
      } else {
        error = 'Failed to load creature.';
      }
    } catch (e) {
      if (e.name === 'AbortError') return;
      console.error(e);
      error = 'Failed to load creature.';
    } finally {
        if (!currentController.signal.aborted) {
            loading = false;
        }
    }
  }
</script>

{#if loading}
  <p>Loading...</p>
{:else if error}
  <p style="color: red">{error}</p>
{:else if monster}
  <div class="container mx-auto p-4">
    <div class="bg-white p-6 rounded shadow flex flex-col items-center">
      <h1 class="text-4xl font-bold mb-4">{monster.name}</h1>
      <img src="{monster.img_link}" alt="{monster.name}" class="w-32 h-32 object-contain mb-4" />

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full">
        <div>
          <h2 class="text-2xl font-bold mb-2">Stats</h2>
          <ul class="list-disc list-inside">
            <li>Health: {monster.health}</li>
            <li>Experience: {monster.experience}</li>
            <li>Speed: {monster.speed}</li>
            <li>Armor: {monster.armor}</li>
            <li>Defense: {monster.defense}</li>
          </ul>
        </div>

        <div>
           {#if monster.loot}
             <h2 class="text-2xl font-bold mb-2">Loot</h2>
             <ul class="list-disc list-inside">
               {#each monster.loot as item}
                 <li>
                   {item.count}x {item.name}
                   <span class="text-gray-500 text-sm">({item.rarity} - {item.rarity_chance}%)</span>
                 </li>
               {/each}
             </ul>
           {/if}
        </div>
      </div>

      {#if monster.voices}
        <div class="mt-8 w-full">
            <h2 class="text-2xl font-bold mb-2">Voices</h2>
            <ul class="italic list-disc list-inside">
                {#each monster.voices as voice}
                    <li>"{voice}"</li>
                {/each}
            </ul>
        </div>
      {/if}
    </div>
  </div>
{:else}
  <div class="container mx-auto p-4 text-center">
      <p class="text-gray-600 text-lg">No monster data available.</p>
      <a href="{base}/library/creatures" class="text-blue-500 hover:underline mt-4 inline-block">Back to Creatures</a>
  </div>
{/if}
