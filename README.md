# Hệ thống tra cứu và xử lý vi phạm giao thông

Ứng dụng PHP thuần gồm cổng tra cứu vi phạm dành cho người dân và trang quản trị phương tiện, vi phạm, thông báo, nhật ký hoạt động. Dữ liệu được lưu trong PostgreSQL.

## Yêu cầu

- PHP 8.0 trở lên.
- PostgreSQL 15 trở lên.
- PHP extension `PDO` và `pdo_pgsql`.
- Trình duyệt có kết nối Internet để tải Bootstrap và Bootstrap Icons từ CDN.

Kiểm tra môi trường:

```powershell
php -v
php -m | Select-String -Pattern "PDO|pdo_pgsql"
psql --version
```

Nếu chưa thấy `pdo_pgsql`, hãy bật dòng `extension=pdo_pgsql` trong `php.ini`, sau đó khởi động lại PHP/Apache.

## Cài đặt và chạy nhanh

### 1. Tạo cơ sở dữ liệu

Khởi động PostgreSQL, sau đó tạo database `bt3`:

```powershell
createdb -U postgres bt3
```

Bạn cũng có thể tạo database bằng pgAdmin hoặc DBeaver nếu không sử dụng được lệnh `createdb`.

### 2. Cấu hình môi trường

Tại thư mục gốc của dự án, tạo `.env` từ file mẫu:

```powershell
Copy-Item .env.example .env
```

Trên Command Prompt hoặc Linux/macOS, dùng lệnh tương ứng:

```bash
copy .env.example .env
# hoặc: cp .env.example .env
```

Cập nhật `.env` theo tài khoản PostgreSQL trên máy:

```env
DB_HOST=localhost
DB_PORT=5432
DB_NAME=bt3
DB_USER=postgres
DB_PASS=mat_khau_postgresql
```

File `.env` chứa thông tin nhạy cảm và đã được loại khỏi Git qua `.gitignore`.

### 3. Import dữ liệu mẫu

Chạy từ thư mục gốc của dự án:

```powershell
psql -U postgres -d bt3 -f database.sql
```

Khi được hỏi, nhập mật khẩu PostgreSQL đã cấu hình ở `DB_PASS`. Ngoài ra, có thể mở [database.sql](database.sql) bằng Query Tool của pgAdmin/DBeaver và thực thi toàn bộ file trên database `bt3`.

> File SQL tạo bảng và nạp một lượng lớn dữ liệu mẫu, vì vậy quá trình import có thể mất một lúc.

### 4. Khởi động ứng dụng

Chạy PHP development server từ đúng thư mục gốc của dự án:

```powershell
php -S localhost:8000
```

Mở trình duyệt tại:

- Cổng tra cứu: <http://localhost:8000/FE/customer/>
- Trang quản trị: <http://localhost:8000/FE/admin/>

Tài khoản quản trị mẫu được tạo bởi `database.sql`:

```text
Tên đăng nhập: admin
Mật khẩu:      123456
```

Chỉ sử dụng tài khoản này cho môi trường học tập/phát triển. Ứng dụng còn có cơ chế đăng nhập dự phòng bằng chính `DB_USER` và `DB_PASS`; không nên triển khai cơ chế này trên môi trường công khai.

Để dừng development server, nhấn `Ctrl+C` trong cửa sổ terminal đang chạy.

## Chạy trên máy phòng lab không dùng Docker

Phần này dành cho máy Windows ở phòng lab. Trước tiên, máy cần có Git, PHP và PostgreSQL. Bạn không cần Composer hoặc Node.js vì dự án không sử dụng các công cụ này.

### A. Kiểm tra phần mềm trên máy

Mở PowerShell và chạy từng lệnh:

```powershell
git --version
php -v
psql --version
php -m | Select-String -Pattern "PDO|pdo_pgsql"
```

Kết quả cần có:

- `git`, `php` và `psql` đều hiển thị phiên bản.
- Danh sách PHP extension có `PDO` và `pdo_pgsql`.

Nếu PHP đã có trong XAMPP nhưng lệnh `php` không tồn tại, có thể gọi bằng đường dẫn đầy đủ, ví dụ:

```powershell
C:\xampp\php\php.exe -v
C:\xampp\php\php.exe -S localhost:8000
```

Nếu PostgreSQL đã được cài nhưng lệnh `psql` không tồn tại, tìm thư mục `bin` của PostgreSQL và gọi bằng đường dẫn đầy đủ, ví dụ:

```powershell
& "C:\Program Files\PostgreSQL\15\bin\psql.exe" --version
```

Số phiên bản trong đường dẫn có thể là `15`, `16`, `17` hoặc phiên bản đang được cài trên máy.

Nếu thiếu phần mềm hoặc không có quyền cài đặt/chỉnh sửa `php.ini`, hãy nhờ giáo viên hoặc quản trị viên phòng lab cài PHP, PostgreSQL và bật `pdo_pgsql`. Chỉ tải bản portable khi nội quy phòng máy cho phép; dữ liệu PostgreSQL portable có thể bị xóa khi đăng xuất hoặc khởi động lại máy.

### B. Lần đầu lấy dự án về máy

Di chuyển tới thư mục được phép lưu bài, sau đó clone repository:

```powershell
cd D:\DuAn
git clone <URL_REPOSITORY> bt3
cd bt3
```

Thay `<URL_REPOSITORY>` bằng địa chỉ Git thật của dự án. Nếu repository riêng tư, đăng nhập bằng tài khoản Git hoặc Personal Access Token theo quy định của trường.

> Không dùng `git pull` khi máy chưa có thư mục dự án. Lần đầu phải dùng `git clone`; `git pull` chỉ dùng để cập nhật một bản clone đã tồn tại.

