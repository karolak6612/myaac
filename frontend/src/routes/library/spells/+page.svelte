<script>
  import { onMount } from 'svelte';
  import { base } from '$app/paths';

  let spells = [];
  let vocations = {};
  let selectedVocation = 'all';
  let loading = true;
  let error = '';
  let currentAbortController = null;

  async function loadData() {
    if (currentAbortController) {
        currentAbortController.abort();
    }
    currentAbortController = new AbortController();

    loading = true;
    error = '';
    try {
      const response = await fetch(`/spells?api=1&vocation=${selectedVocation}`, { signal: currentAbortController.signal });
      if (response.ok) {
        const data = await response.json();
        spells = data.spells || [];
        vocations = data.vocations || {};
      } else {
        error = 'Failed to load spells.';
      }
    } catch (e) {
      if (e.name === 'AbortError') return;
      console.error(e);
      error = 'Failed to load spells.';
    } finally {
        if (!currentAbortController?.signal.aborted) {
            loading = false;
        }
    }
  }

  function changeVocation(newVocation) {
      selectedVocation = newVocation;
      loadData();
  }

  onMount(loadData);
</script>

<h1 class="text-2xl font-bold mb-4">Spells Library</h1>

{#if loading && spells.length === 0}
  <p>Loading...</p>
{:else if error}
  <p style="color: red">{error}</p>
{:else}
    <div class="mb-4">
        <label for="vocation" class="mr-2">Filter by Vocation:</label>
        <select id="vocation" bind:value={selectedVocation} on:change={() => changeVocation(selectedVocation)} class="border p-2 rounded">
            <option value="all">All Vocations</option>
            {#each Object.entries(vocations) as [id, name]}
                 {#if id !== '0'}
                    <option value={name.toLowerCase()}>{name}</option>
                 {/if}
            {/each}
        </select>
    </div>

    <table class="w-full border-collapse border bg-white shadow rounded overflow-hidden">
        <thead class="bg-gray-100">
            <tr>
                <th class="border p-3 text-left">Name</th>
                <th class="border p-3 text-left">Words</th>
                <th class="border p-3 text-center">Level</th>
                <th class="border p-3 text-center">Mana</th>
                <th class="border p-3 text-center">Group</th>
            </tr>
        </thead>
        <tbody>
            {#each spells as spell}
                <tr class="hover:bg-gray-50">
                    <td class="border p-3 font-bold text-blue-600">{spell.name}</td>
                    <td class="border p-3 italic text-gray-600">{spell.words}</td>
                    <td class="border p-3 text-center">{spell.level}</td>
                    <td class="border p-3 text-center">{spell.mana}</td>
                    <td class="border p-3 text-center capitalize">{spell.group_spell}</td>
                </tr>
            {/each}
        </tbody>
    </table>
{/if}
