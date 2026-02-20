<script>
  import { onMount } from 'svelte';
  import { base } from '$app/paths';

  let list = 'experience';
  let vocation = 'all';
  let page = 1;
  let data = null;
  let error = '';
  let loading = false;
  let currentAbortController = null;

  async function loadData() {
    if (currentAbortController) {
        currentAbortController.abort();
    }
    currentAbortController = new AbortController();

    loading = true;
    error = '';
    try {
      const response = await fetch(`/highscores/${list}/${vocation}/${page}?api=1`, { signal: currentAbortController.signal });
      if (response.ok) {
        data = await response.json();
      } else {
        error = 'Failed to load highscores.';
      }
    } catch (e) {
      if (e.name === 'AbortError') return;
      console.error(e);
      error = 'Failed to load highscores.';
    } finally {
        if (!currentAbortController?.signal.aborted) {
            loading = false;
        }
    }
  }

  function changeList(newList) {
      list = newList;
      page = 1;
      loadData();
  }

  function changeVocation(newVocation) {
      vocation = newVocation;
      page = 1;
      loadData();
  }

  onMount(loadData);
</script>

<h1 class="text-2xl font-bold mb-4">Highscores</h1>

{#if loading && !data}
  <p>Loading...</p>
{:else if error}
  <p style="color: red">{error}</p>
{:else if data}
    <div class="filters mb-4 flex gap-4">
        <select bind:value={list} on:change={() => changeList(list)} class="border p-2">
            {#each Object.entries(data.types) as [key, name]}
                <option value={key}>{name}</option>
            {/each}
        </select>

        <select bind:value={vocation} on:change={() => changeVocation(vocation)} class="border p-2">
            <option value="all">All Vocations</option>
            {#each Object.entries(data.vocations) as [id, name]}
                 {#if id !== '0'}
                    <option value={name.toLowerCase()}>{name}</option>
                 {/if}
            {/each}
        </select>
    </div>

    <table class="w-full border-collapse border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2">Rank</th>
                <th class="border p-2">Name</th>
                <th class="border p-2">Level</th>
                <th class="border p-2">Vocation</th>
                <th class="border p-2">Points</th>
            </tr>
        </thead>
        <tbody>
            {#each data.highscores as player}
                <tr>
                    <td class="border p-2 text-center">{player.rank}</td>
                    <td class="border p-2">{player.name}</td>
                    <td class="border p-2 text-center">{player.level}</td>
                    <td class="border p-2 text-center">{player.vocation}</td>
                    <td class="border p-2 text-center">{player.value}</td>
                </tr>
            {/each}
        </tbody>
    </table>

    <div class="pagination mt-4 flex gap-2">
        <button disabled={page <= 1} on:click={() => { page--; loadData(); }} class="border p-2 disabled:opacity-50">Previous</button>
        <span class="p-2">Page {page}</span>
        <button disabled={data && data.highscores && data.highscores.length < (data.perPage || 100)} on:click={() => { page++; loadData(); }} class="border p-2 disabled:opacity-50">Next</button>
    </div>
{/if}
