/**
 * Footer Component Loader
 */

// Tự động phát hiện base path
function getBasePath() {
    const path = window.location.pathname;
    const parts = path.split('/');
    
    const vinhzotaIndex = parts.indexOf('vinhzota');
    if (vinhzotaIndex !== -1) {
        return parts.slice(0, vinhzotaIndex + 1).join('/');
    }
    
    return '';
}

const BASE_PATH = getBasePath();

// Load footer HTML component
async function loadFooter() {
    try {
        const response = await fetch(BASE_PATH + '/assets/components/footer.html');
        const footerHTML = await response.text();
        
        const footerContainer = document.getElementById('footer-container');
        if (footerContainer) {
            footerContainer.innerHTML = footerHTML;
        }
    } catch (error) {
        console.error('Error loading footer:', error);
    }
}

// Initialize footer on page load
document.addEventListener('DOMContentLoaded', function() {
    const footerContainer = document.getElementById('footer-container');
    if (footerContainer) {
        loadFooter();
    }
});
