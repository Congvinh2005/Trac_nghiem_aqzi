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

// Get database connection
$database = new Database();
$db = $database->getConnection();
$exam = new Exam($db);
$question = new Question($db);
$answer = new Answer($db);

// Get action
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Allow public access for these actions
$public_actions = ['get_by_code'];

// Check if user is authenticated and is teacher (except for public actions)
if (!in_array($action, $public_actions)) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['phan_quyen'] !== 'teacher') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
}

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

    case 'get_questions':
        getQuestionsByExam();
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
                'link' => '/views/gui_bai.html?code=' . $result['ma_code']
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
        echo json_encode(['success' => false, 'message' => 'Invalid code: code is empty']);
        return;
    }

    try {
        $exam_data = $exam->getByCode($code);

        if ($exam_data) {
            echo json_encode([
                'success' => true,
                'data' => $exam_data
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy đề thi với code: ' . $code]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
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
 * Get questions by exam ID
 */
function getQuestionsByExam() {
    global $question, $answer;

    $ma_de = isset($_GET['ma_de']) ? trim($_GET['ma_de']) : '';

    if (empty($ma_de)) {
        echo json_encode(['success' => false, 'message' => 'Invalid exam ID']);
        return;
    }

    $questions = $question->getByExam($ma_de);
    
    // Get answers for each question
    foreach ($questions as &$q) {
        $q['dap_an'] = $answer->getByQuestion($q['ma_cau_hoi']);
    }

    echo json_encode([
        'success' => true,
        'data' => $questions
    ]);
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
 * Parse exam file (txt, doc, docx format)
 * Expected format:
 * 1.Câu hỏi?
 * A. Đáp án A
 * B. Đáp án B
 * C. Đáp án C
 * D. Đáp án D
 * Or: a/ Đáp án A, b/ Đáp án B, etc.
 */
function parseExamFile() {
    if (!isset($_FILES['file'])) {
        echo json_encode(['success' => false, 'message' => 'No file uploaded']);
        return;
    }

    $file = $_FILES['file'];
    $allowed_extensions = ['txt', 'csv', 'doc', 'docx'];

    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($file_extension, $allowed_extensions)) {
        echo json_encode(['success' => false, 'message' => 'Chỉ hỗ trợ file .txt, .csv, .doc, .docx']);
        return;
    }

    $questions = [];

    if ($file_extension === 'docx') {
        // .docx: parse XML trực tiếp, detect highlight vàng = đáp án đúng
        $questions = parseDocxWithHighlight($file['tmp_name']);
    } else {
        // txt / csv / doc: dùng plain text + parseContent
        $content = '';
        if ($file_extension === 'txt' || $file_extension === 'csv') {
            $content = file_get_contents($file['tmp_name']);
            if (!mb_detect_encoding($content, 'UTF-8', true)) {
                $content = mb_convert_encoding($content, 'UTF-8', 'auto');
            }
        } else {
            // .doc binary
            $content = file_get_contents($file['tmp_name']);
            $content = preg_replace('/[^\x20-\x7E\x{0080}-\x{FFFF}\r\n\t]/u', ' ', $content);
        }
        $questions = parseContent($content);
    }

    if (empty($questions)) {
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy câu hỏi nào. Kiểm tra định dạng file.',
            'debug' => ['extension' => $file_extension]
        ]);
        return;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Parsing successful',
        'data' => $questions
    ]);
}

/**
 * Parse .docx file trực tiếp từ XML.
 * Detect đáp án highlight màu vàng (w:highlight val="yellow" hoặc shd fill vàng)
 * và tự động đánh dấu la_dung = 1.
 */
function parseDocxWithHighlight($filePath) {
    $zip = new ZipArchive();
    if ($zip->open($filePath) !== TRUE) {
        return [];
    }

    $xmlContent = $zip->getFromName('word/document.xml');
    $zip->close();

    if (!$xmlContent) return [];

    // Decode XML entities
    $xmlContent = html_entity_decode($xmlContent, ENT_QUOTES | ENT_XML1, 'UTF-8');

    // Tách từng paragraph <w:p>
    preg_match_all('/<w:p[ >].*?<\/w:p>/s', $xmlContent, $paraMatches);

    $chapter_pattern  = '/^\s*(Chương|Chapter|Bài|Part|Phần)\s*\d+/iu';
    $question_pattern = '/^\s*(\d+)\s*[.\/):-]\s*(.+)/u';
    $answer_pattern   = '/^\s*([A-Da-d])\s*[.\/):-]\s*(.+)/u';
    $yellow_fills     = ['FFFF00', 'ffff00', 'yellow', 'FFFF', 'ffff'];

    // Build danh sách paragraphs kèm flag is_highlighted
    $paragraphs = [];
    foreach ($paraMatches[0] as $paraXml) {
        // Lấy text
        preg_match_all('/<w:t[^>]*>([^<]*)<\/w:t>/s', $paraXml, $textMatches);
        $paraText = trim(implode('', $textMatches[1]));
        if (empty($paraText)) continue;

        // Kiểm tra highlight vàng:
        // Cách 1: <w:highlight w:val="yellow"/>
        $isYellow = preg_match('/<w:highlight[^>]+w:val="yellow"/', $paraXml);
        // Cách 2: <w:shd w:fill="FFFF00">
        if (!$isYellow) {
            foreach ($yellow_fills as $fill) {
                if (preg_match('/<w:shd[^>]+w:fill="' . preg_quote($fill, '/') . '"/', $paraXml)) {
                    $isYellow = true;
                    break;
                }
            }
        }

        $paragraphs[] = ['text' => $paraText, 'yellow' => (bool)$isYellow];
    }

    // Parse theo kiểu sliding window
    $questions = [];
    $q_counter = 0;
    $i = 0;
    $n = count($paragraphs);
    $letters = ['A', 'B', 'C', 'D'];

    while ($i < $n) {
        $para = $paragraphs[$i];
        $line = $para['text'];

        // Bỏ qua tiêu đề chương
        if (preg_match($chapter_pattern, $line)) { $i++; continue; }

        // Detect câu hỏi
        $q_text = null;
        if (preg_match($question_pattern, $line, $m)) {
            $q_text = trim($m[2]);
            $i++;
        } elseif (preg_match('/\?\s*$/u', $line) && !preg_match($answer_pattern, $line)) {
            $q_text = $line;
            $i++;
        } else {
            $i++;
            continue;
        }

        // Thu thập đến 4 đáp án
        $answers_raw = [];
        while ($i < $n && count($answers_raw) < 4) {
            $al = $paragraphs[$i];
            $al_text = $al['text'];

            if (preg_match($chapter_pattern, $al_text)) break;
            if (preg_match($question_pattern, $al_text) && count($answers_raw) > 0) break;
            if (preg_match('/\?\s*$/u', $al_text) && !preg_match($answer_pattern, $al_text) && count($answers_raw) > 0) break;

            $ans_text = $al_text;
            if (preg_match($answer_pattern, $al_text, $am)) {
                $ans_text = trim($am[2]);
            }

            $answers_raw[] = [
                'text'   => $ans_text,
                'yellow' => $al['yellow']
            ];
            $i++;
        }

        if (count($answers_raw) < 2) continue;

        // Pad đến 4 đáp án nếu thiếu
        while (count($answers_raw) < 4) {
            $answers_raw[] = ['text' => 'Chưa có nội dung', 'yellow' => false];
        }

        // Kiểm tra có đáp án vàng không
        $has_yellow = false;
        foreach ($answers_raw as $a) {
            if ($a['yellow']) { $has_yellow = true; break; }
        }

        $q_counter++;
        $dap_an = [];
        foreach ($answers_raw as $idx => $a) {
            // Nếu file có highlight vàng: dùng vàng để xác định đúng
            // Nếu không có highlight nào: la_dung = 0 (giáo viên chọn thủ công)
            $la_dung = $has_yellow ? ($a['yellow'] ? 1 : 0) : 0;
            $dap_an[] = [
                'ky_tu'   => $letters[$idx],
                'noi_dung'=> $a['text'],
                'la_dung' => $la_dung
            ];
        }

        $questions[] = [
            'thu_tu'  => $q_counter,
            'noi_dung'=> $q_text,
            'dap_an'  => $dap_an
        ];
    }

    return $questions;
}

/**
 * Parse content string into questions and answers.
 * Handles Word auto-list format where A/B/C/D labels may be missing.
 * Strategy: detect question lines, then collect next 4 lines as answers A/B/C/D.
 */
function parseContent($content) {
    $questions = [];

    // Normalize whitespace
    $content = str_replace("\t", ' ', $content);
    $content = preg_replace('/[ \t]+/', ' ', $content);

    // Split into lines and clean
    $lines = preg_split('/\r\n|\r|\n/', $content);
    $cleaned = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line)) {
            $cleaned[] = $line;
        }
    }

    $chapter_pattern  = '/^\s*(Chương|Chapter|Bài|Part|Phần)\s*\d+/iu';
    $question_pattern = '/^\s*(\d+)\s*[.\/):-]\s*(.+)/u';
    $answer_pattern   = '/^\s*([A-Da-d])\s*[.\/):-]\s*(.+)/u';

    $q_counter = 0;
    $i = 0;
    $n = count($cleaned);

    while ($i < $n) {
        $line = $cleaned[$i];

        // Skip chapter headers
        if (preg_match($chapter_pattern, $line)) { $i++; continue; }

        // Detect question line
        $q_text = null;

        if (preg_match($question_pattern, $line, $m)) {
            $q_text = trim($m[2]);
            $i++;
        } elseif (preg_match('/\?\s*$/u', $line) && !preg_match($answer_pattern, $line)) {
            $q_text = $line;
            $i++;
        } else {
            $i++;
            continue;
        }

        // Collect up to 4 answers
        $answers_raw = [];
        $letters = ['A', 'B', 'C', 'D'];

        while ($i < $n && count($answers_raw) < 4) {
            $al = $cleaned[$i];

            if (preg_match($chapter_pattern, $al)) break;
            if (preg_match($question_pattern, $al) && count($answers_raw) > 0) break;
            if (preg_match('/\?\s*$/u', $al) && !preg_match($answer_pattern, $al) && count($answers_raw) > 0) break;

            if (preg_match($answer_pattern, $al, $am)) {
                $answers_raw[] = trim($am[2]);
            } else {
                $answers_raw[] = $al;
            }
            $i++;
        }

        if (count($answers_raw) < 2) continue;

        while (count($answers_raw) < 4) {
            $answers_raw[] = 'Chưa có nội dung';
        }

        $q_counter++;
        $dap_an = [];
        foreach ($answers_raw as $idx => $ans_text) {
            $dap_an[] = [
                'ky_tu'   => $letters[$idx],
                'noi_dung'=> $ans_text,
                'la_dung' => 0
            ];
        }

        $questions[] = [
            'thu_tu'  => $q_counter,
            'noi_dung'=> $q_text,
            'dap_an'  => $dap_an
        ];
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
