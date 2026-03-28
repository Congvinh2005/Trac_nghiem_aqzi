<?php
/**
 * Exam Controller
 * Handle exam creation, parsing, CRUD operations
 */

session_start();
require_once '../config/database.php';
require_once '../models/Exam.php';
require_once '../models/Question.php';
require_once '../models/Answer.php';

header('Content-Type: application/json');

// Check if user is authenticated and is teacher
if (!isset($_SESSION['user']) || $_SESSION['user']['phan_quyen'] !== 'teacher') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get database connection
$database = new Database();
$db = $database->getConnection();
$exam = new Exam($db);
$question = new Question($db);
$answer = new Answer($db);

// Get action
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'create':
        createExam();
        break;
    
    case 'get_by_teacher':
        getExamsByTeacher();
        break;
    
    case 'get_by_code':
        getExamByCode();
        break;
    
    case 'get_by_id':
        getExamById();
        break;
    
    case 'update':
        updateExam();
        break;
    
    case 'delete':
        deleteExam();
        break;
    
    case 'parse_file':
        parseExamFile();
        break;
    
    case 'save_questions':
        saveQuestions();
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

/**
 * Create new exam
 */
function createExam() {
    global $exam;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $exam_data = [
        'ma_giao_vien' => $_SESSION['user']['ma_user'],
        'ten_de' => isset($data['ten_de']) ? trim($data['ten_de']) : '',
        'thoi_gian_nap' => isset($data['thoi_gian_nap']) && !empty($data['thoi_gian_nap']) ? $data['thoi_gian_nap'] : null,
        'cho_xem_ket_qua' => isset($data['cho_xem_ket_qua']) && $data['cho_xem_ket_qua'] === 'yes' ? 1 : 0,
        'yeu_cau_dang_nhap' => isset($data['yeu_cau_dang_nhap']) && $data['yeu_cau_dang_nhap'] === 'yes' ? 1 : 0
    ];
    
    if (empty($exam_data['ten_de'])) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng nhập tên đề thi']);
        return;
    }
    
    $result = $exam->create($exam_data);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Tạo đề thi thành công',
            'data' => [
                'ma_de' => $result['ma_de'],
                'ma_code' => $result['ma_code'],
                'link' => '/vinhzota/views/gui_bai.html?code=' . $result['ma_code']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Tạo đề thi thất bại']);
    }
}

/**
 * Get exams by teacher
 */
function getExamsByTeacher() {
    global $exam;
    
    $ma_giao_vien = $_SESSION['user']['ma_user'];
    $exams = $exam->getByTeacher($ma_giao_vien);
    
    echo json_encode([
        'success' => true,
        'data' => $exams
    ]);
}

/**
 * Get exam by code
 */
