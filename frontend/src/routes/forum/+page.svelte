<script>
  import { onMount } from 'svelte';
  import { base } from '$app/paths';

  let boards = [];
  let error = '';
  let loading = true;

  onMount(async () => {
    try {
      const response = await fetch(`${base}/forum?api=1`);
      if (response.ok) {
        const data = await response.json();
        boards = data.boards || [];
      } else {
        error = 'Failed to load forum boards.';
      }
    } catch (e) {
      console.error(e);
      error = 'Failed to load forum boards.';
    } finally {
        loading = false;
    }
  });

  function formatDate(timestamp) {
        if (!timestamp) return 'Never';
        return new Date(timestamp * 1000).toLocaleString();
    }
</script>

<h1 class="text-2xl font-bold mb-4">Forum</h1>

{#if loading}
  <p>Loading...</p>
{:else if error}
  <p style="color: red">{error}</p>
{:else}
  <div class="overflow-x-auto">
    <table class="w-full border-collapse border bg-white shadow rounded">
        <thead class="bg-gray-100">
            <tr>
                <th class="border p-3 text-left w-1/2">Board</th>
                <th class="border p-3 text-center">Threads</th>
                <th class="border p-3 text-center">Posts</th>
                <th class="border p-3 text-left">Last Post</th>
            </tr>
        </thead>
        <tbody>
            {#each boards as board}
                <tr class="hover:bg-gray-50">
                    <td class="border p-3">
                        <a href="/forum/board/{board.id}" class="font-bold text-blue-600 hover:underline">{board.name}</a>
                        <p class="text-sm text-gray-600">{board.description}</p>
                    </td>
                    <td class="border p-3 text-center">{board.threads}</td>
                    <td class="border p-3 text-center">{board.posts}</td>
                    <td class="border p-3 text-sm">
                        {#if board.last_post && board.last_post.name}
                            {formatDate(board.last_post.date)}<br/>
                            by {board.last_post.name}
                        {:else}
                            No posts
                        {/if}
                    </td>
                </tr>
            {/each}
        </tbody>
    </table>
  </div>
{/if}
