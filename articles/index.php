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

function generateJsonFromMarkdownFiles($directory) {
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

    $jsonData = [
        'title' => 'Douxx.tech | Blog',
        'pages' => $pages
    ];

    return json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}

$directory = __DIR__;
echo generateJsonFromMarkdownFiles($directory);

?>