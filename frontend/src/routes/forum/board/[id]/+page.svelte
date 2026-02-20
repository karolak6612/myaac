<script>
  import { onMount } from 'svelte';
  import { page } from '$app/stores';
  import { base } from '$app/paths';

  let boardData = null;
  let error = '';
  let loading = true;
  let currentPage = 0;
  let abortController = null;

  $: id = $page.params.id;

  $: if (id) {
      currentPage = 0;
      loadData(0);
  }

  async function loadData(pageIndex = 0) {
    if (abortController) abortController.abort();
    abortController = new AbortController();

    loading = true;
    error = '';
    try {
      const response = await fetch(`${base}/forum/board/${id}/${pageIndex}?api=1`, { signal: abortController.signal });
      if (response.ok) {
        boardData = await response.json();
        currentPage = boardData.page;
      } else {
        error = 'Failed to load board data.';
      }
      loading = false;
    } catch (e) {
      if (e.name === 'AbortError') return;
      console.error(e);
      error = 'Failed to load board data.';
      loading = false;
    }
  }

  function formatDate(timestamp) {
    if (!timestamp) return 'Never';
    return new Date(timestamp * 1000).toLocaleString();
  }
</script>

{#if loading}
  <p>Loading...</p>
{:else if error}
  <p style="color: red">{error}</p>
{:else if boardData}
  <h1 class="text-2xl font-bold mb-4">{boardData.board.name}</h1>
  {#if boardData.can_post}
    <div class="mb-4">
        <a href="{base}/forum/new-thread?board={id}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">New Thread</a>
    </div>
  {/if}

  <div class="overflow-x-auto">
      <table class="w-full border-collapse border bg-white shadow rounded">
        <thead class="bg-gray-100">
            <tr>
                <th class="border p-3 text-left w-1/2">Thread</th>
                <th class="border p-3 text-center">Replies</th>
                <th class="border p-3 text-center">Views</th>
                <th class="border p-3 text-left">Last Post</th>
            </tr>
        </thead>
        <tbody>
            {#each boardData.threads as thread}
                <tr class="hover:bg-gray-50">
                    <td class="border p-3">
                        <a href="{base}/forum/thread/{thread.id}" class="font-bold text-blue-600 hover:underline">{thread.topic}</a>
                        <p class="text-sm text-gray-600">by {thread.author_name}</p>
                    </td>
                    <td class="border p-3 text-center">{thread.replies}</td>
                    <td class="border p-3 text-center">{thread.views}</td>
                    <td class="border p-3 text-sm">
                        {formatDate(thread.last_post_date)}<br/>
                        by {thread.last_post_author}
                    </td>
                </tr>
            {/each}
        </tbody>
    </table>
  </div>

  <div class="pagination mt-4 flex gap-2">
      <button disabled={currentPage <= 0} on:click={() => loadData(currentPage - 1)} class="border p-2 bg-gray-200 hover:bg-gray-300 disabled:opacity-50">Previous</button>
      <span class="p-2">Page {currentPage + 1} of {boardData.total_pages}</span>
      <button disabled={currentPage >= boardData.total_pages - 1} on:click={() => loadData(currentPage + 1)} class="border p-2 bg-gray-200 hover:bg-gray-300 disabled:opacity-50">Next</button>
  </div>
{/if}
