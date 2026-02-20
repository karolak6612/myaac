<?php
/**
 * News
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Cache\Cache;
use MyAAC\News;

defined('MYAAC') or die('Direct access not allowed!');

function getNewsCategories() {
    global $db;
    $categories = array();
    foreach($db->query('SELECT `id`, `name`, `icon_id` FROM `' . TABLE_PREFIX . 'news_categories` WHERE `hide` != 1') as $cat)
    {
        $categories[$cat['id']] = array(
            'name' => $cat['name'],
            'icon_id' => $cat['icon_id']
        );
    }
    return $categories;
}

function getTickersData($limit) {
    global $db, $categories;
    $tickers_db = $db->query('SELECT * FROM `' . TABLE_PREFIX . 'news` WHERE `type` = ' . TICKER . ' AND `hide` != 1 ORDER BY `date` DESC LIMIT ' . $limit);
    $data = [];
    if($tickers_db->rowCount() > 0)
    {
        $raw_tickers = $tickers_db->fetchAll();
        foreach($raw_tickers as $ticker) {
            $data[] = [
                'id' => $ticker['id'],
                'title' => $ticker['title'],
                'body' => $ticker['body'],
                'date' => $ticker['date'],
                'category' => $ticker['category'],
                'icon' => isset($categories[$ticker['category']]) ? $categories[$ticker['category']]['icon_id'] : 0,
                'body_short' => short_text(strip_tags($ticker['body']), 100),
                'hide' => $ticker['hide']
            ];
        }
    }
    return $data;
}

function getFeaturedArticleData() {
    global $db;
    $featured_article_db =$db->query('SELECT `id`, `title`, `article_text`, `article_image`, `hide` FROM `' . TABLE_PREFIX . 'news` WHERE `type` = ' . ARTICLE . ' AND `hide` != 1 ORDER BY `date` DESC LIMIT 1');
    if($featured_article_db->rowCount() > 0) {
        $article = $featured_article_db->fetch();
        return [
            'id' => $article['id'],
            'title' => $article['title'],
            'text' => $article['article_text'],
            'image' => $article['article_image'],
            'hide' => $article['hide'],
            'read_more' => getLink('news/archive/') . $article['id']
        ];
    }
    return null;
}

function getNewsData($limit) {
    global $db, $categories;
    $limit = (int)$limit;
    $newsQuery = 'SELECT n.*, p.name AS author_name FROM ' . $db->tableName(TABLE_PREFIX . 'news') . ' n LEFT JOIN ' . $db->tableName('players') . ' p ON n.player_id = p.id WHERE n.type = ' . NEWS . ' AND n.hide != 1 ORDER BY n.date DESC LIMIT ' . $limit;
    $newses = $db->query($newsQuery);
    $data = [];
    if($newses->rowCount() > 0)
    {
        $raw_news = $newses->fetchAll();
        foreach($raw_news as $news)
        {
            $item = [
                'id' => $news['id'],
                'title' => stripslashes($news['title']),
                'body' => $news['body'],
                'date' => $news['date'],
                'category' => $news['category'],
                'icon' => isset($categories[$news['category']]) ? $categories[$news['category']]['icon_id'] : 0,
                'comments' => $news['comments'],
                'hide' => $news['hide']
            ];

            if (setting('core.news_author')) {
                $item['author'] = $news['author_name'] ?? '';
            }

            $data[] = $item;
        }
    }
    return $data;
}

$canEdit = hasFlag(FLAG_CONTENT_NEWS) || superAdmin();
$categories = getNewsCategories();

if(isset($_GET['archive']))
{
	$title = 'News Archive';

	// display big news by id
	if(isset($_GET['id']))
	{
		$id = (int)$_GET['id'];

		$field_name = 'date';
		if($id < 100000)
			$field_name = 'id';

		$news = $db->query('SELECT * FROM `'.TABLE_PREFIX . 'news` WHERE `hide` != 1 AND `' . $field_name . '` = ' . $id  . '');
		if($news->rowCount() == 1)
		{
			$news = $news->fetch();
			$author = '';
            if (setting('core.news_author')) {
                $query = $db->query('SELECT `name` FROM `players` WHERE id = ' . $db->quote($news['player_id']) . ' LIMIT 1;');
                if($query->rowCount() > 0) {
                    $query = $query->fetch();
                    $author = $query['name'];
                }
            }

			if (isApiRequest()) {
                $response = [
					'id' => $news['id'],
					'title' => stripslashes($news['title']),
					'body' => $news['body'],
					'date' => $news['date'],
					'category' => $news['category'],
					'icon' => $categories[$news['category']]['icon_id'],
					'comments' => $news['comments'],
				];
                if (setting('core.news_author')) {
                    $response['author'] = $author;
                }
				jsonResponse(['news' => $response]);
			}

			$content_ = $news['body'];
			$firstLetter = '';
			if($content_[0] != '<')
			{
				$tmp = $template_path.'/images/letters/' . $content_[0] . '.gif';
				if(file_exists($tmp)) {
					$firstLetter = '<img src="' . $tmp . '" alt="' . $content_[0] . '" border="0" align="bottom">';
					$content_ = $firstLetter . substr($content_, 1);
				}
			}

			$admin_options = '';
			if($canEdit) {
				$admin_options = '<br/><br/>' . $twig->render('admin.links.html.twig', ['page' => 'news', 'id' => $news['id'], 'hide' => $news['hide']]);
			}

			$twig->display('news.html.twig', array(
				'title' => stripslashes($news['title']),
				'content' => $content_ . $admin_options,
				'date' => $news['date'],
				'icon' => $categories[$news['category']]['icon_id'],
				'author' => setting('core.news_author') ? $author : '',
				'comments' => $news['comments'] != 0 ? getForumThreadLink($news['comments']) : null,
			));
		}
		else {
			if (isApiRequest()) {
				jsonResponse(['error' => "This news doesn't exist or is hidden."], 404);
			}
			echo "This news doesn't exist or is hidden.<br/>";
		}

		$twig->display('news.back_button.html.twig');
		return;
	}
	?>

	<?php

	$newses = array();
	$news_DB = $db->query('SELECT * FROM '.$db->tableName(TABLE_PREFIX . 'news').' WHERE `type` = 1 AND `hide` != 1 ORDER BY `date` DESC');
	foreach($news_DB as $news)
	{
		$newses[] = array(
			'link' => getLink('news/archive') . '/' . $news['id'],
			'icon_id' => $categories[$news['category']]['icon_id'],
			'title' => stripslashes($news['title']),
			'date' => $news['date'],
			'id' => $news['id']
		);
	}

	if (isApiRequest()) {
		jsonResponse(['archive' => $newses]);
	}

	$twig->display('news.archive.html.twig', array(
		'newses' => $newses
	));

	return;
}

header('X-XSS-Protection: 0');
$title = 'Latest News';

$cache = Cache::getInstance();

if (isApiRequest()) {
	$response = [];

    $tickers = getTickersData(setting('core.news_ticker_limit'));
    foreach ($tickers as &$t) unset($t['hide']);
    $response['tickers'] = $tickers;

    $article = getFeaturedArticleData();
    if ($article) {
        unset($article['hide']);
        $response['article'] = $article;
    }

    $news = getNewsData(setting('core.news_limit'));
    foreach ($news as &$n) unset($n['hide']);
    $response['news'] = $news;

	jsonResponse($response);
}

$news_cached = false;
if($cache->enabled())
	$news_cached = News::getCached(NEWS);

if(!$news_cached)
{
    $tickers = getTickersData(setting('core.news_ticker_limit'));
	$tickers_content = '';
	if(count($tickers) > 0)
	{
        foreach($tickers as &$ticker) {
			$ticker['hidden'] = $ticker['hide']; // map hide to hidden for Twig
		}

		$tickers_content = $twig->render('news.tickers.html.twig', array(
			'tickers' => $tickers,
			'canEdit' => $canEdit
		));
	}

	if($cache->enabled() && !$canEdit)
		$cache->set('news_' . $template_name . '_' . TICKER, $tickers_content, 60 * 60);

    $article = getFeaturedArticleData();
	$featured_article = '';
	if($article) {
        // Map keys for Twig
        $article_data = [
            'id' => $article['id'],
            'title' => $article['title'],
            'text' => $article['text'],
            'image' => $article['image'],
            'hide' => $article['hide'],
            'hidden' => $article['hide'],
            'read_more' => $article['read_more']
        ];

		if($twig->getLoader()->exists('news.featured_article.html.twig')) {
			$featured_article = $twig->render('news.featured_article.html.twig', array(
				'article' => $article_data,
				'canEdit' => $canEdit
			));
		}

		if($cache->enabled() && !$canEdit)
			$cache->set('news_' . $template_name . '_' . ARTICLE, $featured_article, 60 * 60);
	}
}
else {
	$tickers_content = News::getCached(TICKER);
	$featured_article = News::getCached(ARTICLE);
}

if(!$news_cached)
{
	ob_start();
    $news_items = getNewsData(setting('core.news_limit'));

	if(count($news_items) > 0)
	{
		foreach($news_items as $news)
		{
			$admin_options = '';
			if($canEdit) {
				$admin_options = '<br/><br/>' . $twig->render('admin.links.html.twig', ['page' => 'news', 'id' => $news['id'], 'hide' => $news['hide']]);
			}

			$content_ = $news['body'];
			$firstLetter = '';
			if($content_[0] != '<')
			{
				$tmp = $template_path.'/images/letters/' . $content_[0] . '.gif';
				if(file_exists($tmp)) {
					$firstLetter = '<img src="' . $tmp . '" alt="' . $content_[0] . '" border="0" align="bottom">';
					$content_ = $firstLetter . substr($content_, 1);
				}
			}

			$twig->display('news.html.twig', array(
				'id' => $news['id'],
				'title' => $news['title'],
				'content' => $content_ . $admin_options,
				'date' => $news['date'],
				'icon' => $categories[$news['category']]['icon_id'],
				'author' => isset($news['author']) ? $news['author'] : '',
				'comments' => $news['comments'] != 0 ? getForumThreadLink($news['comments']) : null,
				'hide'=> $news['hide']
			));
		}
	}

	$tmp_content = ob_get_contents();
	ob_end_clean();

	if($cache->enabled() && !$canEdit)
		$cache->set('news_' . $template_name . '_' . NEWS, $tmp_content, 60 * 60);

	echo $tmp_content;
}
else
	echo $news_cached;
