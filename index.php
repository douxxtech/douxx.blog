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
    $defaultImage = "https://douxx.tech/assets/img/icon.png";

    if (isset($_GET['p'])) {
        $param = $_GET['p'];
        $filePath = "./articles/{$param}.md";

        if (file_exists($filePath)) {
            $fileContent = file_get_contents($filePath);

            preg_match('/\[info_title\]: (.*)/', $fileContent, $titleMatches);
            preg_match('/\[info_category\]: (.*)/', $fileContent, $categoryMatches);
            preg_match('/\[info_description\]: (.*)/', $fileContent, $descriptionMatches);
            preg_match('/!\[hero\]\((.*?)\)/', $fileContent, $heroMatches);


            $title = !empty($titleMatches[1]) ? urldecode($titleMatches[1]) : "Douxx.tech";
            $category = !empty($categoryMatches[1]) ? preg_replace('/^\d+-/', '', urldecode($categoryMatches[1]) ): "Blog";
            $description = !empty($descriptionMatches[1])
                ? urldecode($descriptionMatches[1])
                : "Learn more about {$title} in the {$category} category on Douxx.blog!";
            $ogImage = !empty($heroMatches[1]) ? $heroMatches[1] : $defaultImage;

            echo "<meta property='og:title' content='{$title} | Douxx.blog'>";
            echo "<meta property='og:description' content=\"{$description}\">";
            echo "<meta property='og:image' content='{$ogImage}' />";
        } else {
            echo "<meta property='og:title' content='{$defaultTitle}'>";
            echo "<meta property='og:description' content='{$defaultDescription}'>";
            echo "<meta property='og:image' content='{$defaultImage}' />";
        }
    } else {
        echo "<meta property='og:title' content='{$defaultTitle}'>";
        echo "<meta property='og:description' content='{$defaultDescription}'>";
    }
    ?>

    <?php echo "<meta name='twitter:card' content='" . (!empty($heroMatches[1]) ? "summary_large_image" : "summary") . "' />"; ?>
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
    <link id="hljs-theme" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/atom-one-dark.min.css">
    <link id="hljs-theme-light" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/github.min.css" disabled>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/opendyslexic" rel="stylesheet">

    <link href="assets/css/vars.css?12" rel="stylesheet">
    <link href="assets/css/base.css?12" rel="stylesheet">
    <link href="assets/css/layout.css?12" rel="stylesheet">
    <link href="assets/css/content.css?12" rel="stylesheet">
    <link href="assets/css/modes.css?12" rel="stylesheet">
    <link href="assets/css/navigation.css?12" rel="stylesheet">
    <link href="assets/css/responsive.css?12" rel="stylesheet">
</head>

<body>
    <header>
        <div class="header-left">
            <button class="menu-button"><i class="ri-menu-line"></i></button>
            <div class="logo">
                <i class="ri-news-line"></i>
                <span id="project-name">Blog</span>
            </div>
            <div class="search-container">
                <i class="ri-search-line search-icon"></i>
                <input type="text" class="search-input" placeholder="Search articles...">
            </div>
        </div>

        <div class="header-right">
            <button class="dyslexia-toggle" id="dyslexiaToggle" title="Toggle Dyslexia Mode">
                <i class="ri-font-size"></i>
            </button>
            <button class="light-mode-toggle" id="lightModeToggle" title="Light mode">
                <i class="ri-sun-line"></i>
            </button>
        </div>
    </header>
    <div class="progress-bar" id="progress-bar"></div>

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
                <div id="giscus-container" class="giscus-container"></div>
                <div class="page-navigation" id="page-navigation">
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/config.js?12"></script>
    <script src="assets/js/renderer.js?12"></script>
    <script src="assets/js/sidebar.js?12"></script>
    <script src="assets/js/navigation.js?12"></script>
    <script src="assets/js/progressbar.js?12"></script>
    <script src="assets/js/giscus.js?12"></script>
    <script src="assets/js/loader.js?12"></script>
    <script src="assets/js/toggles.js?12"></script>
    <script src="assets/js/external-links.js?12"></script>
</body>

</html>