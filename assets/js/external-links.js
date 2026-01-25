function handleExternalLinks() {
    const links = document.querySelectorAll('.content a');
    
    links.forEach(link => {
        const href = link.getAttribute('href');
        
        if (href && (href.startsWith('http://') || href.startsWith('https://'))) {
            const currentDomain = window.location.hostname;
            const linkDomain = new URL(href).hostname;
            
            if (linkDomain !== currentDomain) {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    showExternalLinkWarning(href);
                });
            }
        }
    });
}

function showExternalLinkWarning(url) {
    const modal = document.createElement('div');
    modal.className = 'external-link-modal';
    modal.innerHTML = `
        <div class="external-link-overlay"></div>
        <section class="external-link-content">
            <i class="ri-external-link-line"></i>
            <h3>External Link</h3>
            <p>You are about to leave this site and visit:</p>
            <p class="external-link-url">${url}</p>
            <article class="external-link-buttons">
                <button class="btn-cancel">Cancel</button>
                <button class="btn-continue">Continue</button>
            </article>
        </section>
    `;
    
    document.body.appendChild(modal);
    
    const cancelBtn = modal.querySelector('.btn-cancel');
    const continueBtn = modal.querySelector('.btn-continue');
    const overlay = modal.querySelector('.external-link-overlay');
    
    const closeModal = () => {
        modal.remove();
    };
    
    cancelBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', closeModal);
    
    continueBtn.addEventListener('click', () => {
        window.open(url, '_blank', 'noopener,noreferrer');
        closeModal();
    });
    
    const escHandler = (e) => {
        if (e.key === 'Escape') {
            closeModal();
            document.removeEventListener('keydown', escHandler);
        }
    };
    document.addEventListener('keydown', escHandler);
}
