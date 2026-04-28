<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

function extractInfoFromMarkdown($fileContent)
{
    $info = [
        'title' => '',
        'category' => '',
        'track' => '',
        'date' => ''
    ];

    if (preg_match('/\[info_title\]: (.+)/', $fileContent, $matches)) {
        $info['title'] = trim(urldecode($matches[1]));
    }

    if (preg_match('/\[info_category\]: (.+)/', $fileContent, $matches)) {
        $info['category'] = trim(urldecode($matches[1]));
    }

    if (preg_match('/\[info_track\]: (.+)/', $fileContent, $matches)) {
        $info['track'] = trim(urldecode($matches[1]));
    }

    if (preg_match('/\[info_date\]: (.+)/', $fileContent, $matches)) {
        $info['date'] = trim(urldecode($matches[1]));
    }

    return $info;
}

function stripCategoryPrefix($category)
{
    return preg_replace('/^\d+-/', '', $category);
}

function getMarkdownPages($directory)
{
    $pages = [];
    $categoryOrder = [];

    if ($handle = opendir($directory)) {
        $entries = [];
        while (false !== ($entry = readdir($handle))) {
            if (preg_match('/^(\d+)-.+\.md$/', $entry, $matches)) {
                $entries[$matches[1]] = $entry;
            }
        }
        ksort($entries, SORT_NUMERIC);

        foreach ($entries as $entry) {
            $filePath = $directory . '/' . $entry;
            $fileContent = file_get_contents($filePath);
            $info = extractInfoFromMarkdown($fileContent);

            $date = !empty($info['date']) ? $info['date'] : date('Y-m-d', filemtime($filePath));

            $strippedCategory = stripCategoryPrefix($info['category']);

            $page = [
                'id' => pathinfo($entry, PATHINFO_FILENAME),
                'title' => $info['title'],
                'file' => $entry,
                'category' => $strippedCategory,
                'trackurl' => $info['track'],
                'date' => $date
            ];

            $pages[] = $page;

            if (!empty($info['category']) && !isset($categoryOrder[$strippedCategory])) {
                if (preg_match('/^(\d+)-/', $info['category'], $catMatches)) {
                    $categoryOrder[$strippedCategory] = (int)$catMatches[1];
                } else {
                    $categoryOrder[$strippedCategory] = PHP_INT_MAX;
                }
            }
        }
        closedir($handle);
    }

    usort($pages, function ($a, $b) use ($categoryOrder) {
        if ($a['category'] !== $b['category']) {
            $indexA = $categoryOrder[$a['category']] ?? PHP_INT_MAX;
            $indexB = $categoryOrder[$b['category']] ?? PHP_INT_MAX;
            return $indexA - $indexB;
        }

        return strtotime($b['date']) - strtotime($a['date']);
    });

    return $pages;
}

function generateJsonFromMarkdownFiles($directory)
{
    $pages = getMarkdownPages($directory);

    $jsonData = [
        'title' => 'Douxx.tech | Blog',
        'pages' => $pages
    ];

    return json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}

function generateRssFromMarkdownFiles($directory)
{
    $pages = getMarkdownPages($directory);

    usort($pages, function ($a, $b) {
        $timeA = strtotime($a['date']);
        $timeB = strtotime($b['date']);
        return $timeB - $timeA;
    });

    $rssFeed = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"></rss>');
    $rssFeed->addAttribute('xmlns:xmlns:atom', 'http://www.w3.org/2005/Atom');

    $channel = $rssFeed->addChild('channel');

    $channel->addChild('title', 'Douxx.tech | Blog');
    $channel->addChild('link', 'https://douxx.blog');
    $channel->addChild('description', 'RSS feed for Douxx.blog');

    $atomNS = 'http://www.w3.org/2005/Atom';
    $atomLink = $channel->addChild('atom:link', null, $atomNS);
    $atomLink->addAttribute('href', 'https://douxx.blog/?rss');
    $atomLink->addAttribute('rel', 'self');
    $atomLink->addAttribute('type', 'application/rss+xml');

    $channel->addChild('language', 'en-us');
    $channel->addChild('lastBuildDate', date(DATE_RSS));

    foreach ($pages as $page) {
        $filePath = $directory . '/' . $page['file'];
        $fileContent = file_get_contents($filePath);

        $lines = explode("\n", $fileContent);
        $lines = array_filter($lines, fn($line) => !preg_match('/^\[info_/', $line));
        $content = implode("\n", $lines);

        $content = preg_replace('/!\[.*?\]\(.*?\)/', '', $content);
        $content = preg_replace('/<img[^>]*>/', '', $content);

        $content = preg_replace("/\s+/", " ", $content);
        $content = trim($content);

        $content = preg_replace('/[`*_#]/', '', $content);

        if (mb_strlen($content) > 200) {
            $content = mb_substr($content, 0, 200);
            $lastSpace = mb_strrpos($content, ' ');
            if ($lastSpace !== false) {
                $content = mb_substr($content, 0, $lastSpace);
            }
            $content .= '…';
        }

        $pubDate = date(DATE_RSS, strtotime($page['date']));

        $item = $channel->addChild('item');
        $item->addChild('title', htmlspecialchars($page['title'], ENT_XML1, 'UTF-8'));
        $item->addChild('link', 'https://douxx.blog/?p=' . urlencode($page['id']));

        $guid = $item->addChild('guid', 'https://douxx.blog/?p=' . urlencode($page['id']));
        $guid->addAttribute('isPermaLink', 'true');

        $item->addChild('pubDate', $pubDate);

        if (!empty($page['category'])) {
            $item->addChild('category', htmlspecialchars($page['category'], ENT_XML1, 'UTF-8'));
        }

        $desc = $item->addChild('description');
        $descNode = dom_import_simplexml($desc);
        $ownerDoc = $descNode->ownerDocument;
        $descNode->appendChild($ownerDoc->createCDATASection($content));
    }

    return $rssFeed->asXML();
}

function extractCoverImage($fileContent)
{
    if (preg_match('/!\[.*?\]\((.+?)\)/', $fileContent, $matches)) {
        return trim($matches[1]);
    }
    return '';
}

function generateSitemapFromMarkdownFiles($directory)
{
    $pages = getMarkdownPages($directory);

    $urlset = new SimpleXMLElement(
        '<?xml version="1.0" encoding="UTF-8"?>' .
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ' .
            'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"></urlset>'
    );

    foreach ($pages as $page) {
        $fileContent = file_get_contents($directory . '/' . $page['file']);
        $cover = extractCoverImage($fileContent);

        $url = $urlset->addChild('url');
        $url->addChild('loc', 'https://douxx.blog/?p=' . urlencode($page['id']));
        $url->addChild('lastmod', date('Y-m-d', strtotime($page['date'])));
        $url->addChild('changefreq', 'monthly');
        $url->addChild('priority', '0.8');

        if (!empty($cover)) {
            $imageNS = 'http://www.google.com/schemas/sitemap-image/1.1';
            $image = $url->addChild('image:image', null, $imageNS);
            $image->addChild('image:loc', htmlspecialchars($cover, ENT_XML1, 'UTF-8'), $imageNS);
        }
    }

    return $urlset->asXML();
}

$directory = __DIR__;

if (isset($_GET['rss'])) {
    header('Content-Type: application/xml');
    echo generateRssFromMarkdownFiles($directory);
} elseif (isset($_GET['sitemap'])) {
    header('Content-Type: application/xml');
    echo generateSitemapFromMarkdownFiles($directory);
} else {
    header('Content-Type: application/json');
    echo generateJsonFromMarkdownFiles($directory);
}
