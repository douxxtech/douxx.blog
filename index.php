<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Douxx.tech | Blog</title>
    <link href="https://douxx.tech/assets/img/icon.png" rel="icon">
    <?php
    $defaultTitle = "Douxx.tech | Blog";
    $defaultDescription = "Your go-to resource for coding tips, tech tutorials, and blog posts about building projects";

    if (isset($_GET['p'])) {
        $param = $_GET['p'];
        $filePath = "./articles/{$param}.md";

        if (file_exists($filePath)) {
            $fileContent = file_get_contents($filePath);

            preg_match('/\[info_title\]: (.*)/', $fileContent, $titleMatches);
            preg_match('/\[info_category\]: (.*)/', $fileContent, $categoryMatches);
            preg_match('/\[info_description\]: (.*)/', $fileContent, $descriptionMatches);

            $title = !empty($titleMatches[1]) ? urldecode($titleMatches[1]) : "Douxx.tech";
            $category = !empty($categoryMatches[1]) ? urldecode($categoryMatches[1]) : "Blog";
            $description = !empty($descriptionMatches[1])
                ? urldecode($descriptionMatches[1])
                : "Learn more about {$title} in the {$category} section on Douxx.blog!";

            echo "<meta property='og:title' content='{$title} | Douxx.blog'>";
            echo "<meta property='og:description' content=\"{$description}\">";
        } else {
            echo "<meta property='og:title' content='{$defaultTitle}'>";
            echo "<meta property='og:description' content='{$defaultDescription}'>";
        }
    } else {
        echo "<meta property='og:title' content='{$defaultTitle}'>";
        echo "<meta property='og:description' content='{$defaultDescription}'>";
    }
    ?>

    <meta property="og:image" content="https://douxx.tech/assets/img/icon.png" />
    <meta name="twitter:card" content="summary" />
    <meta property="og:url" content="https://douxx.blog">
    <meta property="og:type" content="website" />
    <meta name='description' content='Here will be posted some tutorials or.. yea idk some random shit basically.'>
    <meta name='copyright' content='douxx.tech'>
    <meta name="robots" content="index, follow">
    <meta name='language' content='EN'>
    <meta name='author' content='douxx.tech, douxx@douxx.tech'>
    <meta name='designer' content='douxx.tech'>
    <meta name='reply-to' content='contact@douxx.tech'>
    <meta name='owner' content='douxx'>
    <meta name='url' content='https://douxx.blog'>
    <meta name='pagename' content='Douxx\' s Blog'>
    <meta name='distribution' content='Global'>
    <meta name='rating' content='General'>
    <meta name='target' content='technology'>
    <meta name='og:site_name' content="@douxxtech">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/marked/4.3.0/marked.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/atom-one-dark.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/opendyslexic" rel="stylesheet">

    <link href="assets/css/styles.css?3" rel="stylesheet">
</head>

<body>
    <header>
        <div class="header-left">
            <button class="menu-button"><i class="ri-menu-line"></i></button>
            <div class="logo">
                <i class="ri-news-line"></i>
                <span id="project-name">Blog</span>
            </div>
        </div>

        <div class="search-container">
            <i class="ri-search-line search-icon"></i>
            <input type="text" class="search-input" placeholder="Search articles...">
        </div>

        <div class="header-right">
            <button class="dyslexia-toggle" id="dyslexiaToggle" title="Toggle Dyslexia Mode">
                <i class="ri-font-size"></i>
            </button>
            <button class="read-mode-toggle" id="readModeToggle" title="Toggle Read Mode">
                <i class="ri-book-read-line"></i>
            </button>
        </div>
    </header>

    <div class="content-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div id="project-name-mobile">Blog</div> <!-- only shown on mobile -->
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

    <script src="assets/js/external-links.js?3"></script>
    <script src="assets/js/loader.js?3"></script>
    <script src="assets/js/readmode.js?3"></script>
</body>

</html>