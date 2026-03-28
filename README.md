# Vinhzota - Hệ Thống Quản Lý Bài Tập Trực Tuyến

## 📋 Giới thiệu

Vinhzota là nền tảng quản lý bài tập trắc nghiệm trực tuyến, cho phép giáo viên tạo đề thi từ file và học sinh làm bài trực tuyến. Dự án được xây dựng dựa trên cấu trúc của dự án Banhang với kiến trúc MVC.

## 🔧 Yêu cầu hệ thống

- **XAMPP** với PHP 7.4+ và MariaDB/MySQL
- **Trình duyệt** hiện đại (Chrome, Firefox, Edge, Safari)

## 📀 Cài đặt

### 1. Import Database

1. Mở phpMyAdmin (thường tại `http://localhost/phpmyadmin`)
2. Tạo database mới tên là `vinhzota`
3. Import file `vinhzota.sql` vào database

Hoặc chạy lệnh:
```bash
mysql -u root -p vinhzota < vinhzota.sql
```

### 2. Cấu hình Database

File cấu hình đã được thiết lập sẵn tại:
- `config/database.php`

Thông tin mặc định:
- Host: `localhost`
- Database: `vinhzota`
- Username: `root`
- Password: `` (trống)

### 3. Truy cập

Mở trình duyệt và truy cập:
```
http://localhost/vinhzota/
```

## 👤 Tài khoản demo

### Giáo viên
- **Username:** `giaovien`
- **Password:** `password123`

### Học sinh
- **Username:** `hocsinh`
- **Password:** `password123`

## 📁 Cấu trúc thư mục

```
vinhzota/
├── config/
│   └── database.php          # Cấu hình database
├── controllers/
│   ├── auth_controller.php   # Xử lý đăng nhập/đăng ký
│   ├── exam_controller.php   # Xử lý đề thi
│   └── submission_controller.php  # Xử lý nộp bài
├── models/
│   ├── User.php              # Model người dùng
│   ├── Exam.php              # Model đề thi
│   ├── Question.php          # Model câu hỏi
│   └── Answer.php            # Model đáp án
├── views/
│   ├── dang_nhap.html        # Trang đăng nhập
│   ├── chon_role_dang_ky.html # Chọn vai trò
│   ├── dang_ky.html          # Đăng ký
│   ├── trang_admin.html      # Dashboard giáo viên
│   ├── tao_de.html           # Tạo đề thi
│   ├── gui_bai.html          # Chia sẻ đề thi
│   ├── trang_lam.html        # Làm bài thi
│   └── ket_qua.html          # Kết quả
├── assets/
│   ├── css/                  # Stylesheets
│   ├── js/                   # JavaScript files
│   └── uploads/              # File upload
├── api/                      # API endpoints
├── index.php                 # Entry point
└── vinhzota.sql              # Database schema
```

## 🎯 Tính năng chính

### Giáo viên
- ✅ Đăng nhập, phân quyền
- ✅ Tạo đề thi mới
- ✅ Upload file câu hỏi (.txt)
- ✅ Tự động parse câu hỏi từ file
- ✅ Đánh dấu đáp án đúng
- ✅ Cấu hình thời gian nộp
- ✅ Cho phép/xem kết quả
- ✅ Yêu cầu đăng nhập làm bài
- ✅ Sinh mã code và link chia sẻ
- ✅ QR code cho đề thi
- ✅ Xem danh sách bài đã nộp
- ✅ Xem kết quả chi tiết

### Học sinh
- ✅ Truy cập qua link hoặc mã code
- ✅ Làm bài không cần đăng nhập (tùy chọn)
- ✅ Giao diện làm bài thân thiện
- ✅ Timer đếm ngược thời gian
- ✅ Điều hướng giữa các câu hỏi
- ✅ Nộp bài tự động khi hết giờ
- ✅ Xem kết quả ngay sau khi nộp
- ✅ Xem lại đáp án (nếu được cho phép)

## 📝 Định dạng file đề thi

File text (.txt) với định dạng:

```
1.Một hệ thống phân tán bao gồm hai phần chính nào?
A. Phần cứng và phần mềm.
B. Mạng máy tính và CSDL phân tán.
C. Máy chủ và máy khách.
D. Hệ điều hành và ứng dụng cơ sở dữ liệu phân tán

2.Mạng Star (hình sao) sử dụng thiết bị trung tâm nào để kết nối?
A. Modem và Switch
B. Router
C. Hub hoặc Switch.
D. Repeater.
```

### Quy tắc:
- Mỗi câu hỏi bắt đầu bằng số thứ tự + dấu chấm
- 4 đáp án A, B, C, D cho mỗi câu
- Các câu hỏi cách nhau bằng dòng trống

## 🔐 Bảo mật

- ✅ Password hashing (bcrypt)
- ✅ Prepared statements (chống SQL Injection)
- ✅ XSS prevention
- ✅ Session management
- ✅ Role-based access control

## 🛠️ API Endpoints

### Authentication
- `POST /controllers/auth_controller.php?action=login` - Đăng nhập
- `POST /controllers/auth_controller.php?action=register` - Đăng ký
- `GET /controllers/auth_controller.php?action=logout` - Đăng xuất
- `GET /controllers/auth_controller.php?action=check_auth` - Kiểm tra đăng nhập

### Exam
- `POST /controllers/exam_controller.php?action=create` - Tạo đề thi
- `GET /controllers/exam_controller.php?action=get_by_teacher` - Lấy đề thi theo GV
- `GET /controllers/exam_controller.php?action=get_by_code&code=xxx` - Lấy đề thi theo code
- `POST /controllers/exam_controller.php?action=parse_file` - Parse file câu hỏi
- `POST /controllers/exam_controller.php?action=save_questions` - Lưu câu hỏi

### Submission
- `POST /controllers/submission_controller.php?action=submit` - Nộp bài
- `GET /controllers/submission_controller.php?action=get_questions&ma_de=xxx` - Lấy câu hỏi
- `GET /controllers/submission_controller.php?action=get_submissions&ma_de=xxx` - Lấy danh sách nộp
- `GET /controllers/submission_controller.php?action=get_submission_detail&ma_bai_lam=xxx` - Chi tiết bài làm

## 🚀 Phát triển thêm

### Các tính năng đang phát triển:
- [ ] Export kết quả ra Excel
- [ ] Ngân hàng câu hỏi
- [ ] Sửa/xóa câu hỏi
- [ ] Thống kê, báo cáo
- [ ] Multiple file formats (.docx, .pdf)
- [ ] Import từ Google Forms
- [ ] Mobile app

## 📞 Hỗ trợ

Nếu gặp vấn đề khi cài đặt hoặc sử dụng:
1. Kiểm tra log lỗi PHP trong `xampp/apache/logs/error.log`
2. Đảm bảo database đã được import đúng
3. Kiểm tra kết nối database trong `config/database.php`

## 📄 License

Dự án mã nguồn mở, tự do sử dụng và phát triển.

---

**Phát triển với ❤️ bởi Vinhzota Team**
