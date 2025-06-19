<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Douxx.tech | Blog</title>
    <link href="https://douxx.tech/assets/img/icon.png" rel="icon">
    <?php
    if (isset($_GET['p'])) {
        $param = $_GET['p'];

        if ($param === '1-intro') {
            echo "<meta property='og:title' content='Douxx.tech | Blog'>";
            echo "<meta property='og:description' content='Here will be posted some tutorials or.. yea idk some random shit basically.'>";
        } else {
            $filePath = "./articles/{$param}.md";

            if (file_exists($filePath)) {
                $fileContent = file_get_contents($filePath);
                preg_match('/\[info_title\]: (.*)/', $fileContent, $titleMatches);
                preg_match('/\[info_category\]: (.*)/', $fileContent, $categoryMatches);

                if (!empty($titleMatches) && !empty($categoryMatches)) {
                    $title = urldecode($titleMatches[1]);
                    $category = urldecode($categoryMatches[1]);

                    echo "<meta property='og:title' content='{$category} | Douxx Blog'>";
                    echo "<meta property='og:description' content=\"Learn more about {$title} on douxx's blog!\">";
                } else {
                    echo "<meta property='og:title' content='Douxx.tech | Blog'>";
                    echo "<meta property='og:description' content='Here will be posted some tutorials or.. yea idk some random shit basically.'>";
                }
            } else {
                echo "<meta property='og:title' content='Douxx.tech | Blog'>";
                echo "<meta property='og:description' content='Here will be posted some tutorials or.. yea idk some random shit basically.'>";
            }
        }
    } else {
        echo "<meta property='og:title' content='Douxx.tech | Blog'>";
        echo "<meta property='og:description' content='Here will be posted some tutorials or.. yea idk some random shit basically.'>";
    }
    ?>
    <meta property="og:image" content="https://douxx.tech/public/Douxx_dev.png" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta property="og:url" content="https://douxx.blog">
    <meta property="og:type" content="website"/>
    <meta name='description' content='Here will be posted some tutorials or.. yea idk some random shit basically.'>
    <meta name='copyright' content='douxx.tech'>
    <meta name="robots" content="index, follow">
    <meta name='language' content='EN'>
    <meta name='author' content='douxx.tech, douxx@douxx.tech'>
    <meta name='designer' content='douxx.tech'>
    <meta name='reply-to' content='contact@douxx.tech'>
    <meta name='owner' content='douxx'>
    <meta name='url' content='https://douxx.blog'>
    <meta name='pagename' content='U2A - URL to App'>
    <meta name='distribution' content='Global'>
    <meta name='rating' content='General'>
    <meta name='target' content='technology'>
    <meta name='og:site_name' content="@douxxpi">
    <meta name='og:email' content='contact@douxx.tech'>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/marked/4.3.0/marked.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/atom-one-dark.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <header>
        <button class="menu-button"><i class="ri-menu-line"></i></button>
        <div class="logo">
            <i class="ri-news-line"></i>
            <span>Blog ^-^</span>
        </div>
        <div class="search-container">
            <i class="ri-search-line search-icon"></i>
            <input type="text" class="search-input" placeholder="Search articles...">
        </div>
    </header>

    <div class="content-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div id="project-name">Project Name</div>
            </div>
            <nav class="sidebar-menu" id="sidebar-menu">
            </nav>
        </aside>

        <main class="main-content">
            <div class="content-container">
                <div class="content" id="content">
                    <div class="loading">Loading articles...</div>
                </div>
                <div class="page-navigation" id="page-navigation">
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/loader.js"></script>
    <script src="assets/js/neko.js"></script>
</body>
</html>
