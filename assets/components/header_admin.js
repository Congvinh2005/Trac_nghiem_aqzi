/**
 * Header Admin Component Loader
 */

function getComponentBasePath() {
    if (typeof BASE_PATH !== 'undefined') {
        return BASE_PATH;
    }

    const path = window.location.pathname || '';
    const lower = path.toLowerCase();
    const markers = ['/views/', '/controllers/', '/assets/', '/api/'];

    for (const marker of markers) {
        const idx = lower.indexOf(marker);
        if (idx === 0) return '';
        if (idx > 0) return path.slice(0, idx);
    }

    const parts = path.split('/').filter(Boolean);
    return parts.length > 0 ? '/' + parts[0] : '';
}

async function loadHeaderAdmin(options = {}) {
    const {
        showSearch = true,
        showFilter = true,
        userName = 'Giáo viên',
        userAvatar = 'GV'
    } = options;

    try {
        const response = await fetch(getComponentBasePath() + '/assets/components/header_admin.html?v=' + Date.now());
        let headerHTML = await response.text();

        const headerContainer = document.getElementById('header-container');
        if (headerContainer) {
            headerContainer.innerHTML = headerHTML;

            // Đợi DOM render xong
            setTimeout(() => {
                // Ẩn search nếu không hiển thị
                if (!showSearch) {
                    const headerCenter = headerContainer.querySelector('.header-center');
                    if (headerCenter) {
                        headerCenter.style.display = 'none';
                    }
                }

                // Ẩn filter nếu không hiển thị
                if (!showFilter) {
                    const filterBtn = headerContainer.querySelector('.filter-btn');
                    if (filterBtn) {
                        filterBtn.style.display = 'none';
                    }
                }

                // Set user info
                if (userName) {
                    const nameEl = document.getElementById('headerUserName');
                    if (nameEl) nameEl.textContent = userName;
                }
                if (userAvatar) {
                    const avatarEl = document.getElementById('headerAvatar');
                    if (avatarEl) avatarEl.textContent = userAvatar;
                }
            }, 100);
        }
    } catch (error) {
        console.error('Error loading header admin:', error);
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
}

// Open settings
function openSettings() {
    console.log('Open settings');
}

// Logout
async function logout() {
    if (!confirm('Bạn có chắc chắn muốn đăng xuất?')) {
        return;
    }

    try {
        const response = await fetch(getComponentBasePath() + '/controllers/auth_controller.php?action=logout');
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
        // Only load if loadHeaderAdmin was called, or default
        // loadHeaderAdmin(); // Commennted out to let explicit calls run it
    }
});
