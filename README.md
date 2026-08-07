# Hệ thống tra cứu và xử lý vi phạm giao thông

Ứng dụng PHP thuần gồm cổng tra cứu dành cho người dân và trang quản trị phương tiện, vi phạm, thông báo, nhật ký hoạt động.

Dự án được hướng dẫn chạy trên **GitHub Codespaces** và sử dụng database PostgreSQL đã tạo trên **Supabase**.

## Chuẩn bị

Cần có:

- Tài khoản GitHub có quyền truy cập repository.
- Một Supabase project đã có cấu trúc bảng và dữ liệu của dự án.
- Database password của Supabase project.

Nếu dữ liệu đã tồn tại trên Supabase thì không cần cài PostgreSQL Server trong Codespaces, không cần tạo database và không import lại `database.sql`.

## 1. Tạo GitHub Codespace

1. Mở repository <https://github.com/bch7504/bt3>.
2. Chọn `Code` → `Codespaces`.
3. Chọn `Create codespace on main`.
4. Chờ Codespaces mở trình soạn thảo.

Mã nguồn đã được GitHub clone tự động nên không chạy `git clone` thêm lần nữa.

## 2. Cài PHP trong Codespaces

Mở Terminal và chạy:

```bash
sudo apt update
sudo apt install -y php-cli php-pgsql postgresql-client
```

Kiểm tra môi trường:

```bash
php -v
php -m | grep -E 'PDO|pdo_pgsql'
psql --version
```

Kết quả PHP phải có cả `PDO` và `pdo_pgsql`.

> Chỉ cài `postgresql-client` để kiểm tra kết nối Supabase. Không cần cài hoặc khởi động PostgreSQL Server trong Codespaces.

## 3. Lấy thông tin kết nối Supabase

Trong Supabase Dashboard:

1. Mở project đang chứa dữ liệu.
2. Nhấn `Connect`.
3. Chọn `Session pooler`.
4. Sao chép Host, Port, Database và User.
5. Sử dụng database password đã đặt khi tạo project.

Thông tin Session pooler thường có dạng:

```text
Host:     aws-0-<region>.pooler.supabase.com
Port:     5432
Database: postgres
User:     postgres.<project-ref>
Password: database password của project
```

Lưu ý:

- Phải sao chép giá trị thật từ Supabase, không dùng nguyên giá trị ví dụ.
- Database password không phải `anon key` hoặc `service_role key`.
- Dùng **Session pooler cổng `5432`**.
- Không dùng Transaction pooler cổng `6543` vì dự án sử dụng prepared statements.

