# 📚 DỰ ÁN VINHZOTA - TỔNG KẾT

## 🎯 Tổng quan

**Vinhzota** là hệ thống quản lý bài tập trắc nghiệm trực tuyến, được xây dựng dựa trên kiến trúc MVC tương tự dự án Banhang. Hệ thống cho phép giáo viên tạo đề thi từ file text và học sinh làm bài trực tuyến với giao diện hiện đại, responsive.

---

## ✨ Tính năng đã hoàn thành

### 👨‍🏫 Dành cho Giáo viên

| Tính năng | Mô tả | File |
|-----------|-------|------|
| Đăng nhập/Đăng ký | Phân quyền teacher/student | `dang_nhap.html`, `dang_ky.html` |
| Dashboard | Xem danh sách đề thi đã tạo | `trang_admin.html` |
| Tạo đề thi | Upload file, parse tự động câu hỏi | `tao_de.html` |
| Đánh dấu đáp án | Chọn đáp án đúng cho mỗi câu | `tao_de.html` |
| Cấu hình đề thi | Thời gian, xem kết quả, yêu cầu ĐN | `tao_de.html` |
| Chia sẻ đề thi | Sinh link, QR code | `gui_bai.html` |
| Xem danh sách nộp | Theo dõi học sinh đã nộp bài | `danh_sach_da_thi.html` |

### 🎓 Dành cho Học sinh

| Tính năng | Mô tả | File |
|-----------|-------|------|
| Trang chủ | Nhập mã đề thi để làm | `trang_chu.html` |
| Làm bài thi | Giao diện trắc nghiệm, timer | `trang_lam.html` |
| Nộp bài | Tự động/hand submit | `trang_lam.html` |
| Xem kết quả | Điểm số, thống kê | `ket_qua.html` |
| Xem đáp án | Review lại bài làm | `xem_dap_an.html` |

---

## 📁 Cấu trúc dự án

```
vinhzota/
│
├── 📄 index.php                    # Entry point
├── 📄 README.md                    # Documentation
├── 📄 DEPLOYMENT.md                # Hướng dẫn deploy
│
├── 🗄️ vinhzota.sql                 # Database schema
│
├── 📂 config/
│   └── database.php                # DB connection
│
├── 📂 controllers/
│   ├── auth_controller.php         # Login, Register, Logout
│   ├── exam_controller.php         # Exam CRUD, Parse file
│   └── submission_controller.php   # Submit, Grade, Review
│
├── 📂 models/
│   ├── User.php                    # User model
│   ├── Exam.php                    # Exam model
│   ├── Question.php                # Question model
│   └── Answer.php                  # Answer model
│
├── 📂 views/
│   ├── dang_nhap.html              # Login page
│   ├── chon_role_dang_ky.html      # Role selection
│   ├── dang_ky.html                # Register page
│   ├── trang_admin.html            # Teacher dashboard
│   ├── tao_de.html                 # Create exam
│   ├── gui_bai.html                # Share exam
│   ├── trang_chu.html              # Student home
│   ├── trang_lam.html              # Take exam
│   ├── ket_qua.html                # Results
│   ├── xem_dap_an.html             # Review answers
│   └── danh_sach_da_thi.html       # Submissions list
│
└── 📂 assets/
    ├── css/                        # Stylesheets (future)
    ├── js/                         # JavaScript (future)
    └── uploads/
        └── sample_exam.txt         # Sample exam file
```

---

## 🗄️ Database Schema

### Bảng `users`
- Lưu thông tin người dùng (giáo viên/học sinh)
- Fields: ma_user, ten_user, password, full_name, email, phone, school, phan_quyen

### Bảng `de_thi`
- Lưu thông tin đề thi
- Fields: ma_de, ma_giao_vien, ten_de, ma_code, thoi_gian_nap, cho_xem_ket_qua, yeu_cau_dang_nhap

### Bảng `cau_hoi`
- Lưu câu hỏi của đề thi
- Fields: ma_cau_hoi, ma_de, noi_dung, hinh_anh, thu_tu

### Bảng `dap_an`
- Lưu đáp án cho mỗi câu hỏi
- Fields: ma_dap_an, ma_cau_hoi, noi_dung, ky_tu, la_dung

### Bảng `bai_lam`
- Lưu bài làm của học sinh
- Fields: ma_bai_lam, ma_de, ma_hoc_sinh, ten_hoc_sinh, email, danh_sach_dap_an, diem

---

