<script>
  import { onMount } from 'svelte';
  import { user, loggedIn, loading } from '$lib/stores/auth';
  import { goto } from '$app/navigation';
  import { page } from '$app/stores';
  import { base } from '$app/paths';
  import DOMPurify from 'dompurify';

  let content = '';
  let currentPage = 'dashboard';
  let error = '';

  $: if (!$loading && (!$loggedIn || !$user?.is_admin)) {
    goto('/account/login');
  }

  $: queryPage = $page.url.searchParams.get('p') || 'dashboard';

  async function loadPage(p) {
    try {
      const response = await fetch(`${base}/admin/?api=1&p=${p}`);
      if (response.ok) {
        const data = await response.json();
        content = data.content;
        currentPage = data.page;

      } else {
        error = 'Failed to load admin page.';
      }
    } catch (e) {
      console.error(e);
      error = 'Failed to load admin page.';
    }
  }

  $: if ($loggedIn) {
      loadPage(queryPage);
  }
</script>

<div class="flex h-screen bg-gray-100">
  <!-- Sidebar -->
  <aside class="w-64 bg-gray-800 text-white flex flex-col">
    <div class="h-16 flex items-center justify-center border-b border-gray-700 font-bold text-xl">
      Admin Panel
    </div>
    <nav class="flex-grow p-4 overflow-y-auto">
      <ul class="space-y-2">
        <li><a href="/admin?p=dashboard" class="block py-2 px-4 hover:bg-gray-700 rounded {currentPage === 'dashboard' ? 'bg-gray-700' : ''}">Dashboard</a></li>
        <li><a href="/admin?p=news" class="block py-2 px-4 hover:bg-gray-700 rounded {currentPage === 'news' ? 'bg-gray-700' : ''}">News</a></li>
        <li><a href="/admin?p=account" class="block py-2 px-4 hover:bg-gray-700 rounded {currentPage === 'account' ? 'bg-gray-700' : ''}">Accounts</a></li>
        <li><a href="/admin?p=players" class="block py-2 px-4 hover:bg-gray-700 rounded {currentPage === 'players' ? 'bg-gray-700' : ''}">Players</a></li>
        <li><a href="/admin?p=settings" class="block py-2 px-4 hover:bg-gray-700 rounded {currentPage === 'settings' ? 'bg-gray-700' : ''}">Settings</a></li>
        <!-- Add more links as needed -->
        <li><a href="/" class="block py-2 px-4 hover:bg-gray-700 rounded mt-4 border-t border-gray-600 pt-4">Back to Site</a></li>
      </ul>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="flex-grow flex flex-col overflow-hidden">
     <header class="h-16 bg-white shadow flex items-center justify-between px-6">
        <h2 class="text-xl font-semibold capitalize">{currentPage}</h2>
        <div>
            {#if $user}
                <span>Logged in as: <strong>{$user.name}</strong></span>
            {/if}
        </div>
     </header>

     <div class="flex-grow overflow-auto p-6" id="admin-content">
        {#if error}
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{error}</span>
            </div>
        {:else}
            {@html DOMPurify.sanitize(content)}
        {/if}
     </div>
  </main>
</div>
