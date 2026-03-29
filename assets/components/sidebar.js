/**
 * Sidebar Component Loader
 * Usage: Include this script and call loadSidebar() with the active nav item index
 */

// Tự động phát hiện base path
function getBasePath() {
    const path = window.location.pathname;
    const parts = path.split('/');
    
    // Tìm 'vinhzota' trong path
    const vinhzotaIndex = parts.indexOf('vinhzota');
    if (vinhzotaIndex !== -1) {
        // Lấy tất cả phần từ đầu đến 'vinhzota'
        return parts.slice(0, vinhzotaIndex + 1).join('/');
    }
    
    // Nếu không tìm thấy, trả về rỗng (root)
    return '';
}

const BASE_PATH = getBasePath();

// Load sidebar HTML component
async function loadSidebar(activeIndex = -1) {
    try {
        const response = await fetch(BASE_PATH + '/assets/components/sidebar.html');
        const sidebarHTML = await response.text();

        const sidebarContainer = document.getElementById('sidebar-container');
        if (sidebarContainer) {
            sidebarContainer.innerHTML = sidebarHTML;

            // Set active state if specified
            if (activeIndex >= 0) {
                const navItems = sidebarContainer.querySelectorAll('.nav-item');
                if (navItems[activeIndex]) {
                    navItems[activeIndex].classList.add('active');
                }
            }
        }
    } catch (error) {
        console.error('Error loading sidebar:', error);
    }
}

// Alternative: Inline sidebar for simpler implementation
function createSidebar(activeIndex = -1) {
    const sidebar = document.createElement('div');
    sidebar.className = 'sidebar';
    sidebar.innerHTML = `
        <div class="logo-sidebar" onclick="window.location.href='trang_admin.html'">V</div>
        <div class="nav-item ${activeIndex === 0 ? 'active' : ''}" title="Trang chủ" onclick="window.location.href='trang_admin.html'">🏠</div>
        <div class="nav-item ${activeIndex === 1 ? 'active' : ''}" title="Đề thi" onclick="window.location.href='trang_admin.html'">📚</div>
        <div class="nav-item ${activeIndex === 2 ? 'active' : ''}" title="Lớp học" onclick="window.location.href='#'">👥</div>
        <div class="nav-item ${activeIndex === 3 ? 'active' : ''}" title="Thống kê" onclick="window.location.href='#'">📊</div>
        <div class="nav-item ${activeIndex === 4 ? 'active' : ''}" title="Bài tập" onclick="window.location.href='#'">📚</div>
        <div class="nav-item ${activeIndex === 5 ? 'active' : ''}" title="Tổ chuyên môn" onclick="window.location.href='#'">🏛️</div>
        <div class="nav-item ${activeIndex === 6 ? 'active' : ''}" title="Thư viện" onclick="window.location.href='#'">📖</div>
        <div class="nav-spacer"></div>
        <button class="settings-btn" title="Cài đặt">⚙️</button>
    `;
    
    return sidebar;
}

// Initialize sidebar on page load
document.addEventListener('DOMContentLoaded', function() {
    // Auto-load sidebar if sidebar-container exists
    const sidebarContainer = document.getElementById('sidebar-container');
    if (sidebarContainer) {
        loadSidebar();
    }
});