function getExamByCode() {
    global $exam;
    
    $code = isset($_GET['code']) ? trim($_GET['code']) : '';
    
    if (empty($code)) {
        echo json_encode(['success' => false, 'message' => 'Invalid code']);
        return;
    }
    
    $exam_data = $exam->getByCode($code);
    
    if ($exam_data) {
        echo json_encode([
            'success' => true,
            'data' => $exam_data
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy đề thi']);
    }
}

/**
 * Get exam by ID
 */
function getExamById() {
    global $exam;
    
    $ma_de = isset($_GET['ma_de']) ? trim($_GET['ma_de']) : '';
    
    if (empty($ma_de)) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        return;
    }
    
    $exam_data = $exam->getById($ma_de);
    
    if ($exam_data) {
        echo json_encode([
            'success' => true,
            'data' => $exam_data
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy đề thi']);
    }
}

/**
 * Update exam
 */
function updateExam() {
    global $exam;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $ma_de = isset($data['ma_de']) ? trim($data['ma_de']) : '';
    
    if (empty($ma_de)) {
        echo json_encode(['success' => false, 'message' => 'Invalid exam ID']);
        return;
    }
    
    $exam_data = [
        'ten_de' => isset($data['ten_de']) ? trim($data['ten_de']) : '',
        'thoi_gian_nap' => isset($data['thoi_gian_nap']) && !empty($data['thoi_gian_nap']) ? $data['thoi_gian_nap'] : null,
        'cho_xem_ket_qua' => isset($data['cho_xem_ket_qua']) && $data['cho_xem_ket_qua'] === 'yes' ? 1 : 0,
        'yeu_cau_dang_nhap' => isset($data['yeu_cau_dang_nhap']) && $data['yeu_cau_dang_nhap'] === 'yes' ? 1 : 0,
        'trang_thai' => isset($data['trang_thai']) ? $data['trang_thai'] : 'active'
    ];
    
    if ($exam->update($ma_de, $exam_data)) {
        echo json_encode(['success' => true, 'message' => 'Cập nhật đề thi thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Cập nhật đề thi thất bại']);
    }
}

/**
 * Delete exam
 */
function deleteExam() {
    global $exam;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    $ma_de = isset($data['ma_de']) ? trim($data['ma_de']) : '';
    
    if (empty($ma_de)) {
        echo json_encode(['success' => false, 'message' => 'Invalid exam ID']);
        return;
    }
    
    if ($exam->delete($ma_de)) {
        echo json_encode(['success' => true, 'message' => 'Xóa đề thi thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Xóa đề thi thất bại']);
    }
}

/**
 * Parse exam file (txt format)
 * Expected format:
 * 1.Câu hỏi?
 * A. Đáp án A
 * B. Đáp án B
 * C. Đáp án C
 * D. Đáp án D
 */
function parseExamFile() {
    if (!isset($_FILES['file'])) {
        echo json_encode(['success' => false, 'message' => 'No file uploaded']);
        return;
    }
    
    $file = $_FILES['file'];
    $allowed_types = ['text/plain', 'text/csv'];
    $allowed_extensions = ['txt', 'csv'];
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_extension, $allowed_extensions)) {
        echo json_encode(['success' => false, 'message' => 'Chỉ hỗ trợ file .txt hoặc .csv']);
        return;
    }
    
    $content = file_get_contents($file['tmp_name']);
    
    // Parse content
    $questions = parseContent($content);
    
    echo json_encode([
        'success' => true,
        'message' => 'Parsing successful',
        'data' => $questions
    ]);
}

/**
 * Parse content string into questions and answers
 */
function parseContent($content) {
    $questions = [];
    $lines = explode("\n", $content);
    $current_question = null;
    $question_counter = 0;
    
    // Regex patterns
    $question_pattern = '/^\s*(\d+)\.\s*(.+)$/';
    $answer_pattern = '/^\s*([A-D])\.\s*(.+)$/';
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        if (empty($line)) {
            continue;
        }
        
        // Check if it's a question
        if (preg_match($question_pattern, $line, $matches)) {
            // Save previous question if exists
            if ($current_question !== null) {
                $questions[] = $current_question;
            }
            
            $question_counter++;
            $current_question = [
                'thu_tu' => $question_counter,
                'noi_dung' => trim($matches[2]),
                'dap_an' => []
            ];
        }
        // Check if it's an answer
        elseif (preg_match($answer_pattern, $line, $matches) && $current_question !== null) {
            $current_question['dap_an'][] = [
                'ky_tu' => strtoupper($matches[1]),
                'noi_dung' => trim($matches[2]),
                'la_dung' => 0  // Default to false, teacher will select
            ];
        }
    }
    
    // Add last question
    if ($current_question !== null) {
        $questions[] = $current_question;
    }
    
    return $questions;
}

/**
 * Save questions and answers for an exam
 */
function saveQuestions() {
    global $question, $answer;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $ma_de = isset($data['ma_de']) ? trim($data['ma_de']) : '';
    $questions = isset($data['questions']) ? $data['questions'] : [];
    
    if (empty($ma_de) || empty($questions)) {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
        return;
    }
    
    // Delete existing questions if any
    $question->deleteByExam($ma_de);
    
    $saved_count = 0;
    
    foreach ($questions as $q) {
        // Create question
        $question_data = [
            'ma_de' => $ma_de,
            'noi_dung' => isset($q['noi_dung']) ? $q['noi_dung'] : '',
            'hinh_anh' => isset($q['hinh_anh']) ? $q['hinh_anh'] : null,
            'thu_tu' => isset($q['thu_tu']) ? $q['thu_tu'] : 0
        ];
        
        $ma_cau_hoi = $question->create($question_data);
        
        if ($ma_cau_hoi && isset($q['dap_an'])) {
            // Create answers
            foreach ($q['dap_an'] as $a) {
                $answer_data = [
                    'ma_cau_hoi' => $ma_cau_hoi,
                    'noi_dung' => isset($a['noi_dung']) ? $a['noi_dung'] : '',
                    'ky_tu' => isset($a['ky_tu']) ? strtoupper($a['ky_tu']) : 'A',
                    'la_dung' => isset($a['la_dung']) && $a['la_dung'] ? 1 : 0
                ];
                
                if ($answer->create($answer_data)) {
                    $saved_count++;
                }
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Lưu câu hỏi thành công',
        'saved_count' => $saved_count
    ]);
}
?>
