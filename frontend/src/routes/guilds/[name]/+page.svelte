<script>
  import { onMount } from 'svelte';
  import { page } from '$app/stores';
  import { base } from '$app/paths';
  import DOMPurify from 'dompurify';

  let guildData = null;
  let loading = true;
  let error = '';

  $: name = $page.params.name;
  $: if (name) loadGuild(name);

  async function loadGuild(guildName) {
    loading = true;
    error = '';
    guildData = null;
    try {
      const response = await fetch(`/guilds/${encodeURIComponent(guildName)}?api=1`);
      if (response.ok) {
        guildData = await response.json();
      } else {
        error = 'Failed to load guild data.';
      }
    } catch (e) {
      console.error(e);
      error = 'Failed to load guild data.';
    } finally {
        loading = false;
    }
  }
</script>

{#if loading}
  <p>Loading...</p>
{:else if error}
  <p style="color: red">{error}</p>
{:else if guildData}
    <div class="container mx-auto p-4">
        <header class="flex items-center mb-6">
            <img src="{base}/images/guilds/{guildData.guild.logo}" alt="Guild Logo" class="w-24 h-24 rounded shadow mr-6" />
            <div>
                <h1 class="text-4xl font-bold">{guildData.guild.name}</h1>
                <p class="text-gray-600">Created on {guildData.guild.creation_date_formatted}</p>
            </div>
        </header>

        <section class="mb-8 bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-4">Description</h2>
            <div class="prose max-w-none">{@html DOMPurify.sanitize(guildData.guild.description)}</div>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-bold mb-4">Members</h2>
            {#each guildData.members as rankGroup}
                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-2">{rankGroup.rank_name}</h3>
                    <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        {#each rankGroup.members as member}
                            <li class="bg-gray-50 p-3 rounded flex items-center shadow-sm">
                                <span class="w-3 h-3 rounded-full mr-2 {member.online ? 'bg-green-500' : 'bg-red-500'}"></span>
                                <div>
                                    <div class="font-bold">{member.name}</div>
                                    <div class="text-sm text-gray-600">{member.vocation} - Level {member.level}</div>
                                </div>
                            </li>
                        {/each}
                    </ul>
                </div>
            {/each}
        </section>

        {#if guildData.invites && guildData.invites.length > 0}
            <section class="mb-8">
                <h2 class="text-2xl font-bold mb-4">Invited Characters</h2>
                 <ul class="list-disc list-inside">
                    {#each guildData.invites as invite}
                        <li>{invite.name}</li>
                    {/each}
                 </ul>
            </section>
        {/if}
    </div>
{/if}
