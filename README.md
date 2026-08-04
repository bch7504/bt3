# Hệ Thống Tra Cứu & Xử Lý Vi Phạm Giao Thông (Phạt Nguội)

Dự án này là hệ thống quản lý và tra cứu thông tin vi phạm giao thông (phạt nguội) bao gồm cổng tra cứu công cộng cho người dân và trang quản trị quản lý phương tiện, vi phạm cho Cảnh sát giao thông (CSGT).

---

## 📋 Yêu Cầu Hệ Thống (Requirements)

Trước khi chạy dự án, hãy đảm bảo máy tính của bạn đã cài đặt các phần mềm sau (Xem thêm chi tiết tại [requirements.txt](requirements.txt)):

1. **PHP**: Phiên bản `>= 8.0`.
2. **PostgreSQL**: Phiên bản `>= 15`.
3. **PHP Extensions**: Phải kích hoạt extension `pdo_pgsql` và `pdo` trong cấu hình `php.ini`.

---

## 🗄️ Hướng Dẫn Thiết Lập Cơ Sở Dữ Liệu (Database Setup)

Dự án sử dụng cơ sở dữ liệu **PostgreSQL**. Thực hiện các bước sau để thiết lập:

### Bước 1: Tạo Cơ sở dữ liệu và User
1. Mở công cụ quản lý PostgreSQL (như **pgAdmin**, **DBeaver**, hoặc sử dụng dòng lệnh **psql**).
2. Tạo một cơ sở dữ liệu mới tên là: `bt3`
3. Đảm bảo bạn có tài khoản người dùng PostgreSQL phù hợp. Mặc định dự án đang sử dụng:
   - **Username**: `postgres`
   - **Password**: `123456`

### Bước 2: Tạo File Cấu hình Môi trường `.env`
1. Tại thư mục gốc của dự án, sao chép file `.env.example` và đổi tên thành `.env`.
2. Mở file `.env` vừa tạo và cập nhật thông tin kết nối PostgreSQL của bạn:
   ```env
   DB_HOST=localhost
   DB_PORT=5432
   DB_NAME=bt3
   DB_USER=postgres
   DB_PASS=123456
   ```

### Bước 3: Import Cấu trúc bảng và Dữ liệu mẫu
Sử dụng file [database.sql](database.sql) trong thư mục gốc để import vào database `bt3` vừa tạo:
- **Cách 1 (Qua pgAdmin / DBeaver)**: Mở file `database.sql`, sao chép toàn bộ nội dung và chạy trong công cụ **Query Tool** của database `bt3`.
- **Cách 2 (Qua dòng lệnh psql)**: Chạy lệnh sau trong Terminal/Cmd ở thư mục dự án (sử dụng mật khẩu trong `.env` để kết nối):
  ```bash
  psql -U postgres -d bt3 -f database.sql
  ```

---

## 🚀 Hướng Dẫn Chạy Dự Án (How to Run)

### Cách 1: Sử dụng PHP Built-in Web Server (Khuyên dùng để chạy nhanh)
1. Mở Terminal / PowerShell và di chuyển vào thư mục gốc của dự án (`bt3`).
2. Chạy lệnh khởi động server mini của PHP:
   ```bash
   php -S localhost:8000
   ```
3. Truy cập hệ thống qua trình duyệt:
   - **Cổng tra cứu công cộng (Khách hàng)**: [http://localhost:8000/FE/customer/](http://localhost:8000/FE/customer/)
   - **Trang quản trị (Admin/CSGT)**: [http://localhost:8000/FE/admin/](http://localhost:8000/FE/admin/)

---

### Cách 2: Sử dụng XAMPP / Laragon / WampServer (Apache)
1. Copy toàn bộ thư mục dự án `bt3` vào thư mục gốc của Web Server:
   - Với Laragon: `C:\laragon\www\bt3`
   - Với XAMPP: `C:\xampp\htdocs\bt3`
2. Đảm bảo đã bật module **PostgreSQL** trong phần cấu hình PHP của Laragon/XAMPP.
3. Khởi động Apache và PostgreSQL từ bảng điều khiển Laragon/XAMPP.
4. Truy cập hệ thống qua các URL:
   - **Khách hàng**: `http://localhost/bt3/FE/customer/`
   - **Admin/CSGT**: `http://localhost/bt3/FE/admin/`

---

## 🔐 Thông Tin Đăng Nhập Trang Quản Trị (Admin Credentials)

Hệ thống đã được nạp sẵn 2 tài khoản quản trị mẫu trong database:

| Tên đăng nhập (Username) | Mật khẩu (Password) | Quyền hạn |
|:-------------------------|:--------------------|:----------|
| `admin`                  | `123456`            | Admin     |
| `postgres`               | `123456`            | Admin     |

> 💡 **Cơ chế Đăng nhập Dự phòng (Fallback Authentication)**:
> Hệ thống tích hợp tính năng tự động khôi phục quyền truy cập. Nếu tài khoản chưa tồn tại trong cơ sở dữ liệu nhưng bạn nhập đúng thông tin kết nối database (được khai báo trong tệp `.env`), hệ thống sẽ tự động tạo mới tài khoản quản trị viên đó và đăng nhập thành công.

---

## 📁 Cấu Trúc Thư Mục Dự Án (Folder Structure)

```text
bt3/
├── BE/                         # Mã nguồn API & xử lý Backend
│   ├── audit_logs/             # API log hoạt động
│   ├── auth/                   # API đăng nhập/đăng xuất/session
│   ├── config/                 # File cấu hình kết nối cơ sở dữ liệu
│   │   └── database.php
│   ├── customer/               # API tra cứu vi phạm của khách hàng
│   ├── notifications/          # API quản lý và gửi thông báo
│   ├── vehicles/               # API CRUD quản lý phương tiện
│   └── violations/             # API CRUD quản lý vi phạm
│
├── FE/                         # Giao diện người dùng (Frontend)
│   ├── admin/                  # Giao diện quản trị dành cho CSGT
│   │   ├── components/         # Các thành phần giao diện chung (header, footer, auth)
│   │   └── pages/              # Các trang chức năng (dashboard, vehicles, violations, log, notifications)
│   └── customer/               # Giao diện tra cứu công cộng dành cho người dân
│       ├── assets/             # CSS & Javascript tùy chỉnh
│       ├── components/         # Các phần giao diện (navbar, hero, footer)
│       └── pages/              # Trang chủ, trang kết quả tra cứu, hướng dẫn
│
├── .env                        # File cấu hình môi trường thực tế (Không push lên Git)
├── .env.example                # File cấu hình môi trường mẫu
├── database.sql                # File import cấu trúc database & dữ liệu mẫu
├── requirements.txt            # Danh sách cấu hình môi trường yêu cầu
└── README.md                   # Tài liệu hướng dẫn sử dụng (File này)
```

Để xem thông tin chi tiết về từng chức năng hệ thống, vui lòng tham khảo file [huong_dan_chuc_nang.md](huong_dan_chuc_nang.md).
