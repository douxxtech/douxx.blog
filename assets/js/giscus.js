function initGiscus() {
    const giscus = document.getElementById('giscus-container');
    if (giscus) giscus.innerHTML = '';

    const isLight = document.body.classList.contains('light-mode');

    const script = document.createElement('script');
    script.src = 'https://giscus.app/client.js';
    script.setAttribute('data-repo', 'douxxtech/douxx.blog');
    script.setAttribute('data-repo-id', 'R_kgDOODxnVA');
    script.setAttribute('data-category', 'General');
    script.setAttribute('data-category-id', 'DIC_kwDOODxnVM4C7uZI');
    script.setAttribute('data-mapping', 'url');
    script.setAttribute('data-strict', '0');
    script.setAttribute('data-reactions-enabled', '1');
    script.setAttribute('data-emit-metadata', '0');
    script.setAttribute('data-input-position', 'top');
    script.setAttribute('data-theme', isLight ? 'https://douxx.blog/assets/css/giscus-light.css?11' : 'https://douxx.blog/assets/css/giscus.css?11');
    script.setAttribute('data-lang', 'en');
    script.crossOrigin = 'anonymous';
    script.async = true;

    giscus.appendChild(script);
}
