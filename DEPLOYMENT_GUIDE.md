# 📦 Hướng dẫn Deploy Vinhzota lên Hosting

## ✅ Đã hoàn thành

- [x] Xóa thư mục `.qwen` (file cấu hình local)
- [x] Cấu hình đường dẫn tự động phát hiện local/hosting
- [x] Cập nhật JavaScript components (sidebar, header, footer)
- [x] Cấu hình database tự động nhận diện môi trường
- [x] Cấu hình `.htaccess` cho Apache

---

## 🚀 Cách deploy lên hosting

### 1. Chuẩn bị

**File cần upload:**
```
vinhzota/
├── api/
├── assets/
├── config/
├── controllers/
├── models/
├── views/
├── .htaccess          ✅ Quan trọng
├── index.php
├── vinhosota.sql      ✅ Database
└── ... (các file khác)
```

**KHÔNG upload:**
- `.git/` (thư mục Git)
- `README.md`, `DEPLOYMENT.md` (file tài liệu)
- File backup, file tạm

---

### 2. Upload files lên hosting

#### Cách 1: Dùng cPanel File Manager
1. Đăng nhập cPanel
2. Vào **File Manager**
3. Vào thư mục `public_html`
4. Tạo thư mục `vinhzota` (hoặc upload thẳng vào root nếu muốn)
5. Upload tất cả files
6. Giải nén (nếu upload file .zip)

#### Cách 2: Dùng FTP (FileZilla)
1. Kết nối FTP với thông tin hosting cung cấp
2. Vào thư mục `public_html`
3. Upload toàn bộ files vào `public_html/vinhzota/`

---

### 3. Cấu hình Database

**Bước 1: Tạo database mới**
1. Vào cPanel → **MySQL Databases**
2. Tạo database mới (ví dụ: `username_vinhzota`)
3. Tạo user MySQL (ví dụ: `username_vinhzota_user`)
4. Đặt password mạnh
5. Gán user vào database với quyền **ALL PRIVILEGES**

**Bước 2: Import database**
1. Vào cPanel → **phpMyAdmin**
2. Chọn database vừa tạo
3. Click **Import**
4. Chọn file `vinhzota.sql`
5. Click **Go**

**Bước 3: Cập nhật thông tin database (nếu cần)**

Tạo file `config/database_config.php` trên hosting:

```php
<?php
// Cấu hình cho hosting
define('DB_HOST', 'localhost');           // Thường là localhost
define('DB_NAME', 'username_vinhzota');   // Tên database đã tạo
define('DB_USER', 'username_vinhzota');   // User đã tạo
define('DB_PASS', 'mat_khau_manh');       // Password đã đặt
?>
```

---

### 4. Kiểm tra

**Truy cập và test:**
```
https://your-domain.com/vinhzota/
hoặc
http://your-domain.com/vinhzota/
```

**Test các trang:**
- ✅ Trang chủ: `/vinhzota/`
- ✅ Admin: `/vinhzota/views/trang_admin.html`
- ✅ Tạo đề: `/vinhzota/views/tao_de.html`
- ✅ Login: `/vinhzota/views/dang_nhap.html`

---

## 🔧 Cấu hình thêm (nếu cần)

### Force HTTPS
Mở `.htaccess`, bỏ comment dòng:
```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### Custom error pages
Tạo file `404.html`, `403.html` và uncomment trong `.htaccess`

### PHP Version
Đảm bảo hosting dùng **PHP 7.4+** hoặc **PHP 8.x**

---

## 🐛 Troubleshooting

### Lỗi: "Connection Error"
- Kiểm tra thông tin database trong `config/database_config.php`
- Đảm bảo user MySQL có quyền truy cập database

### Lỗi: "404 Not Found"
- Kiểm tra `.htaccess` đã được upload chưa
- Đảm bảo `mod_rewrite` được bật trên hosting

### Lỗi: "500 Internal Server Error"
- Kiểm tra PHP version
- Xem error log trong cPanel → **Error Log**

### Components không load (sidebar, header, footer)
- Xóa cache trình duyệt (Ctrl+Shift+R)
- Kiểm tra Console (F12) xem có lỗi JavaScript không

---

## 📝 Ghi chú

### Đường dẫn tự động
Hệ thống tự động phát hiện:
- **Local**: `http://localhost/vinhzota/`
- **Hosting**: `https://your-domain.com/vinhzota/`
- **Root hosting**: `https://your-domain.com/`

### Components
Tất cả components (sidebar, header, footer) tự động nhận diện base path:
```javascript
// Tự động phát hiện
const BASE_PATH = getBasePath();
// Local: BASE_PATH = '/vinhzota'
// Hosting: BASE_PATH = '/vinhzota' hoặc '' (nếu ở root)
```

---

## ✅ Checklist sau khi deploy

- [ ] Upload tất cả files (trừ .git)
- [ ] Tạo database và user
- [ ] Import file .sql
- [ ] Tạo file `config/database_config.php` (nếu cần)
- [ ] Test trang chủ
- [ ] Test login
- [ ] Test tạo đề thi
- [ ] Test upload file
- [ ] Xóa cache trình duyệt
- [ ] Kiểm tra Console không có lỗi

---

## 🎉 Hoàn thành!

Website đã chạy trên hosting với:
- ✅ Sidebar, header, footer dùng chung
- ✅ Đường dẫn tự động phát hiện môi trường
- ✅ Database configuration thông minh
- ✅ Security headers
- ✅ Error handling

**Support:** Nếu có vấn đề, kiểm tra error log trong cPanel!
