<?php
/**
 * Authentication Controller
 * Handle login, register, logout
 */

session_start();
require_once '../config/database.php';
require_once '../models/User.php';

header('Content-Type: application/json');

// Get database connection
$database = new Database();
$db = $database->getConnection();
$user = new User($db);

// Get action
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'login':
        handleLogin($user);
        break;
    
    case 'register':
        handleRegister($user);
        break;
    
    case 'logout':
        handleLogout();
        break;
    
    case 'check_auth':
        checkAuth();
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

/**
 * Handle login
 */
function handleLogin($user) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $username = isset($data['username']) ? trim($data['username']) : '';
    $password = isset($data['password']) ? $data['password'] : '';
    
    // Validation
    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin']);
        return;
    }
    
    // Get user
    $user_data = $user->getByUsernameOrEmail($username);
    
    if (!$user_data) {
        echo json_encode(['success' => false, 'message' => 'Tên đăng nhập hoặc email không tồn tại']);
        return;
    }
    
    // Verify password
    if (!$user->verifyPassword($password, $user_data['password'])) {
        echo json_encode(['success' => false, 'message' => 'Mật khẩu không chính xác']);
        return;
    }
    
    // Set session
    $_SESSION['user'] = [
        'ma_user' => $user_data['ma_user'],
        'ten_user' => $user_data['ten_user'],
        'full_name' => $user_data['full_name'],
        'email' => $user_data['email'],
        'phan_quyen' => $user_data['phan_quyen'],
        'avatar' => $user_data['avatar']
    ];
    
    // Determine redirect based on role
    $redirect = '/vinhzota/master_teacher.html';
    if ($user_data['phan_quyen'] === 'teacher') {
        $redirect = '/vinhzota/master_teacher.html';
    } else {
        $redirect = '/vinhzota/master_student.html';
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Đăng nhập thành công',
        'redirect' => $redirect,
        'user' => [
            'ma_user' => $user_data['ma_user'],
            'full_name' => $user_data['full_name'],
            'phan_quyen' => $user_data['phan_quyen']
        ]
    ]);
}

/**
 * Handle register
 */
function handleRegister($user) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $ten_user = isset($data['ten_user']) ? trim($data['ten_user']) : '';
    $password = isset($data['password']) ? $data['password'] : '';
    $full_name = isset($data['full_name']) ? trim($data['full_name']) : '';
    $email = isset($data['email']) ? trim($data['email']) : '';
    $phone = isset($data['phone']) ? trim($data['phone']) : '';
    $school = isset($data['school']) ? trim($data['school']) : '';
    $phan_quyen = isset($data['phan_quyen']) ? $data['phan_quyen'] : 'student';
    
    // Validation
    if (empty($ten_user) || empty($password) || empty($full_name)) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin bắt buộc']);
        return;
    }
    
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Mật khẩu phải có ít nhất 6 ký tự']);
        return;
    }
    
    if (!empty($email) && $user->emailExists($email)) {
        echo json_encode(['success' => false, 'message' => 'Email đã tồn tại']);
        return;
    }
    
    if (!empty($phone) && $user->phoneExists($phone)) {
        echo json_encode(['success' => false, 'message' => 'Số điện thoại đã tồn tại']);
        return;
    }
    
    // Create user
    $user_data = [
        'ten_user' => $ten_user,
        'password' => $password,
        'full_name' => $full_name,
        'email' => $email,
        'phone' => $phone,
        'school' => $school,
        'phan_quyen' => $phan_quyen
    ];
    
    $ma_user = $user->create($user_data);
    
    if ($ma_user) {
        echo json_encode([
            'success' => true,
            'message' => 'Đăng ký thành công',
            'redirect' => '/vinhzota/views/dang_nhap.html'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Đăng ký thất bại, vui lòng thử lại']);
    }
}

/**
 * Handle logout
 */
function handleLogout() {
    session_destroy();
    echo json_encode([
        'success' => true,
        'message' => 'Đăng xuất thành công',
        'redirect' => '/vinhzota/views/dang_nhap.html'
    ]);
}

/**
 * Check authentication
 */
function checkAuth() {
    if (isset($_SESSION['user'])) {
        echo json_encode([
            'authenticated' => true,
            'user' => $_SESSION['user']
        ]);
    } else {
        echo json_encode([
            'authenticated' => false
        ]);
    }
}
?>
