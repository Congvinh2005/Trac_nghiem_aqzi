<?php
/**
 * Vinhzota - Hệ thống quản lý bài tập trực tuyến
 * Main entry point - Redirect to master layout
 */

// Redirect to master layout based on user role
session_start();

if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['phan_quyen'] === 'teacher') {
        header('Location: master_teacher.html');
    } else {
        header('Location: master_student.html');
    }
} else {
    header('Location: views/dang_nhap.html');
}
exit;
?>
