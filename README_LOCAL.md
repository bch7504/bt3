# Hướng dẫn chạy dự án cục bộ qua XAMPP

Tài liệu này hướng dẫn chi tiết cách cài đặt Git, tải mã nguồn, cấu hình và khởi chạy dự án bằng **XAMPP** kết hợp với cơ sở dữ liệu **PostgreSQL** trên một máy tính mới hoàn toàn.

> [!NOTE]  
> XAMPP tích hợp sẵn Apache và PHP, nhưng mặc định đi kèm MySQL/MariaDB. Vì dự án này sử dụng PostgreSQL làm cơ sở dữ liệu, bạn sẽ cần **cài thêm PostgreSQL** và **kích hoạt driver pgsql trong XAMPP**.

---

## 🛠️ Quy trình thiết lập từng bước

### Bước 1: Cài đặt các công cụ & phần mềm cần thiết

#### 1. Cài đặt Git (Để tải và cập nhật mã nguồn)
- **Windows:** 
  1. Tải bộ cài tại [Trang chủ Git SCM](https://git-scm.com/download/win).
  2. Chạy file cài đặt, nhấn **Next** giữ nguyên các tùy chọn mặc định để hoàn tất.
  3. *Mẹo:* Bạn cũng có thể mở Command Prompt/PowerShell và chạy lệnh: `winget install --id Git.Git -e --source winget`.
- **macOS:** Mở Terminal và chạy lệnh: `brew install git`.
- **Linux (Ubuntu/Debian):** Mở Terminal và chạy: `sudo apt update && sudo apt install -y git`.

#### 2. Cài đặt XAMPP
- Tải XAMPP phiên bản mới nhất (có PHP >= 8.0) tại [Trang chủ XAMPP](https://www.apachefriends.org/index.html).
- Tiến hành cài đặt mặc định vào thư mục `C:\xampp`.

#### 3. Cài đặt PostgreSQL Server (Cài riêng không kèm pgAdmin 4)
- Tải bộ cài đặt PostgreSQL cho Windows tại [PostgreSQL Downloads](https://www.postgresql.org/download/windows/) (khuyên dùng bản 15 hoặc 16).
- **Mẹo cài riêng (Không cài pgAdmin 4 để nhẹ máy):** Khi trình cài đặt hiện bảng chọn các thành phần cài đặt (Select Components), bạn hãy **bỏ tích chọn ở dòng `pgAdmin 4` và `Stack Builder`**. Chỉ giữ lại tích chọn ở dòng `PostgreSQL Server` và `Command Line Tools` (chứa lệnh `psql` cần dùng).
- Trong lúc cài đặt, hãy thiết lập và ghi nhớ mật khẩu tài khoản quản trị `postgres` (ví dụ: `123456`).
- Cổng kết nối (Port) mặc định là `5432`.

---

### Bước 2: Tải mã nguồn dự án về máy (Clone)
Thay vì tải zip thủ công, ta sẽ clone repository trực tiếp vào thư mục chạy web của XAMPP (`htdocs`):

1. Mở Command Prompt (cmd), PowerShell hoặc Git Bash.
2. Di chuyển vào thư mục `htdocs` của XAMPP:
   ```cmd
   cd C:\xampp\htdocs
   ```
3. Chạy lệnh clone repository dự án về máy:
   ```bash
   git clone https://github.com/bch7504/bt3.git
   ```
Sau khi chạy xong, toàn bộ mã nguồn sẽ nằm tại đường dẫn: `C:\xampp\htdocs\bt3`.

---

### Bước 3: Kích hoạt Driver PostgreSQL trong XAMPP
Để PHP của XAMPP có thể kết nối được với PostgreSQL, bạn cần bật hai extension `pgsql` và `pdo_pgsql`:

1. Mở **XAMPP Control Panel**.
2. Tại dòng **Apache**, nhấn nút **Config** -> chọn **PHP (php.ini)**.
3. Trình soạn thảo Notepad sẽ mở file `php.ini`. Nhấn `Ctrl + F` để tìm các dòng sau:
   ```ini
   ;extension=pgsql
   ;extension=pdo_pgsql
   ```
4. **Xóa dấu chấm phẩy (`;`)** ở đầu cả hai dòng trên để kích hoạt extension:
   ```ini
   extension=pgsql
   extension=pdo_pgsql
   ```
5. Lưu file lại (`Ctrl + S`) và đóng Notepad.

---

### Bước 4: Tạo Cơ sở dữ liệu và nạp dữ liệu mẫu

Nếu không cài pgAdmin 4, bạn có thể dễ dàng tạo database và nạp dữ liệu hoàn toàn bằng dòng lệnh (Command Prompt/PowerShell) như sau:

1. **Tạo Database tên là `bt3`:**
   Mở Command Prompt (cmd) và chạy lệnh dưới đây (nhập mật khẩu của user `postgres` khi được hỏi):
   ```cmd
   "C:\Program Files\PostgreSQL\<VERSION>\bin\psql.exe" -U postgres -c "CREATE DATABASE bt3;"
   ```
   *(Thay `<VERSION>` bằng số phiên bản bạn cài đặt, ví dụ `15` hoặc `16`)*

2. **Nạp dữ liệu mẫu từ `database.sql`:**
   Tiếp tục chạy lệnh sau để import cấu trúc bảng và dữ liệu mẫu vào database `bt3`:
   ```cmd
   "C:\Program Files\PostgreSQL\<VERSION>\bin\psql.exe" -U postgres -d bt3 -f C:\xampp\htdocs\bt3\database.sql
   ```

*(Nếu bạn vẫn muốn dùng giao diện trực quan, bạn có thể tải các phần mềm nhẹ hơn pgAdmin 4 như **TablePlus** hoặc **DBeaver** để quản lý database PostgreSQL)*

---

### Bước 5: Cấu hình file môi trường `.env`
1. Truy cập thư mục `C:\xampp\htdocs\bt3`.
2. Sao chép file `.env.example` và đổi tên thành `.env`.
3. Mở file `.env` bằng Notepad hoặc VS Code và cập nhật thông tin kết nối PostgreSQL cục bộ của bạn:
   ```env
   DB_HOST=localhost
   DB_PORT=5432
   DB_NAME=bt3
   DB_USER=postgres
   DB_PASS=mật_khẩu_postgresql_của_bạn
   PGSSLMODE=disable
   ```
   > **Lưu ý:** Đặt `PGSSLMODE=disable` vì kết nối PostgreSQL nội bộ trên máy cá nhân không yêu cầu mã hóa SSL như Supabase.

---

### Bước 6: Khởi chạy và truy cập ứng dụng
1. Mở **XAMPP Control Panel**.
2. Nhấn nút **Start** ở dòng **Apache** (nút này sẽ chuyển sang màu xanh và hiển thị cổng kết nối).
3. Mở trình duyệt web và truy cập các đường dẫn sau:
   - **Giao diện Tra cứu (Người dân):** [http://localhost/bt3/FE/customer/](http://localhost/bt3/FE/customer/)
   - **Trang quản trị hệ thống (Admin):** [http://localhost/bt3/FE/admin/](http://localhost/bt3/FE/admin/)
     - *Tài khoản quản trị mặc định:* `admin` / `123456`

---

## 🔍 Khắc phục lỗi thường gặp

1. **Lỗi: `could not find driver`**
   - **Nguyên nhân:** XAMPP chưa load thành công extension PostgreSQL hoặc bạn chưa khởi động lại Apache sau khi sửa `php.ini`.
   - **Khắc phục:** Mở XAMPP Control Panel, bấm **Stop** Apache rồi bấm **Start** lại. Kiểm tra xem đã lưu đúng file `php.ini` chưa.

2. **Lỗi: `password authentication failed for user "postgres"`**
   - **Nguyên nhân:** Sai mật khẩu PostgreSQL trong file `.env`.
   - **Khắc phục:** Hãy đảm bảo `DB_PASS` trong file `.env` khớp hoàn toàn với mật khẩu bạn đã thiết lập khi cài đặt PostgreSQL.

3. **Lỗi: `connection refused`**
   - **Nguyên nhân:** Dịch vụ PostgreSQL Server cục bộ chưa được khởi chạy.
   - **Khắc phục:** Nhấn tổ hợp phím `Windows + R`, gõ `services.msc` và nhấn Enter. Tìm dịch vụ có tên **postgresql...**, chuột phải chọn **Start** để khởi động dịch vụ.
