<script>
  import { onMount } from 'svelte';
  import { page } from '$app/stores';

  let monster = null;
  let loading = true;
  let error = '';

  $: name = $page.params.name;

  onMount(async () => {
    loading = true;
    error = '';
    try {
      const response = await fetch(`/monsters/${name}?api=1`);
      if (response.ok) {
        const data = await response.json();
        monster = data.monster;
      } else {
        error = 'Failed to load creature.';
      }
    } catch (e) {
      console.error(e);
      error = 'Failed to load creature.';
    } finally {
        loading = false;
    }
  });
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
{/if}
