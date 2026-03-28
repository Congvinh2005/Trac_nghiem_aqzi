<?php
/**
 * Submission Controller (Bai Lam)
 * Handle exam submission and grading
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
$exam_model = new Exam($db);
$question_model = new Question($db);
$answer_model = new Answer($db);

// Get action
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'submit':
        submitExam();
        break;
    
    case 'get_questions':
        getQuestionsForExam();
        break;
    
    case 'grade':
        gradeExam();
        break;
    
    case 'get_submissions':
        getSubmissions();
        break;
    
    case 'get_submission_detail':
        getSubmissionDetail();
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

/**
 * Submit exam
 */
function submitExam() {
    global $db;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $ma_de = isset($data['ma_de']) ? trim($data['ma_de']) : '';
    $answers = isset($data['answers']) ? $data['answers'] : [];
    $student_info = isset($data['student_info']) ? $data['student_info'] : [];
    
    if (empty($ma_de)) {
        echo json_encode(['success' => false, 'message' => 'Invalid exam ID']);
        return;
    }
    
    // Get exam info
    global $exam_model;
    $exam = $exam_model->getById($ma_de);
    
    if (!$exam) {
        echo json_encode(['success' => false, 'message' => 'Exam not found']);
        return;
    }
    
    // Check if login required
    if ($exam['yeu_cau_dang_nhap'] && !isset($_SESSION['user'])) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để nộp bài']);
        return;
    }
    
    // Generate submission ID
    $ma_bai_lam = 'BL' . date('YmdHis') . strtoupper(substr(md5(uniqid()), 0, 4));
    
    // Prepare student info
    $ma_hoc_sinh = null;
    $ten_hoc_sinh = '';
    $email = '';
    
    if (isset($_SESSION['user'])) {
        $ma_hoc_sinh = $_SESSION['user']['ma_user'];
        $ten_hoc_sinh = $_SESSION['user']['full_name'];
        $email = $_SESSION['user']['email'];
    } else {
        $ten_hoc_sinh = isset($student_info['ten_hoc_sinh']) ? $student_info['ten_hoc_sinh'] : 'Học sinh ẩn danh';
        $email = isset($student_info['email']) ? $student_info['email'] : '';
    }
    
    // Save answers as JSON
    $danh_sach_dap_an = json_encode($answers);
    
    // Insert submission
    $query = "INSERT INTO bai_lam 
              (ma_bai_lam, ma_de, ma_hoc_sinh, ten_hoc_sinh, email, danh_sach_dap_an) 
              VALUES 
              (:ma_bai_lam, :ma_de, :ma_hoc_sinh, :ten_hoc_sinh, :email, :danh_sach_dap_an)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":ma_bai_lam", $ma_bai_lam);
    $stmt->bindParam(":ma_de", $ma_de);
    $stmt->bindParam(":ma_hoc_sinh", $ma_hoc_sinh);
    $stmt->bindParam(":ten_hoc_sinh", $ten_hoc_sinh);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":danh_sach_dap_an", $danh_sach_dap_an);
    
    if ($stmt->execute()) {
        // Auto grade if answers are provided
        $grade_result = autoGrade($ma_de, $answers);
        
        // Update score
        $update_query = "UPDATE bai_lam SET diem = :diem WHERE ma_bai_lam = :ma_bai_lam";
        $update_stmt = $db->prepare($update_query);
        $update_stmt->bindParam(":diem", $grade_result['score']);
        $update_stmt->bindParam(":ma_bai_lam", $ma_bai_lam);
        $update_stmt->execute();
        
        echo json_encode([
            'success' => true,
            'message' => 'Nộp bài thành công',
            'ma_bai_lam' => $ma_bai_lam,
            'score' => $grade_result['score'],
            'total_questions' => $grade_result['total'],
            'correct_answers' => $grade_result['correct']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nộp bài thất bại']);
    }
}

/**
 * Auto grade exam
 */
function autoGrade($ma_de, $answers) {
    global $question_model, $answer_model;
    
    // Get all questions for this exam
    $questions = $question_model->getByExam($ma_de);
    $total = count($questions);
    $correct = 0;
    
    foreach ($questions as $question) {
        $ma_cau_hoi = $question['ma_cau_hoi'];
        
        // Get correct answer
        $correct_answer = $answer_model->getCorrectAnswer($ma_cau_hoi);
        
        // Get student's answer
        $student_answer = isset($answers[$ma_cau_hoi]) ? $answers[$ma_cau_hoi] : null;
        
        // Check if correct
        if ($correct_answer && $student_answer) {
            if ($correct_answer['ky_tu'] === strtoupper($student_answer)) {
                $correct++;
            }
        }
    }
    
    // Calculate score (out of 10)
    $score = $total > 0 ? round(($correct / $total) * 10, 2) : 0;
    
    return [
        'score' => $score,
        'total' => $total,
        'correct' => $correct
    ];
}

/**
 * Get questions for exam (for student to take)
 */
function getQuestionsForExam() {
    global $question_model, $answer_model;
    
    $ma_de = isset($_GET['ma_de']) ? trim($_GET['ma_de']) : '';
    
    if (empty($ma_de)) {
        echo json_encode(['success' => false, 'message' => 'Invalid exam ID']);
        return;
    }
    
    $questions = $question_model->getByExam($ma_de);
    
    $result = [];
    foreach ($questions as $question) {
        $answers = $answer_model->getByQuestion($question['ma_cau_hoi']);
        
        $result[] = [
            'ma_cau_hoi' => $question['ma_cau_hoi'],
            'noi_dung' => $question['noi_dung'],
            'hinh_anh' => $question['hinh_anh'],
            'thu_tu' => $question['thu_tu'],
            'dap_an' => $answers
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $result
    ]);
}

/**
 * Get all submissions for an exam (for teacher)
 */
function getSubmissions() {
    global $db;
    
    $ma_de = isset($_GET['ma_de']) ? trim($_GET['ma_de']) : '';
    
    if (empty($ma_de)) {
        echo json_encode(['success' => false, 'message' => 'Invalid exam ID']);
        return;
    }
    
    $query = "SELECT * FROM bai_lam 
              WHERE ma_de = :ma_de 
              ORDER BY thoi_gian_nop DESC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":ma_de", $ma_de);
    $stmt->execute();
    
    $submissions = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $submissions
    ]);
}

/**
 * Get submission detail
 */
function getSubmissionDetail() {
    global $db;
    
    $ma_bai_lam = isset($_GET['ma_bai_lam']) ? trim($_GET['ma_bai_lam']) : '';
    
    if (empty($ma_bai_lam)) {
        echo json_encode(['success' => false, 'message' => 'Invalid submission ID']);
        return;
    }
    
    $query = "SELECT * FROM bai_lam 
              WHERE ma_bai_lam = :ma_bai_lam 
              LIMIT 1";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":ma_bai_lam", $ma_bai_lam);
    $stmt->execute();
    
    $submission = $stmt->fetch();
    
    if ($submission) {
        echo json_encode([
            'success' => true,
            'data' => $submission
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Submission not found']);
    }
}
?>
