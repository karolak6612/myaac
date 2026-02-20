import type { PageLoad } from './$types';
import { base } from '$app/paths';

export const load: PageLoad = async ({ fetch }) => {
    try {
        // Use full URL if needed, but relative should work in browser
        const res = await fetch(`${base}/news?api=1`);
        if (res.ok) {
            const data = await res.json();
            return {
                newsData: data
            };
        }
    } catch (e) {
        console.error(e);
    }
    return {
        newsData: { news: [], tickers: [], article: null }
    };
};
