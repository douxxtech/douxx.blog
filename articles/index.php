<?php

function extractInfoFromMarkdown($fileContent) {
    $info = [
        'title' => '',
        'category' => '',
        'track' => ''
    ];

    if (preg_match('/\[info_title\]: (.+)/', $fileContent, $matches)) {
        $info['title'] = urldecode($matches[1]);
    }

    if (preg_match('/\[info_category\]: (.+)/', $fileContent, $matches)) {
        $info['category'] = urldecode($matches[1]);
    }

    if (preg_match('/\[info_track\]: (.+)/', $fileContent, $matches)) {
        $info['track'] = urldecode($matches[1]);
    }

    return $info;
}

function getMarkdownPages($directory) {
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

            $page = [
                'id' => pathinfo($entry, PATHINFO_FILENAME),
                'title' => $info['title'],
                'file' => $entry,
                'category' => $info['category'],
                'trackurl' => $info['track']
            ];
            
            $pages[] = $page;
            
            if (!empty($info['category']) && !in_array($info['category'], $categoryOrder)) {
                $categoryOrder[] = $info['category'];
            }
        }
        closedir($handle);
    }

    usort($pages, function ($a, $b) use ($categoryOrder) {
        if ($a['category'] !== $b['category']) {
            $indexA = array_search($a['category'], $categoryOrder);
            $indexB = array_search($b['category'], $categoryOrder);
            return $indexA - $indexB;
        }
        
        $idA = (int)preg_replace('/^(\d+)-.+$/', '$1', $a['id']);
        $idB = (int)preg_replace('/^(\d+)-.+$/', '$1', $b['id']);
        return $idA - $idB;
    });

    return $pages;
}

function generateJsonFromMarkdownFiles($directory) {
    $pages = getMarkdownPages($directory);

    $jsonData = [
        'title' => 'Douxx.tech | Blog',
        'pages' => $pages
    ];

    return json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}

function generateRssFromMarkdownFiles($directory) {
    $pages = getMarkdownPages($directory);
    $rssFeed = new SimpleXMLElement('<rss version="2.0"></rss>');
    $channel = $rssFeed->addChild('channel');
    $channel->addChild('title', 'Douxx.tech | Blog');
    $channel->addChild('link', 'https://douxx.blog');
    $channel->addChild('description', 'RSS feed for Douxx.blog blog');

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

        $fileDate = filemtime($filePath);
        $pubDate = date(DATE_RSS, $fileDate);

        $item = $channel->addChild('item');
        $item->addChild('title', htmlspecialchars($page['title']));
        $item->addChild('link', 'https://douxx.blog/?p=' . $page['id']);
        $item->addChild('guid', 'https://douxx.blog/?p=' . $page['id']); $item->addChild('pubDate', $pubDate);
        $desc = $item->addChild('description');
        $descNode = dom_import_simplexml($desc);
        $ownerDoc = $descNode->ownerDocument;
        $descNode->appendChild($ownerDoc->createCDATASection($content));
    }

    return $rssFeed->asXML();
}

$directory = __DIR__;

if (isset($_GET['rss'])) {
    header('Content-Type: application/rss+xml; charset=utf-8');
    echo generateRssFromMarkdownFiles($directory);
} else {
    header('Content-Type: application/json; charset=utf-8');
    echo generateJsonFromMarkdownFiles($directory);
}

?>
