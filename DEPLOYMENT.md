# 🚀 HƯỚNG DẪN DEPLOY DỰ ÁN VINHZOTA

## 📋 Mục lục
1. [Yêu cầu hệ thống](#yêu-cầu-hệ-thống)
2. [Cài đặt từng bước](#cài-đặt-từng-bước)
3. [Cấu hình](#cấu-hình)
4. [Chạy thử](#chạy-thử)
5. [Xử lý sự cố](#xử-lý-sự-cố)

---

## 🔧 Yêu cầu hệ thống

### Phần mềm cần thiết:
- **XAMPP** (PHP 7.4+ và MariaDB/MySQL)
  - Download: https://www.apachefriends.org/
  
- **Trình duyệt web** hiện đại
  - Chrome, Firefox, Edge, Safari

### Cấu hình tối thiểu:
- RAM: 2GB
- CPU: Dual Core 2.0 GHz
- Dung lượng trống: 500MB

---

## 📀 Cài đặt từng bước

### Bước 1: Cài đặt XAMPP

1. **Download XAMPP** từ trang chủ
2. **Cài đặt** vào thư mục mặc định:
   - Windows: `C:\xampp`
   - macOS: `/Applications/XAMPP`
   - Linux: `/opt/lampp`

3. **Khởi động XAMPP Control Panel**
   - Start **Apache**
   - Start **MySQL**

### Bước 2: Import Database

#### Cách 1: Sử dụng phpMyAdmin (Giao diện web)

1. Mở trình duyệt, truy cập: `http://localhost/phpmyadmin`
2. Click tab **"SQL"** ở menu trên
3. Click nút **"Choose File"** và chọn file `vinhzota.sql`
4. Click **"Go"** để thực thi

#### Cách 2: Sử dụng Command Line

```bash
# Windows (Command Prompt)
cd C:\xampp\mysql\bin
mysql -u root -p < "path/to/vinhzota.sql"

# macOS/Linux
cd /Applications/XAMPP/xamppfiles/bin
mysql -u root -p < /path/to/vinhzota.sql
```

**Lưu ý:** Nếu MySQL không có password (mặc định), bỏ qua `-p`:
```bash
mysql -u root < path/to/vinhzota.sql
```

### Bước 3: Kiểm tra Database

1. Vào phpMyAdmin: `http://localhost/phpmyadmin`
2. Click vào database **`vinhzota`** ở cột trái
3. Kiểm tra các bảng đã được tạo:
   - ✅ users
   - ✅ de_thi
   - ✅ cau_hoi
   - ✅ dap_an
   - ✅ bai_lam

### Bước 4: Copy project vào htdocs

#### Windows:
```
Copy thư mục: C:\Users\YourName\Downloads\Banhang
Paste vào: C:\xampp\htdocs\
```

#### macOS:
```
Copy thư mục: /Users/YourName/Downloads/Banhang
Paste vào: /Applications/XAMPP/xamppfiles/htdocs/
```

### Bước 5: Cấu hình Database (nếu cần)

Nếu database của bạn khác mặc định, mở file:
```
/Applications/XAMPP/xamppfiles/htdocs/vinhzota/config/database.php
```

Sửa thông tin:
```php
private $host = "localhost";        // Server database
private $db_name = "vinhzota";      // Tên database
private $username = "root";         // Username MySQL
private $password = "";             // Password MySQL (thường để trống)
```

---

## 🎯 Chạy thử

### 1. Truy cập ứng dụng

Mở trình duyệt và vào:
```
http://localhost/vinhzota/
```

### 2. Đăng nhập bằng tài khoản demo

#### Giáo viên:
- **Username:** `giaovien`
- **Password:** `password123`

#### Học sinh:
- **Username:** `hocsinh`
- **Password:** `password123`

### 3. Test các tính năng

#### 👨‍🏫 Luồng Giáo viên:

1. **Đăng nhập** với tài khoản giáo viên
2. Click **"+ Tạo đề thi"**
3. Nhập thông tin:
   - Tên đề thi: "Kiểm tra Mạng máy tính"
   - Thời gian nộp: (chọn ngày giờ)
   - Cho xem kết quả: "Có"
   - Yêu cầu đăng nhập: "Không"
4. **Upload file** `sample_exam.txt` từ thư mục `assets/uploads/`
5. **Đánh dấu đáp án đúng** cho mỗi câu hỏi
6. Click **"💾 Lưu và chia sẻ"**
7. **Copy link** hoặc **QR code** để chia sẻ

#### 🎓 Luồng Học sinh:

1. **Mở link** từ giáo viên gửi
2. **Nhập thông tin** (nếu không yêu cầu đăng nhập)
3. **Làm bài** trắc nghiệm
4. **Nộp bài**
5. **Xem kết quả** ngay lập tức

---

## 🔧 Xử lý sự cố

### Lỗi 1: "Connection Error"

**Nguyên nhân:** Không kết nối được database

**Giải pháp:**
1. Kiểm tra MySQL đã start chưa
2. Kiểm tra thông tin trong `config/database.php`
3. Đảm bảo database `vinhzota` đã được tạo

```bash
# Kiểm tra MySQL đang chạy
# Windows: Mở XAMPP Control Panel
# macOS: 
ps aux | grep mysql
```

### Lỗi 2: "404 Not Found"

**Nguyên nhân:** Đường dẫn không đúng

**Giải pháp:**
1. Kiểm tra thư mục project trong `htdocs`
2. Đảm bảo URL đúng: `http://localhost/vinhzota/`

### Lỗi 3: "Permission Denied" khi upload file

**Nguyên nhân:** Thư mục uploads không có quyền ghi

**Giải pháp:**

#### macOS/Linux:
```bash
chmod 777 /Applications/XAMPP/xamppfiles/htdocs/vinhzota/assets/uploads/
```

#### Windows:
1. Right-click thư mục `uploads`
2. Properties → Security
3. Edit → Add "Everyone" → Full Control

### Lỗi 4: Password không hoạt động

**Nguyên nhân:** Password hash không đúng

**Giải pháp:** Reset password trong database

```sql
-- Mở phpMyAdmin, chạy lệnh SQL:
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE ten_user = 'giaovien';

-- Password mới sẽ là: password123
```

### Lỗi 5: Session không hoạt động

**Nguyên nhân:** Session config trong php.ini

**Giải pháp:**
1. Mở `xampp/php/php.ini`
2. Tìm và đảm bảo:
```ini
session.save_path = "/tmp"
session.cookie_lifetime = 0
```
3. Restart Apache

### Lỗi 6: File upload không parse được

**Nguyên nhân:** Định dạng file không đúng

**Giải pháp:**
- File phải là `.txt` hoặc `.csv`
- Format đúng:
```
1.Câu hỏi?
A. Đáp án A
B. Đáp án B
C. Đáp án C
D. Đáp án D
```

---

## 📞 Hỗ trợ thêm

### Log files để debug:

#### Apache Error Log:
- Windows: `C:\xampp\apache\logs\error.log`
- macOS: `/Applications/XAMPP/xamppfiles/logs/error_log`

#### PHP Error Log:
- Location: `xampp/php/logs/`

### Kiểm tra PHP version:
```bash
php -v
```

### Kiểm tra MySQL version:
```bash
mysql --version
```

---

## ✅ Checklist sau khi cài đặt

- [ ] XAMPP đã cài đặt và chạy
- [ ] Apache đang start (đèn xanh)
- [ ] MySQL đang start (đèn xanh)
- [ ] Database `vinhzota` đã được tạo
- [ ] Các bảng: users, de_thi, cau_hoi, dap_an, bai_lam
- [ ] Thư mục project trong `htdocs`
- [ ] Truy cập được `http://localhost/vinhzota/`
- [ ] Đăng nhập được với tài khoản demo
- [ ] Upload file đề thi thành công
- [ ] Làm bài và nộp bài thành công

---

## 🎉 Thành công!

Nếu tất cả các bước trên đều OK, bạn đã deploy thành công Vinhzota!

**Chúc bạn thành công! 🚀**