### C. Tạo file cấu hình riêng cho máy lab

File `.env` không được lưu trên Git, vì vậy mỗi máy mới cần tạo lại:

```powershell
Copy-Item .env.example .env
notepad .env
```

Điền thông tin PostgreSQL trên máy lab:

```env
DB_HOST=localhost
DB_PORT=5432
DB_NAME=bt3
DB_USER=postgres
DB_PASS=mat_khau_postgresql
```

Lưu file rồi đóng Notepad. Không commit hoặc gửi file `.env` lên repository.

### D. Tạo và import database lần đầu

Đảm bảo dịch vụ PostgreSQL đang chạy. Sau đó, tại thư mục `bt3`, chạy:

```powershell
createdb -U postgres bt3
psql -U postgres -d bt3 -f database.sql
```

Nhập mật khẩu của user `postgres` khi được hỏi. Nếu máy không nhận lệnh `createdb` và `psql`, dùng đường dẫn đầy đủ:

```powershell
& "C:\Program Files\PostgreSQL\15\bin\createdb.exe" -U postgres bt3
& "C:\Program Files\PostgreSQL\15\bin\psql.exe" -U postgres -d bt3 -f database.sql
```

Có thể thay hai lệnh trên bằng pgAdmin:

1. Mở pgAdmin và kết nối tới PostgreSQL trên máy.
2. Nhấp phải `Databases`, chọn `Create` → `Database`.
3. Đặt tên database là `bt3`.
4. Chọn database `bt3`, mở `Query Tool`.
5. Mở file `database.sql`, sau đó chạy toàn bộ nội dung.

Chỉ import `database.sql` khi tạo database lần đầu. Không import lại sau mỗi lần `git pull`, vì file hiện tại có các lệnh tạo bảng và có thể báo lỗi nếu bảng đã tồn tại.

### E. Chạy website

Từ đúng thư mục gốc `bt3`, chạy:

```powershell
php -S localhost:8000
```

Nếu sử dụng PHP của XAMPP:

```powershell
C:\xampp\php\php.exe -S localhost:8000
```

Giữ nguyên cửa sổ PowerShell này trong lúc sử dụng website, rồi mở:

- Cổng tra cứu: <http://localhost:8000/FE/customer/>
- Trang quản trị: <http://localhost:8000/FE/admin/>
- Tài khoản quản trị mẫu: `admin` / `123456`

Khi hoàn thành, nhấn `Ctrl+C` để dừng PHP server. Nếu máy lab dùng chung, nên đăng xuất khỏi tài khoản Git và xóa thông tin đăng nhập đã lưu theo nội quy của phòng máy.

### F. Các buổi học tiếp theo

Nếu thư mục dự án và database vẫn còn trên máy, không cần clone hoặc import lại. Chạy:

```powershell
cd D:\DuAn\bt3
git status
git pull
php -S localhost:8000
```

Trước khi `git pull`, dùng `git status` để kiểm tra file đang sửa. Hãy commit hoặc sao lưu thay đổi của bạn trước; không dùng lệnh xóa/reset nếu chưa chắc chắn.

File `.env` vẫn được giữ nguyên sau `git pull` vì đã nằm trong `.gitignore`. Nếu nhóm cập nhật `.env.example` với biến mới, hãy tự bổ sung biến đó vào `.env` hiện có.

Nếu phòng lab xóa dữ liệu sau mỗi buổi, cần thực hiện lại các bước B–E hoặc lưu repository trong vùng lưu trữ cá nhân được nhà trường cho phép.

## Chạy bằng XAMPP, Laragon hoặc Apache

1. Đặt toàn bộ thư mục dự án vào web root, ví dụ `C:\xampp\htdocs\bt3` hoặc `C:\laragon\www\bt3`.
2. Bật extension `pdo_pgsql` trong phiên bản PHP mà Apache đang dùng.
3. Cấu hình `.env` và import `database.sql` như hướng dẫn ở trên.
4. Khởi động Apache và PostgreSQL.
5. Truy cập `http://localhost/bt3/FE/customer/` hoặc `http://localhost/bt3/FE/admin/`.

## Cấu trúc chính

```text
bt3/
├── BE/                  # API PHP và xử lý backend
│   ├── auth/            # Đăng nhập, đăng xuất, session
│   ├── config/          # Kết nối PostgreSQL
│   ├── customer/        # API tra cứu công khai
│   ├── vehicles/        # API quản lý phương tiện
│   ├── violations/      # API quản lý vi phạm
│   ├── notifications/   # API thông báo
│   └── audit_logs/      # API nhật ký hoạt động
├── FE/
│   ├── customer/        # Giao diện tra cứu
│   └── admin/           # Giao diện quản trị
├── .env.example         # Mẫu cấu hình môi trường
└── database.sql         # Cấu trúc và dữ liệu mẫu PostgreSQL
```

## Lỗi thường gặp

- `could not find driver`: extension `pdo_pgsql` chưa được bật hoặc PHP đang đọc nhầm file `php.ini`. Dùng `php --ini` để kiểm tra.
- `connection refused`: PostgreSQL chưa chạy hoặc `DB_HOST`/`DB_PORT` trong `.env` chưa đúng.
- `password authentication failed`: kiểm tra lại `DB_USER` và `DB_PASS`.
- `database "bt3" does not exist`: tạo database và import `database.sql` trước khi chạy ứng dụng.
- API trả về `401`: phiên quản trị đã hết hạn; đăng nhập lại tại trang admin.
- Giao diện thiếu định dạng hoặc icon: kiểm tra kết nối Internet vì Bootstrap được tải từ CDN.

> PHP built-in server chỉ phù hợp để phát triển và chạy thử, không nên dùng làm máy chủ production.
