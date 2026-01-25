function handleExternalLinks() {
    const links = document.querySelectorAll('.content a');
    
    links.forEach(link => {
        const href = link.getAttribute('href');
        
        if (href && (href.startsWith('http://') || href.startsWith('https://'))) {
            const currentDomain = window.location.hostname;
            try {
                const linkDomain = new URL(href).hostname;
                
                if (linkDomain !== currentDomain) {
                    link.setAttribute('target', '_blank');
                }
            } catch (e) {
                link.setAttribute('target', '_blank');
            }
        }
    });
}