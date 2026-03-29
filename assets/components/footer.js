/**
 * Footer Component Loader
 */

// Load footer HTML component
async function loadFooter() {
    try {
        const response = await fetch('/vinhzota/assets/components/footer.html');
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
