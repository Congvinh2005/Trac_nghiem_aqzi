/**
 * Global App Configuration
 * Tự động phát hiện base path cho cả local và hosting
 */

// Tự động phát hiện base path
function getBasePath() {
    const path = window.location.pathname || '';
    const normalizedPath = path.replace(/\/+$/, '');
    const lowerPath = normalizedPath.toLowerCase();

    // Ưu tiên cắt theo các thư mục chuẩn của dự án
    const markers = ['/views/', '/controllers/', '/assets/', '/api/'];
    for (const marker of markers) {
        const idx = lowerPath.indexOf(marker);
        if (idx === 0) {
            return '';
        }
        if (idx > 0) {
            return normalizedPath.slice(0, idx);
        }
        // Trường hợp URL kết thúc ngay tại thư mục marker, ví dụ: /vinhzota/views
        if (lowerPath.endsWith(marker.slice(0, -1))) {
            return normalizedPath.slice(0, -marker.length + 1);
        }
    }

    const parts = normalizedPath.split('/').filter(Boolean);
    if (parts.length > 0) {
        // Fallback: lấy segment đầu tiên làm base path
        return '/' + parts[0];
    }

    // Nếu không tìm thấy, trả về rỗng (root)
    return '';
}

// Global constant
const BASE_PATH = getBasePath();

// Base URL đầy đủ
const BASE_URL = window.location.origin + BASE_PATH;

// API Endpoints
const API = {
    AUTH: BASE_PATH + '/controllers/auth_controller.php',
    EXAM: BASE_PATH + '/controllers/exam_controller.php',
    SUBMISSION: BASE_PATH + '/controllers/submission_controller.php'
};

// Log cho debug
console.log('=== App Config ===');
console.log('BASE_PATH:', BASE_PATH);
console.log('BASE_URL:', BASE_URL);
console.log('API Endpoints:', API);
