<?php
const PASSWORD = 'ThatOnePwd';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['password']) || $_POST['password'] !== PASSWORD) {
        die("Invalid password.");
    }

    if (!isset($_POST['title']) || !isset($_POST['category']) || !isset($_POST['content'])) {
        die("Missing required fields.");
    }

    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $content = trim($_POST['content']);

    $uploadDir = '../articles/';
    if (!is_dir($uploadDir)) {
        die("Upload directory does not exist.");
    }

    $files = glob($uploadDir . "*-*.md");
    $lastNumber = 0;
    foreach ($files as $f) {
        if (preg_match('/^(\d+)-/', basename($f), $matches)) {
            $lastNumber = max($lastNumber, (int)$matches[1]);
        }
    }
    $newNumber = $lastNumber + 1;

    $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($title));
    $slug = trim($slug, '-');

    $newFilename = "$newNumber-$slug.md";
    $newFilePath = $uploadDir . $newFilename;

    $metadata = "[info_title]: " . urlencode($title) . "\n";
    $metadata .= "[info_category]: " . urlencode($category) . "\n";
    $metadata .= "[info_date]: " . date('Y-m-d') . "\n";
    $metadata .= "[info_track]: https://track.dpip.lol/?id=" . urlencode($title) . "\n\n";

    if (file_put_contents($newFilePath, $metadata . $content) === false) {
        die("Failed to save file.");
    }

    echo "File saved successfully as $newFilename";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Article</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: rgb(15, 13, 12);
            color: rgb(235, 230, 225);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            padding: 2rem;
            line-height: 1.6;
        }

        ::selection {
            background-color: rgba(255, 154, 86, 0.3);
            color: rgb(235, 230, 225);
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        h1 {
            font-size: 1.75rem;
            font-weight: 500;
            margin-bottom: 2rem;
            color: rgb(255, 154, 86);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            color: rgb(190, 180, 170);
            font-weight: 400;
        }

        input, textarea {
            width: 100%;
            padding: 0.75rem;
            background-color: rgb(28, 26, 24);
            border: 1px solid rgb(45, 42, 40);
            border-radius: 4px;
            color: rgb(235, 230, 225);
            font-family: inherit;
            font-size: 0.9375rem;
            transition: border-color 0.2s ease;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: rgb(255, 154, 86);
        }

        input::placeholder, textarea::placeholder {
            color: rgb(190, 180, 170);
        }

        textarea {
            min-height: 400px;
            resize: vertical;
            font-family: 'Courier New', monospace;
            line-height: 1.6;
        }

        button {
            width: 100%;
            padding: 0.875rem;
            background-color: rgb(255, 154, 86);
            color: rgb(15, 13, 12);
            border: none;
            border-radius: 4px;
            font-size: 0.9375rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        button:hover {
            background-color: rgb(235, 134, 66);
        }

        .preview-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgb(45, 42, 40);
        }

        .preview-label {
            font-size: 0.875rem;
            color: rgb(190, 180, 170);
            margin-bottom: 0.75rem;
            display: block;
        }

        .preview {
            background-color: rgb(20, 18, 17);
            border: 1px solid rgb(45, 42, 40);
            border-radius: 4px;
            padding: 1.5rem;
            max-height: 500px;
            overflow-y: auto;
        }

        .preview h1, .preview h2, .preview h3 {
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            color: rgb(255, 154, 86);
        }

        .preview h1 { font-size: 1.75rem; }
        .preview h2 { font-size: 1.5rem; }
        .preview h3 { font-size: 1.25rem; }

        .preview p {
            margin-bottom: 1rem;
        }

        .preview code {
            background-color: rgb(28, 26, 24);
            padding: 0.2rem 0.4rem;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
        }

        .preview pre {
            background-color: rgb(28, 26, 24);
            border: 1px solid rgb(45, 42, 40);
            border-radius: 4px;
            padding: 1rem;
            overflow-x: auto;
            margin-bottom: 1rem;
        }

        .preview pre code {
            background: none;
            padding: 0;
        }

        .preview blockquote {
            border-left: 3px solid rgb(255, 154, 86);
            padding-left: 1rem;
            margin: 1rem 0;
            color: rgb(190, 180, 170);
        }

        .preview a {
            color: rgb(255, 154, 86);
            text-decoration: none;
            border-bottom: 1px solid transparent;
            transition: border-color 0.2s ease;
        }

        .preview a:hover {
            border-bottom-color: rgb(255, 154, 86);
        }

        .preview ul, .preview ol {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .preview li {
            margin-bottom: 0.5rem;
        }

        .success-message {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: rgb(16, 185, 129);
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            font-size: 0.9375rem;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            h1 {
                font-size: 1.5rem;
            }

            textarea {
                min-height: 300px;
            }

            .preview {
                max-height: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>New Article</h1>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($newFilename)): ?>
        <div class="success-message">
            ✓ File saved successfully as <?php echo htmlspecialchars($newFilename); ?>
        </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter password" required>
            </div>

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" placeholder="Article title" required>
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" name="category" id="category" placeholder="Category name" required>
            </div>

            <div class="form-group">
                <label for="markdown-content">Content</label>
                <textarea name="content" id="markdown-content" placeholder="Write your article in Markdown..." required></textarea>
            </div>

            <div class="preview-section">
                <span class="preview-label">Preview</span>
                <div class="preview" id="preview">
                    <p style="color: rgb(190, 180, 170);">Start typing to see preview...</p>
                </div>
            </div>

            <div class="form-group" style="margin-top: 2rem;">
                <button type="submit">Save Article</button>
            </div>
        </form>
    </div>

    <script>
        const markdownContent = document.getElementById('markdown-content');
        const preview = document.getElementById('preview');

        markdownContent.addEventListener('input', function() {
            const content = this.value.trim();
            if (content) {
                preview.innerHTML = marked.parse(content);
            } else {
                preview.innerHTML = '<p style="color: rgb(190, 180, 170);">Start typing to see preview...</p>';
            }
        });
    </script>
</body>
</html>