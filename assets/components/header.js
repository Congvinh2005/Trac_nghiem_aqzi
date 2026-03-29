/**
 * Header Component Loader
 */

// Sử dụng BASE_PATH từ app-config.js nếu đã tồn tại
if (typeof BASE_PATH === 'undefined') {
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
}

// Load header HTML component
async function loadHeader(options = {}) {
    const {
        showBack = true,
        showSearch = true,
        showFilter = true,
        customTitle = '',
        userName = 'Giáo viên',
        userAvatar = 'GV'
    } = options;

    try {
        const response = await fetch(BASE_PATH + '/assets/components/header.html?v=' + Date.now());
        let headerHTML = await response.text();

        const headerContainer = document.getElementById('header-container');
        if (headerContainer) {
            headerContainer.innerHTML = headerHTML;

            // Đợi DOM render xong
            setTimeout(() => {
                const customTitleEl = document.getElementById('headerCustomTitle');
                const backBtnEl = document.getElementById('headerBackBtn');
                const backTextEl = document.getElementById('headerBackText');

                console.log('=== Header Debug ===');
                console.log('customTitle:', customTitle);
                console.log('customTitleEl:', customTitleEl);
                console.log('backBtnEl:', backBtnEl);

                if (customTitle) {
                    // Hiển thị custom title, ẩn back button
                    if (customTitleEl) {
                        customTitleEl.textContent = customTitle;
                        customTitleEl.style.display = 'block';
                        console.log('Đã hiển thị custom title:', customTitle);
                    }
                    if (backBtnEl) {
                        backBtnEl.style.display = 'none';
                        console.log('Đã ẩn back button');
                    }
                } else {
                    // Xử lý back button và label
                    if (backBtnEl) {
                        backBtnEl.style.display = showBack ? 'flex' : 'none';
                        if (showBack && options.backLabel) {
                            if (backTextEl) backTextEl.textContent = options.backLabel;
                        }
                    }
                    if (customTitleEl) {
                        customTitleEl.style.display = 'none';
                    }
                }

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
