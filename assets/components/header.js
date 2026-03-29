/**
 * Header Component Loader
 */

// Load header HTML component
async function loadHeader(options = {}) {
    const {
        showBack = true,
        showSearch = true,
        showFilter = true,
        userName = 'Giáo viên',
        userAvatar = 'GV'
    } = options;
    
    try {
        const response = await fetch('/vinhzota/assets/components/header.html');
        let headerHTML = await response.text();
        
        // Customize based on options
        if (!showBack) {
            headerHTML = headerHTML.replace(/<button class="back-btn".*?<\/button>/, '');
        }
        
        const headerContainer = document.getElementById('header-container');
        if (headerContainer) {
            headerContainer.innerHTML = headerHTML;
            
            // Set user info
            if (userName) {
                const nameEl = document.getElementById('headerUserName');
                if (nameEl) nameEl.textContent = userName;
            }
            if (userAvatar) {
                const avatarEl = document.getElementById('headerAvatar');
                if (avatarEl) avatarEl.textContent = userAvatar;
            }
        }
    } catch (error) {
        console.error('Error loading header:', error);
    }
}

// Toggle profile dropdown
function toggleProfileMenu() {
    const dropdown = document.getElementById('profileDropdown');
    if (dropdown) {
        dropdown.classList.toggle('show');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('profileDropdown');
    const profile = document.querySelector('.profile');
    
    if (dropdown && profile && !dropdown.contains(event.target) && !profile.contains(event.target)) {
        dropdown.classList.remove('show');
    }
});

// Go back
function goBack() {
    window.history.back();
}

// View profile
function viewProfile() {
    console.log('View profile');
    // Add your profile navigation logic here
}

// Open settings
function openSettings() {
    console.log('Open settings');
    // Add your settings logic here
}

// Logout
async function logout() {
    if (!confirm('Bạn có chắc chắn muốn đăng xuất?')) {
        return;
    }
    
    try {
        const response = await fetch('/vinhzota/controllers/auth_controller.php?action=logout');
        const result = await response.json();
        
        if (result.success) {
            window.location.href = result.redirect || 'dang_nhap.html';
        } else {
            window.location.href = 'dang_nhap.html';
        }
    } catch (error) {
        console.error('Logout error:', error);
        window.location.href = 'dang_nhap.html';
    }
}

// Search handler
function setupSearchHandler(callback) {
    const searchInput = document.getElementById('headerSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            if (callback) callback(e.target.value);
        });
        
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && callback) {
                callback(e.target.value);
            }
        });
    }
}

// Initialize header on page load
document.addEventListener('DOMContentLoaded', function() {
    const headerContainer = document.getElementById('header-container');
    if (headerContainer) {
        loadHeader();
    }
});
