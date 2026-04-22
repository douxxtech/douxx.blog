const renderer = new marked.Renderer();
const originalBlockquote = renderer.blockquote;

renderer.blockquote = function (quote) {
    const calloutMatch = quote.match(/<p>\[!(INFO|NOTE|TIP|IMPORTANT|WARNING|CAUTION|DANGER)\](.*?)(?:<\/p>)?/i);

    if (calloutMatch) {
        const type = calloutMatch[1].toLowerCase();
        let content = quote.replace(/<p>\[!.*?\](.*?)(?:<\/p>)?/i, '$1');

        let icon = '';
        switch (type) {
            case 'info':
            case 'note':
                icon = '<i class="ri-information-line"></i>';
                break;
            case 'tip':
                icon = '<i class="ri-lightbulb-line"></i>';
                break;
            case 'important':
                icon = '<i class="ri-alert-line"></i>';
                break;
            case 'warning':
            case 'caution':
                icon = '<i class="ri-error-warning-line"></i>';
                break;
            case 'danger':
                icon = '<i class="ri-close-circle-line"></i>';
                break;
            default:
                icon = '<i class="ri-information-line"></i>';
        }

        return `<div class="callout callout-${type}">${icon}<div class="callout-content">${content}</div></div>`;
    }

    return originalBlockquote.call(this, quote);
};

renderer.code = function(code, language) {
    const highlighted = language && hljs.getLanguage(language)
        ? hljs.highlight(code, { language }).value
        : hljs.highlightAuto(code).value;

    return `
        <div class="code-block">
            <button class="copy-button" onclick="copyCode(this)"><i class="ri-clipboard-line"></i></button>
            <pre><code class="hljs ${language || ''}">${highlighted}</code></pre>
        </div>
    `;
};

marked.setOptions({
    renderer: renderer,
    highlight: function (code, lang) {
        if (lang && hljs.getLanguage(lang)) {
            return hljs.highlight(code, { language: lang }).value;
        }
        return hljs.highlightAuto(code).value;
    },
    breaks: true,
    gfm: true
});

function copyCode(btn) {
    const code = btn.nextElementSibling.querySelector('code').innerText;
    navigator.clipboard.writeText(code).then(() => {
        btn.innerHTML = '<i class="ri-check-line"></i>';
        setTimeout(() => btn.innerHTML = '<i class="ri-clipboard-line"></i>', 2000);
    });
}

function executeScripts(container) {
    const scripts = container.querySelectorAll('script');

    scripts.forEach(oldScript => {
        const newScript = document.createElement('script');

        Array.from(oldScript.attributes).forEach(attr => {
            newScript.setAttribute(attr.name, attr.value);
        });

        newScript.textContent = oldScript.textContent;

        oldScript.replaceWith(newScript);
    });
}