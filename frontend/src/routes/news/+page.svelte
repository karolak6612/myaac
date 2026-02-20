<script>
    import { base } from '$app/paths';
    import DOMPurify from 'dompurify';
    export let data;

    $: ({ newsData } = data);
    $: news = newsData.news || [];
    $: tickers = newsData.tickers || [];
    $: article = newsData.article || null;

    function formatDate(timestamp) {
        if (!timestamp) return '';
        return new Date(timestamp * 1000).toLocaleString();
    }
</script>

{#if tickers.length > 0}
    <section class="tickers bg-gray-100 p-4 rounded mb-4">
        <h2 class="text-xl font-bold mb-2">Tickers</h2>
        <ul>
            {#each tickers as ticker}
                <li class="border-b py-2 flex items-center">
                    {#if ticker.icon}
                        <img src="{base}/images/icons/{ticker.icon}.gif" alt="Icon" class="mr-2" />
                    {/if}
                    <span>{ticker.body_short}</span>
                    <small class="ml-auto text-gray-500">{formatDate(ticker.date)}</small>
                </li>
            {/each}
        </ul>
    </section>
{/if}

{#if article}
    <article class="featured-article bg-white p-6 rounded shadow mb-6">
        <h2 class="text-2xl font-bold mb-4">{article.title}</h2>
        {#if article.article_image}
            <img src="{base}/{article.article_image}" alt="Article Image" class="mb-4 w-full h-auto" />
        {/if}
        <div class="prose max-w-none">
            {@html DOMPurify.sanitize(article.article_text)}
        </div>
    </article>
{/if}

<div class="news-feed">
    {#each news as item}
        <article class="news-item bg-white p-6 rounded shadow mb-6">
            <header class="flex items-center mb-4 border-b pb-2">
                {#if item.icon}
                    <img src="{base}/images/icons/{item.icon}.gif" alt="Icon" class="mr-2" />
                {/if}
                <h3 class="text-xl font-bold flex-grow">{item.title}</h3>
                <div class="text-sm text-gray-500">
                    {formatDate(item.date)}
                    {#if item.author}
                        by {item.author}
                    {/if}
                </div>
            </header>
            <div class="prose max-w-none">
                {@html DOMPurify.sanitize(item.body)}
            </div>
            {#if item.comments}
                <div class="mt-4 pt-2 border-t text-sm text-right">
                    <a href="{item.comments}" class="text-blue-500 hover:underline">Read Comments</a>
                </div>
            {/if}
        </article>
    {/each}
</div>
