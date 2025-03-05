<?php
session_start();

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
    $metadata .= "[info_category]: " . urlencode($category) . "\n\n";

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
    <title>Create an Article</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --text-primary: #f8fafc;
            --text-secondary: #cbd5e1;
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-input: #334155;
            --success: #10b981;
            --warning: #f59e0b;
            --error: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1.5rem;
            line-height: 1.6;
        }

        .container {
            background-color: var(--bg-secondary);
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 800px;
            overflow: hidden;
        }

        .header {
            background-color: var(--primary);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .header h2 {
            font-weight: 600;
            font-size: 1.5rem;
            margin: 0;
        }

        .form-content {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
        }

        .input-icon {
            padding-left: 2.75rem !important;
        }

        input, textarea, button {
            width: 100%;
            padding: 0.75rem 1rem;
            border: none;
            border-radius: 0.5rem;
            font-family: inherit;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input, textarea {
            background-color: var(--bg-input);
            color: var(--text-primary);
            outline: none;
            border: 2px solid transparent;
        }

        input:focus, textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }

        textarea {
            min-height: 150px;
            resize: vertical;
        }

        button {
            background-color: var(--primary);
            color: white;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
        }

        button:hover {
            background-color: var(--primary-hover);
        }

        .preview-container {
            margin-top: 1.5rem;
        }

        .preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .preview-title {
            font-weight: 500;
            color: var(--text-secondary);
        }

        .preview {
            background-color: var(--bg-input);
            padding: 1rem;
            border-radius: 0.5rem;
            height: 250px;
            overflow-y: auto;
        }

        .preview h1, .preview h2, .preview h3, .preview h4, .preview h5, .preview h6 {
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }

        .preview p, .preview ul, .preview ol {
            margin-bottom: 1rem;
        }

        .preview a {
            color: var(--primary);
        }

        .preview code {
            background-color: rgba(0,0,0,0.2);
            padding: 0.2rem 0.4rem;
            border-radius: 0.25rem;
            font-family: monospace;
        }

        .preview blockquote {
            border-left: 3px solid var(--primary);
            padding-left: 1rem;
            margin-left: 0;
            color: var(--text-secondary);
        }

        .preview img {
            max-width: 100%;
            height: auto;
        }

        .preview table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }

        .preview th, .preview td {
            border: 1px solid var(--bg-secondary);
            padding: 0.5rem;
            text-align: left;
        }

        .success-message {
            background-color: var(--success);
            color: white;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .error-message {
            background-color: var(--error);
            color: white;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        @media (max-width: 640px) {
            .form-content {
                padding: 1.5rem;
            }
            
            .preview {
                height: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <i class="ri-article-line ri-lg"></i>
            <h2>Create an Article</h2>
        </div>
        
        <div class="form-content">
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($newFilename)): ?>
            <div class="success-message">
                <i class="ri-check-line"></i>
                <span>File saved successfully as <?php echo $newFilename; ?></span>
            </div>
            <?php endif; ?>
            
            <form action="" method="POST">
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrapper">
                        <i class="ri-lock-line"></i>
                        <input type="password" name="password" id="password" class="input-icon" placeholder="Enter your password" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="title">Title</label>
                    <div class="input-wrapper">
                        <i class="ri-heading"></i>
                        <input type="text" name="title" id="title" class="input-icon" placeholder="Article title" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="category">Category</label>
                    <div class="input-wrapper">
                        <i class="ri-price-tag-3-line"></i>
                        <input type="text" name="category" id="category" class="input-icon" placeholder="Article category" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="markdown-content">Content</label>
                    <textarea name="content" id="markdown-content" placeholder="Write your article in Markdown" required></textarea>
                    
                    <div class="preview-container">
                        <div class="preview-header">
                            <span class="preview-title">Preview</span>
                            <i class="ri-eye-line"></i>
                        </div>
                        <div class="preview" id="preview"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit">
                        <i class="ri-save-line"></i>
                        <span>Save Article</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.getElementById('markdown-content').addEventListener('input', function() {
            document.getElementById('preview').innerHTML = marked.parse(this.value);
        });
    </script>
</body>
</html>