Tham khảo: [Supabase – Connect to your database](https://supabase.com/docs/guides/database/connecting-to-postgres).

## 4. Tạo file `.env`

Tại thư mục gốc dự án trong Codespaces, chạy:

```bash
cp .env.example .env
```

Mở file `.env` trong Explorer và thay các giá trị mẫu bằng thông tin lấy từ Supabase:

```env
DB_HOST=aws-0-your-region.pooler.supabase.com
DB_PORT=5432
DB_NAME=postgres
DB_USER=postgres.your_project_ref
DB_PASS=your_supabase_database_password
PGSSLMODE=require
```

Ví dụ, nếu Supabase cung cấp username `postgres.abcdefghijklmnop`, phải ghi đầy đủ:

```env
DB_USER=postgres.abcdefghijklmnop
```

File `.env` chứa mật khẩu thật và đã được `.gitignore` loại trừ. Không commit hoặc gửi file này lên GitHub.

## 5. Kiểm tra database Supabase

Có thể kiểm tra kết nối bằng `psql`. Thay Host và User bằng giá trị thật:

```bash
psql "host=HOST_SUPABASE port=5432 dbname=postgres user=postgres.PROJECT_REF sslmode=require" -W
```

Nhập database password khi được hỏi. Sau khi kết nối thành công, chạy:

```sql
\dt
SELECT COUNT(*) FROM vehicles;
\q
```

Dự án cần các bảng chính:

```text
vehicles
violations
admins
notifications
audit_logs
```

Nếu các bảng đã có dữ liệu thì không chạy `database.sql`.

Tài khoản `admin` / `123456` chỉ hoạt động nếu bảng `admins` trên Supabase đã chứa bản ghi quản trị mẫu tương ứng.

## 6. Chạy dự án

Tại thư mục gốc chứa `BE`, `FE` và `.env`, chạy:

```bash
php -S 0.0.0.0:8000 -t .
```

Phải dùng `0.0.0.0` để GitHub Codespaces có thể forward cổng. Giữ Terminal này mở trong lúc sử dụng website.

## 7. Mở website

1. Mở tab `PORTS` ở khu vực Terminal của Codespaces.
2. Tìm cổng `8000`.
3. Nhấn biểu tượng quả địa cầu hoặc chọn `Open in Browser`.

GitHub sẽ cấp một URL có dạng:

```text
https://<ten-codespace>-8000.app.github.dev
```

Truy cập:

```text
https://<ten-codespace>-8000.app.github.dev/FE/customer/
https://<ten-codespace>-8000.app.github.dev/FE/admin/
```

Không mở `localhost:8000` trực tiếp trên trình duyệt máy cá nhân. Nên giữ cổng `8000` ở chế độ private vì dự án có tài khoản quản trị mẫu.

Có thể thử tra cứu biển số `30A-123.45` nếu dữ liệu này tồn tại trên Supabase.

Để dừng PHP server, quay lại Terminal và nhấn `Ctrl+C`.

## Những lần mở Codespace sau

Khi mở lại cùng một Codespace, file `.env` và các package thường vẫn còn. Chỉ cần chạy:

```bash
php -S 0.0.0.0:8000 -t .
```

Nếu đã xóa Codespace và tạo Codespace mới, thực hiện lại các bước cài PHP và tạo `.env`. Dữ liệu trên Supabase không bị mất vì được lưu bên ngoài Codespace.

## Lỗi thường gặp

### `could not find driver`

PHP chưa có extension PostgreSQL:

```bash
sudo apt install -y php-pgsql
php -m | grep -E 'PDO|pdo_pgsql'
```

Sau đó dừng và chạy lại PHP server.

### `password authentication failed`

Kiểm tra lại:

- `DB_PASS` phải là database password.
- `DB_USER` của Session pooler thường có dạng `postgres.<project-ref>`.
- Không dùng `anon key` hoặc `service_role key` làm mật khẩu.

### `could not translate host name`

Sao chép lại đúng Host tại `Supabase Dashboard` → `Connect` → `Session pooler`.

### `connection refused` hoặc timeout

Kiểm tra project Supabase có đang hoạt động và có cấu hình Network Restrictions chặn kết nối hay không.

### Website báo bảng không tồn tại

Kiểm tra `.env` đang trỏ tới đúng Supabase project và các bảng nằm trong schema `public`.

### Không thấy cổng `8000`

Đảm bảo Terminal vẫn đang chạy:

```bash
php -S 0.0.0.0:8000 -t .
```

Sau đó vào tab `PORTS`, chọn `Add Port` và nhập `8000`.

## Cấu trúc chính

```text
bt3/
├── BE/                  # API PHP và xử lý backend
│   ├── auth/            # Đăng nhập, đăng xuất, session
│   ├── config/          # Kết nối PostgreSQL/Supabase
│   ├── customer/        # API tra cứu công khai
│   ├── vehicles/        # API quản lý phương tiện
│   ├── violations/      # API quản lý vi phạm
│   ├── notifications/   # API thông báo
│   └── audit_logs/      # API nhật ký hoạt động
├── FE/
│   ├── customer/        # Giao diện tra cứu
│   └── admin/           # Giao diện quản trị
├── .env.example         # Mẫu cấu hình Supabase
└── database.sql         # Cấu trúc và dữ liệu mẫu, không chạy lại nếu Supabase đã có dữ liệu
```

> PHP built-in server chỉ phù hợp để học tập, phát triển và chạy thử; không dùng trực tiếp làm máy chủ production.
