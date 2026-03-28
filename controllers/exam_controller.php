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

    $content = '';
    
    // Read file content based on extension
    if ($file_extension === 'txt' || $file_extension === 'csv') {
        $content = file_get_contents($file['tmp_name']);
        // Convert to UTF-8 if needed
        if (!mb_detect_encoding($content, 'UTF-8', true)) {
            $content = mb_convert_encoding($content, 'UTF-8', 'auto');
        }
    } elseif ($file_extension === 'doc' || $file_extension === 'docx') {
        // For .doc/.docx, try to extract text
        $content = extractTextFromDoc($file['tmp_name'], $file_extension);
    }

    // Debug: log content
    file_put_contents('/tmp/debug_content.txt', substr($content, 0, 500));

    // Parse content
    $questions = parseContent($content);

    if (empty($questions)) {
        echo json_encode([
            'success' => false, 
            'message' => 'Không tìm thấy câu hỏi nào. Định dạng: 1.Câu hỏi? A. Đáp án A B. Đáp án B...',
            'debug' => [
                'extension' => $file_extension,
                'content_length' => strlen($content),
                'first_200_chars' => substr($content, 0, 200)
            ]
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
 * Extract text from .doc or .docx file
 */
function extractTextFromDoc($filePath, $extension) {
    $content = '';
    
    if ($extension === 'docx') {
        // .docx is a ZIP archive containing XML files
        $zip = new ZipArchive();
        if ($zip->open($filePath) === TRUE) {
            // Try to extract from word/document.xml
            $xmlContent = $zip->getFromName('word/document.xml');
            
            if ($xmlContent) {
                // Parse paragraph-by-paragraph to avoid text fragmentation.
                // In OOXML, a single line like "A. Phần cứng và phần mềm."
                // is split across many <w:r><w:t> run elements. Stripping all
                // XML tags naively breaks these into separate lines that don't
                // match the answer regex. Instead, we join all <w:t> within 
                // each <w:p> paragraph first, then split by paragraph.
                preg_match_all('/<w:p[ >].*?<\/w:p>/s', $xmlContent, $paraMatches);
                $lines = [];
                foreach ($paraMatches[0] as $paraXml) {
                    preg_match_all('/<w:t[^>]*>([^<]*)<\/w:t>/s', $paraXml, $textMatches);
                    $paraText = trim(implode('', $textMatches[1]));
                    if (!empty($paraText)) {
                        $lines[] = $paraText;
                    }
                }
                $content = implode("\n", $lines);
                // Decode XML entities
                $content = html_entity_decode($content, ENT_QUOTES | ENT_XML1, 'UTF-8');
            }
            
            $zip->close();
        }
        
        // If still empty, try alternative method
        if (empty(trim($content))) {
            $content = file_get_contents($filePath);
            // Try to extract readable text
            $content = preg_replace('/[^\x20-\x7E\x{0080}-\x{FFFF}\r\n]/u', "\n", $content);
            $content = preg_replace('/\n\s*\n/', "\n", $content);
        }
    } else {
        // .doc is binary - basic extraction (limited support)
        $content = file_get_contents($filePath);
        // Remove binary characters, keep text
        $content = preg_replace('/[^\x20-\x7E\x{0080}-\x{FFFF}\r\n\t]/u', ' ', $content);
        $content = preg_replace('/\s+/', ' ', $content);
    }
    
    return $content;
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
