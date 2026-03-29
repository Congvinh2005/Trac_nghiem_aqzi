# Shared Components - Vinhzota

## Overview
Reusable UI components for consistent design across all Vinhzota pages.

## Components

### 1. Sidebar Component

**Files:**
- `/vinhzota/assets/components/sidebar.html` - The sidebar HTML template
- `/vinhzota/assets/components/sidebar.css` - Shared sidebar styles
- `/vinhzota/assets/components/sidebar.js` - JavaScript loader for dynamic inclusion

**Usage:**
```html
<head>
    <link rel="stylesheet" href="/vinhzota/assets/components/sidebar.css">
</head>
<body>
    <div id="sidebar-container"></div>
    <script src="/vinhzota/assets/components/sidebar.js"></script>
    <script>
        loadSidebar(1); // 0=Trang chủ, 1=Đề thi, 2=Lớp học, etc.
    </script>
    <div class="main-content">
        <!-- your content -->
    </div>
</body>
```

**Navigation Items:**
| Index | Icon | Label | Link |
|-------|------|-------|------|
| 0 | 🏠 | Trang chủ | trang_admin.html |
| 1 | 📚 | Đề thi | trang_admin.html |
| 2 | 👥 | Lớp học | # |
| 3 | 📊 | Thống kê | # |
| 4 | 📚 | Bài tập | # |
| 5 | 🏛️ | Tổ chuyên môn | # |
| 6 | 📖 | Thư viện | # |

---

### 2. Header Component

**Files:**
- `/vinhzota/assets/components/header.html` - The header HTML template
- `/vinhzota/assets/components/header.css` - Shared header styles
- `/vinhzota/assets/components/header.js` - JavaScript loader with interactions

**Usage:**
```html
<head>
    <link rel="stylesheet" href="/vinhzota/assets/components/header.css">
</head>
<body>
    <div id="header-container"></div>
    <script src="/vinhzota/assets/components/header.js"></script>
    <script>
        loadHeader({
            showBack: true,
            showSearch: true,
            showFilter: true,
            userName: 'Công Vinh',
            userAvatar: 'CV'
        });
    </script>
</body>
```

**Options:**
- `showBack` (boolean): Show/hide back button
- `showSearch` (boolean): Show/hide search box
- `showFilter` (boolean): Show/hide filter button
- `userName` (string): Display user name
- `userAvatar` (string): Display avatar initials

**Functions:**
- `toggleProfileMenu()` - Toggle profile dropdown
- `logout()` - Handle logout
- `setupSearchHandler(callback)` - Setup search input handler

---

### 3. Footer Component

**Files:**
- `/vinhzota/assets/components/footer.html` - The footer HTML template
- `/vinhzota/assets/components/footer.css` - Shared footer styles
- `/vinhzota/assets/components/footer.js` - JavaScript loader

**Usage:**
```html
<head>
    <link rel="stylesheet" href="/vinhzota/assets/components/footer.css">
</head>
<body>
    <!-- your content -->
    <div id="footer-container"></div>
    <script src="/vinhzota/assets/components/footer.js"></script>
</body>
```

---

## Complete Page Template

```html
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Title - Vinhzota</title>
    <link rel="stylesheet" href="/vinhzota/assets/components/sidebar.css">
    <link rel="stylesheet" href="/vinhzota/assets/components/header.css">
    <link rel="stylesheet" href="/vinhzota/assets/components/footer.css">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }
        .main-content {
            flex: 1;
            margin-left: 90px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div id="sidebar-container"></div>
    <script src="/vinhzota/assets/components/sidebar.js"></script>
    <script>loadSidebar(1);</script>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div id="header-container"></div>
        <script src="/vinhzota/assets/components/header.js"></script>
        <script>
            loadHeader({
                showBack: true,
                userName: 'Công Vinh',
                userAvatar: 'CV'
            });
        </script>
        
        <!-- Page Content -->
        <div class="container">
            <!-- your content here -->
        </div>
    </div>
    
    <!-- Footer -->
    <div id="footer-container"></div>
    <script src="/vinhzota/assets/components/footer.js"></script>
</body>
</html>
```

---

## Pages Updated

| Page | Sidebar | Header | Footer |
|------|---------|--------|--------|
| `trang_admin.html` | ✅ (1) | ✅ | ✅ |
| `danh_sach_da_thi.html` | ✅ (2) | ✅ | ✅ |
| `xem_chi_tiet.html` | ✅ (2) | ✅ | ✅ |
| `tao_de.html` | ✅ (1) | ✅ | ✅ |

---

## Migration Checklist

For each page:

**Sidebar:**
- [ ] Add sidebar CSS link to `<head>`
- [ ] Remove existing sidebar CSS
- [ ] Replace sidebar HTML with container
- [ ] Add sidebar.js and call `loadSidebar(index)`

**Header:**
- [ ] Add header CSS link to `<head>`
- [ ] Remove existing header CSS
- [ ] Replace header HTML with container
- [ ] Add header.js and call `loadHeader(options)`

**Footer:**
- [ ] Add footer CSS link to `<head>`
- [ ] Remove existing footer CSS
- [ ] Replace footer HTML with container
- [ ] Add footer.js

---

## Notes

- All components use the same color scheme: `#1e3c72` → `#2a5298` gradient
- Sidebar width: 90px (responsive to 70px on mobile)
- Components are designed for admin/teacher interfaces
- For student interfaces, consider creating separate variants