## 🔧 Công nghệ sử dụng

### Backend
- **PHP 7.4+** - Server-side scripting
- **PDO** - Database abstraction layer
- **MySQL/MariaDB** - Database

### Frontend
- **HTML5** - Markup
- **CSS3** - Styling with gradients, animations
- **JavaScript (ES6+)** - Client-side logic
- **Fetch API** - AJAX requests

### Security
- **Password Hashing** - bcrypt
- **Prepared Statements** - SQL Injection prevention
- **XSS Protection** - htmlspecialchars
- **Session Management** - PHP Sessions

---

## 🚀 API Endpoints

### Authentication
```
POST /controllers/auth_controller.php?action=login
POST /controllers/auth_controller.php?action=register
GET  /controllers/auth_controller.php?action=logout
GET  /controllers/auth_controller.php?action=check_auth
```

### Exam Management
```
POST /controllers/exam_controller.php?action=create
GET  /controllers/exam_controller.php?action=get_by_teacher
GET  /controllers/exam_controller.php?action=get_by_code
POST /controllers/exam_controller.php?action=parse_file
POST /controllers/exam_controller.php?action=save_questions
POST /controllers/exam_controller.php?action=delete
```

### Submission
```
POST /controllers/submission_controller.php?action=submit
GET  /controllers/submission_controller.php?action=get_questions
GET  /controllers/submission_controller.php?action=get_submissions
GET  /controllers/submission_controller.php?action=get_submission_detail
```

---

## 📊 Statistics

| Metric | Count |
|--------|-------|
| **Total Files** | 20+ |
| **PHP Files** | 7 |
| **HTML Files** | 11 |
| **SQL Tables** | 5 |
| **API Endpoints** | 14 |
| **Lines of Code** | ~5000+ |

---

## 🎯 Luồng hoạt động

### 1. Giáo viên tạo đề thi

```
Login → Dashboard → Tạo đề thi → Upload file → 
Parse câu hỏi → Đánh dấu đáp án → Lưu & Chia sẻ
```

### 2. Học sinh làm bài

```
Nhận link → Nhập thông tin → Làm bài → Nộp bài → 
Xem kết quả → Xem đáp án
```

---

## 🔐 Tài khoản Demo

```
Giáo viên:
- Username: giaovien
- Password: password123

Học sinh:
- Username: hocsinh
- Password: password123
```

---

## 📝 Định dạng file đề thi

File `.txt` với format:

```
1.Câu hỏi thứ nhất?
A. Đáp án A
B. Đáp án B
C. Đáp án C
D. Đáp án D

2.Câu hỏi thứ hai?
A. Đáp án A
B. Đáp án B
C. Đáp án C
D. Đáp án D
```

**Quy tắc:**
- Câu hỏi bắt đầu bằng số + dấu chấm
- 4 đáp án A, B, C, D
- Cách nhau bằng dòng trống

---

## 🚧 Tính năng tương lai (Roadmap)

### Phase 2
- [ ] Export kết quả ra Excel/PDF
- [ ] Ngân hàng câu hỏi
- [ ] Import từ Word/PDF
- [ ] Thống kê, biểu đồ
- [ ] Quản lý lớp học

### Phase 3
- [ ] Multiple question types (tự luận, đúng/sai)
- [ ] Randomize questions
- [ ] Time limit per question
- [ ] Mobile App (React Native)

### Phase 4
- [ ] AI tạo câu hỏi tự động
- [ ] Integration với LMS
- [ ] Real-time proctoring
- [ ] Cloud deployment

---

## 📞 Hỗ trợ & Debug

### Log files
- Apache: `xampp/apache/logs/error.log`
- MySQL: `xampp/mysql/data/hostname.err`

### Common issues
1. **Connection Error** → Check DB config
2. **404 Not Found** → Check URL path
3. **Permission Denied** → chmod 777 uploads/
4. **Session issues** → Check php.ini

---

## 👥 Đóng góp

Dự án mã nguồn mở, welcome mọi đóng góp!

### Cách đóng góp:
1. Fork repository
2. Tạo branch mới (`feature/ten-tinh-nang`)
3. Commit changes
4. Push và tạo Pull Request

---

## 📄 License

MIT License - Tự do sử dụng và phát triển

---

## 🙏 Lời cảm ơn

Cảm ơn bạn đã sử dụng Vinhzota!

**Phát triển với ❤️ bởi Vinhzota Team**

---

*Last updated: 2026-03-28*
