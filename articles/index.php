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

    if ($handle = opendir($directory)) {
        while (false !== ($entry = readdir($handle))) {
            if (preg_match('/^(\d+)-.+\.md$/', $entry, $matches)) {
                $filePath = $directory . '/' . $entry;
                $fileContent = file_get_contents($filePath);
                $info = extractInfoFromMarkdown($fileContent);

                $pages[] = [
                    'id' => pathinfo($entry, PATHINFO_FILENAME),
                    'title' => $info['title'],
                    'file' => $entry,
                    'category' => $info['category'],
                    'trackurl' => $info['track']
                ];
            }
        }
        closedir($handle);
    }

    usort($pages, function ($a, $b) {
        return $a['id'] <=> $b['id'];
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
