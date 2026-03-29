/**
 * Global App Configuration
 * Tự động phát hiện base path cho cả local và hosting
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
