<script>
  import { onMount } from 'svelte';
  import { page } from '$app/stores';
  import { base } from '$app/paths';
  import DOMPurify from 'dompurify';

  let threadData = null;
  let error = '';
  let loading = true;
  let currentPage = 0;

  $: id = $page.params.id;
  $: if (id) loadData(0);

  let currentAbortController = null;

  async function loadData(pageIndex = 0) {
    if (currentAbortController) {
      currentAbortController.abort();
    }
    currentAbortController = new AbortController();

    loading = true;
    error = '';
    try {
      const response = await fetch(`/forum/thread/${id}/${pageIndex}?api=1`, { signal: currentAbortController.signal });
      if (response.ok) {
        threadData = await response.json();
        currentPage = threadData.page;
      } else {
        error = 'Failed to load thread data.';
      }
    } catch (e) {
      if (e.name === 'AbortError') return;
      console.error(e);
      error = 'Failed to load thread data.';
    } finally {
        if (!currentAbortController?.signal.aborted) {
            loading = false;
        }
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
{:else if threadData}
  <h1 class="text-2xl font-bold mb-4">{threadData.thread.topic}</h1>
  <div class="mb-4 text-sm text-gray-500">
      <a href="/forum" class="hover:underline">Forum</a> &gt;
      <a href="/forum/board/{threadData.thread.section.id}" class="hover:underline">{threadData.thread.section.name}</a> &gt;
      {threadData.thread.topic}
  </div>

  <div class="space-y-4">
      {#each threadData.posts as post}
        <div class="bg-white border rounded shadow p-4 flex flex-col md:flex-row gap-4">
            <div class="md:w-1/4 border-r pr-4 text-center md:text-left">
                <div class="font-bold text-lg mb-1">{post.author.name}</div>
                <div class="text-sm text-gray-600 mb-2">{post.author.vocation} (Level {post.author.level})</div>
                {#if post.author.group}
                    <div class="text-xs text-blue-600 font-bold mb-1">{post.author.group}</div>
                {/if}
                 {#if post.author.outfit}
                    <div class="my-2 flex justify-center md:justify-start">
                        {@html DOMPurify.sanitize(post.author.outfit)}
                    </div>
                {/if}
                <div class="text-xs text-gray-500">Posts: {post.author.posts_count}</div>
            </div>
            <div class="md:w-3/4 flex flex-col">
                <div class="text-xs text-gray-400 mb-2 border-b pb-1 flex justify-between">
                    <span>{formatDate(post.date)}</span>
                    <span>#{post.id}</span>
                </div>
                <div class="prose max-w-none flex-grow">
                    {@html DOMPurify.sanitize(post.content)}
                </div>
                {#if post.edited}
                    <div class="mt-4 pt-2 border-t text-xs text-gray-400 italic">
                        Edited by {post.edited.by} on {formatDate(post.edited.date)}
                    </div>
                {/if}
            </div>
        </div>
      {/each}
  </div>

  <div class="pagination mt-4 flex gap-2">
      <button disabled={currentPage <= 0} on:click={() => loadData(currentPage - 1)} class="border p-2 bg-gray-200 hover:bg-gray-300 disabled:opacity-50">Previous</button>
      <span class="p-2">Page {currentPage + 1} of {threadData.total_pages}</span>
      <button disabled={currentPage >= threadData.total_pages - 1} on:click={() => loadData(currentPage + 1)} class="border p-2 bg-gray-200 hover:bg-gray-300 disabled:opacity-50">Next</button>
  </div>
{/if}
