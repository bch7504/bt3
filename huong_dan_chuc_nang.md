# Hướng Dẫn Chi Tiết Các Chức Năng Hệ Thống (Thêm, Sửa, Xóa & Tra Cứu)

Tài liệu này hướng dẫn chi tiết về vị trí mã nguồn, cơ chế hoạt động, cách thức thao tác trên giao diện và lý do xuất hiện của các chức năng cốt lõi trong hệ thống: **Quản lý phương tiện**, **Quản lý vi phạm** (dành cho Admin) và **Tra cứu vi phạm giao thông** (dành cho Khách hàng).

---

## 1. QUẢN LÝ PHƯƠNG TIỆN (VEHICLE MANAGEMENT)

### A. Chức năng THÊM phương tiện mới
#### 1. Vị trí trong mã nguồn (Where in Code)
- **Nút kích hoạt (HTML):** [vehicles.php (Dòng 23-25)](file:///d:/bt3/FE/admin/pages/vehicles.php#L23-L25). Là nút màu xanh dương nằm góc trên bên phải giao diện quản lý:
  ```html
  <button type="button" class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreateVehicle">
      <i class="bi bi-plus-circle-fill"></i> Thêm phương tiện mới
  </button>
  ```
- **Giao diện Modal Form (HTML):** [vehicles.php (Dòng 109-198)](file:///d:/bt3/FE/admin/pages/vehicles.php#L109-L198) có `id="modalCreateVehicle"`.
- **Hành vi kiểm soát nhập liệu (JS):** [vehicles.php (Dòng 255-260)](file:///d:/bt3/FE/admin/pages/vehicles.php#L255-L260) tự động viết hoa biển số khi admin nhập.
- **Xử lý JavaScript gửi đi:** [vehicles.php (Dòng 273-311)](file:///d:/bt3/FE/admin/pages/vehicles.php#L273-L311) lắng nghe sự kiện `submit` của `#createVehicleForm`.
- **API Backend xử lý:** [BE/vehicles/create.php](file:///d:/bt3/BE/vehicles/create.php).

#### 2. Cách thức hoạt động trong Code (How it Runs)
Hệ thống thực hiện chức năng này qua các bước liên tiếp dưới đây:
1. **Chặn reload trang:** Khi Admin bấm nút lưu, sự kiện `submit` của biểu mẫu `#createVehicleForm` kích hoạt hàm xử lý tại [vehicles.php (Dòng 273)](file:///d:/bt3/FE/admin/pages/vehicles.php#L273), JS gọi `e.preventDefault()` tại [vehicles.php (Dòng 274)](file:///d:/bt3/FE/admin/pages/vehicles.php#L274) để ngăn trình duyệt reload trang.
2. **Khóa giao diện tránh spam:** JS vô hiệu hóa nút submit `#btn-submit-create` tại [vehicles.php (Dòng 281-282)](file:///d:/bt3/FE/admin/pages/vehicles.php#L281-L282).
3. **Đóng gói dữ liệu:** JS gom dữ liệu từ Form thành một đối tượng JSON thông qua `FormData` tại [vehicles.php (Dòng 284-285)](file:///d:/bt3/FE/admin/pages/vehicles.php#L284-L285).
4. **Gửi yêu cầu lên máy chủ:** JS gọi hàm `fetch()` gửi yêu cầu `POST` tới API Backend: `../../BE/vehicles/create.php` tại [vehicles.php (Dòng 287-293)](file:///d:/bt3/FE/admin/pages/vehicles.php#L287-L293).
5. **Xác thực quyền Admin:** File `create.php` tiếp nhận request, gọi hàm `check_admin_login()` tại [create.php (Dòng 9)](file:///d:/bt3/BE/vehicles/create.php#L9) để xác thực quyền quản trị viên.
6. **Kiểm tra dữ liệu đầu vào:** Backend parse luồng dữ liệu JSON từ `php://input` và kiểm tra các trường bắt buộc (`license_plate`, `owner_name`) tại [create.php (Dòng 12-33)](file:///d:/bt3/BE/vehicles/create.php#L12-L33).
7. **Kiểm tra biển kiểm soát trùng lặp:** Backend gọi `$conn->prepare` và `$stmt->execute()` tại [create.php (Dòng 37-43)](file:///d:/bt3/BE/vehicles/create.php#L37-L43) để chạy câu lệnh SQL kiểm tra trùng biển số xe:
   ```sql
   SELECT COUNT(*) FROM vehicles WHERE REPLACE(REPLACE(UPPER(license_plate), '-', ''), ' ', '') = REPLACE(REPLACE(UPPER(?), '-', ''), ' ', '')
   ```
8. **Từ chối nếu trùng lặp:** Nếu biển số đã tồn tại, Backend gọi `http_response_code(400)` và trả về thông tin lỗi qua `echo json_encode()` tại [create.php (Dòng 45-49)](file:///d:/bt3/BE/vehicles/create.php#L45-L49).
9. **Ghi nhận phương tiện vào Cơ sở dữ liệu:** Nếu hợp lệ, Backend thực thi câu lệnh SQL `INSERT INTO vehicles (...)` qua `$stmt->execute()` tại [create.php (Dòng 52-66)](file:///d:/bt3/BE/vehicles/create.php#L52-L66).
10. **Ghi nhật ký hệ thống:** Backend gọi hàm `write_audit_log($conn, ...)` để lưu lịch sử hoạt động tại [create.php (Dòng 71)](file:///d:/bt3/BE/vehicles/create.php#L71).
11. **Trả về phản hồi:** Backend trả về JSON thành công qua `echo json_encode()` tại [create.php (Dòng 73-77)](file:///d:/bt3/BE/vehicles/create.php#L73-L77).
12. **Cập nhật giao diện và nạp lại dữ liệu:** JS nhận phản hồi qua chuỗi các hàm `.then()` tại [vehicles.php (Dòng 295-310)](file:///d:/bt3/FE/admin/pages/vehicles.php#L295-L310):
    - Nếu thành công: Gọi hàm `showToast(...)` hiển thị thông báo thành công màu xanh lá (`bg-success`), reset sạch form, đóng Modal bằng cách gọi API Bootstrap Modal `bootstrap.Modal.getInstance(...).hide()`, và gọi hàm `loadVehicles(currentPage, currentSearch)` để cập nhật danh sách bảng.
    - Nếu thất bại: Gọi hàm `showToast(...)` hiển thị thông báo lỗi màu đỏ (`bg-danger`).

#### 3. Cách bấm/thao tác trên giao diện (How to Click/Operate)
- Đăng nhập vào trang Quản trị, truy cập mục **"Quản lý phương tiện"**.
- Bấm vào nút xanh **"Thêm phương tiện mới"** ở góc trên bên phải.
- Form Modal hiện lên, nhập thông tin: *Biển kiểm soát (Bắt buộc, tự động viết hoa), Loại phương tiện, Hãng sản xuất, Số loại (Model), Màu sơn, Số máy, Số khung, Ngày đăng ký, Hạn đăng kiểm, Trạng thái (Bình thường/Tạm giữ/Mất cắp)* và phần thông tin chủ sở hữu (*Họ tên chủ xe - Bắt buộc, Số CCCD, Số điện thoại, Email*).
- Nhấp vào nút **"Lưu lại"** ở cuối form để gửi đi, hoặc nhấp **"Hủy bỏ"** để đóng modal mà không lưu.

#### 4. Tại sao lại hiện lên (Why it Appears)
- Chức năng thêm mới phương tiện phục vụ cho mục đích đăng ký và quản lý xe cơ giới lưu hành. Nút thêm mới luôn luôn xuất hiện ở góc trên bên phải trang Quản lý phương tiện khi người dùng đăng nhập thành công dưới quyền quản trị viên, giúp họ có thể cập nhật nhanh các xe mới vào hệ thống quốc gia.

---

### B. Chức năng SỬA thông tin phương tiện
#### 1. Vị trí trong mã nguồn (Where in Code)
- **Nút kích hoạt trong bảng (HTML):** [vehicles.php (Dòng 441-443)](file:///d:/bt3/FE/admin/pages/vehicles.php#L441-L443) nằm trong cột Thao tác (Actions) ở mỗi hàng phương tiện:
  ```html
  <button type="button" class="btn btn-outline-warning btn-sm px-2 rounded-3 text-dark border-secondary border-opacity-25" title="Chỉnh sửa" onclick="editVehicle(${v.id})">
      <i class="bi bi-pencil-square"></i>
  </button>
  ```
- **Giao diện Modal Chỉnh sửa (HTML):** [vehicles.php (Dòng 201-222)](file:///d:/bt3/FE/admin/pages/vehicles.php#L201-L222) có `id="modalEditVehicle"`.
- **Hàm JavaScript lấy dữ liệu cũ:** [vehicles.php (Dòng 663-767)](file:///d:/bt3/FE/admin/pages/vehicles.php#L663-L767) (`function editVehicle(id)`).
- **Xử lý JavaScript gửi form cập nhật:** [vehicles.php (Dòng 313-352)](file:///d:/bt3/FE/admin/pages/vehicles.php#L313-L352) lắng nghe sự kiện `submit` của `#editVehicleForm`.
- **API Backend xử lý cập nhật:** [BE/vehicles/edit.php](file:///d:/bt3/BE/vehicles/edit.php).

#### 2. Cách thức hoạt động trong Code (How it Runs)
Hệ thống thực hiện chức năng này qua các bước liên tiếp dưới đây:
1. **Lắng nghe sự kiện click sửa:** Khi Admin bấm nút sửa ở cột Thao tác, sự kiện click gọi hàm **`editVehicle(id)`** được kích hoạt tại [vehicles.php (Dòng 663)](file:///d:/bt3/FE/admin/pages/vehicles.php#L663).
2. **Hiển thị Modal chỉnh sửa:** Hàm `editVehicle` mở Modal `#modalEditVehicle` bằng cách gọi `.show()` tại [vehicles.php (Dòng 664)](file:///d:/bt3/FE/admin/pages/vehicles.php#L664).
3. **Hiển thị trạng thái chờ:** JS nạp biểu tượng spinner loading tạm thời vào thân modal (`#editVehicleBody`) tại [vehicles.php (Dòng 667-673)](file:///d:/bt3/FE/admin/pages/vehicles.php#L667-L673).
4. **Yêu cầu dữ liệu cũ từ máy chủ:** JS gọi hàm `fetch()` gửi yêu cầu `GET` tới API Backend: `../../BE/vehicles/view.php?id=${id}` tại [vehicles.php (Dòng 675)](file:///d:/bt3/FE/admin/pages/vehicles.php#L675).
5. **Truy vấn cơ sở dữ liệu tại Backend:** File `view.php` kết nối DB PostgreSQL và gọi `$conn->prepare` / `$stmt->execute` để truy vấn thông tin phương tiện tại [view.php (Dòng 20-22)](file:///d:/bt3/BE/vehicles/view.php#L20-L22) và danh sách vi phạm liên quan tại [view.php (Dòng 31-33)](file:///d:/bt3/BE/view.php#L31-L33) bằng SQL, sau đó trả về dữ liệu JSON qua hàm `echo json_encode()`.
6. **Vẽ biểu mẫu chỉnh sửa:** JS nhận dữ liệu trong hàm `.then()` tại [vehicles.php (Dòng 683-757)](file:///d:/bt3/FE/admin/pages/vehicles.php#L683-L757), ghi giá trị `v.id` vào input ẩn `edit_id` tại [vehicles.php (Dòng 684)](file:///d:/bt3/FE/admin/pages/vehicles.php#L684), dùng chuỗi HTML động để vẽ Form chỉnh sửa chứa toàn bộ thông tin cũ và ghi đè vào thân modal.
7. **Lắng nghe sự kiện gửi biểu mẫu cập nhật:** Khi Admin chỉnh sửa xong và bấm **"Cập nhật"**, sự kiện submit của `#editVehicleForm` được kích hoạt tại [vehicles.php (Dòng 313)](file:///d:/bt3/FE/admin/pages/vehicles.php#L313).
8. **Chặn reload trang và khóa nút submit:** JS chặn reload trang qua `e.preventDefault()` tại [vehicles.php (Dòng 314-315)](file:///d:/bt3/FE/admin/pages/vehicles.php#L314-L315), vô hiệu hóa nút gửi (`#btn-submit-edit`) tại [vehicles.php (Dòng 322-323)](file:///d:/bt3/FE/admin/pages/vehicles.php#L322-L323).
9. **Gửi dữ liệu cập nhật lên máy chủ:** JS gom dữ liệu form thành đối tượng JSON và gọi hàm `fetch()` gửi yêu cầu `POST` tới API: `../../BE/vehicles/edit.php` tại [vehicles.php (Dòng 325-334)](file:///d:/bt3/FE/admin/pages/vehicles.php#L325-L334).
10. **Xác thực và cập nhật tại Backend:** File `edit.php` kiểm tra quyền admin qua `check_admin_login()` tại [edit.php (Dòng 9)](file:///d:/bt3/BE/vehicles/edit.php#L9), kiểm tra trùng biển số qua `$stmt->execute()` tại [edit.php (Dòng 38-51)](file:///d:/bt3/BE/vehicles/edit.php#L38-L51), và thực hiện câu lệnh cập nhật SQL `UPDATE vehicles SET ... WHERE id = ?` qua `$conn->prepare` / `$stmt->execute` tại [edit.php (Dòng 54-64)](file:///d:/bt3/BE/vehicles/edit.php#L54-L64).
11. **Ghi nhật ký cập nhật và phản hồi:** Backend ghi log hoạt động UPDATE qua hàm `write_audit_log(...)` tại [edit.php (Dòng 67)](file:///d:/bt3/BE/vehicles/edit.php#L67), trả về phản hồi thành công hoặc thất bại dạng JSON qua `echo json_encode(...)` tại [edit.php (Dòng 69-72)](file:///d:/bt3/BE/vehicles/edit.php#L69-L72).
12. **Cập nhật giao diện:** JS bắt kết quả trả về trong chuỗi các hàm `.then()` tại [vehicles.php (Dòng 336-347)](file:///d:/bt3/FE/admin/pages/vehicles.php#L336-L347), gọi hàm `showToast(...)` hiển thị Toast xanh lá nếu thành công, đóng Modal qua `bootstrap.Modal.getInstance(...).hide()`, và gọi hàm `loadVehicles(currentPage, currentSearch)` để tải lại danh sách xe mới nhất lên bảng.

#### 3. Cách bấm/thao tác trên giao diện (How to Click/Operate)
- Tìm phương tiện cần chỉnh sửa trên bảng danh sách.
- Nhấp vào nút màu vàng có biểu tượng cây bút viết ở cột cuối cùng ("Thao tác").
- Modal chỉnh sửa mở ra và hiển thị sẵn toàn bộ thông tin cũ của xe.
- Thực hiện sửa đổi các thông tin cần thiết.
- Nhấp nút vàng **"Cập nhật"** để lưu lại, hoặc nhấp **"Hủy bỏ"** để thoát.

#### 4. Tại sao lại hiện lên (Why it Appears)
- Nút Sửa được render động bằng JavaScript cho từng dòng xe trên bảng phương tiện dựa vào danh sách dữ liệu lấy từ API backend. Việc này đảm bảo admin có thể truy cập trực tiếp form chỉnh sửa cho chính xác phương tiện của hàng đó thông qua khóa chính `id`.

---

### C. Chức năng XÓA phương tiện
#### 1. Vị trí trong mã nguồn (Where in Code)
- **Nút kích hoạt trong bảng (HTML):** [vehicles.php (Dòng 444-446)](file:///d:/bt3/FE/admin/pages/vehicles.php#L444-L446):
  ```html
  <button type="button" class="btn btn-outline-danger btn-sm px-2 rounded-3 border-secondary border-opacity-25" title="Xóa" onclick="confirmDeleteVehicle(${v.id}, '${v.license_plate}')">
      <i class="bi bi-trash-fill"></i>
  </button>
  ```
- **Modal xác nhận xóa (HTML):** [vehicles.php (Dòng 225-246)](file:///d:/bt3/FE/admin/pages/vehicles.php#L225-L246) có `id="modalDeleteVehicle"`.
- **Hàm JavaScript hiển thị Modal & gán hành động xóa:** [vehicles.php (Dòng 770-801)](file:///d:/bt3/FE/admin/pages/vehicles.php#L770-L801) (`function confirmDeleteVehicle(id, plate)`).
- **API Backend xử lý xóa:** [BE/vehicles/delete.php](file:///d:/bt3/BE/vehicles/delete.php).

#### 2. Cách thức hoạt động trong Code (How it Runs)
Hệ thống thực hiện chức năng này qua các bước liên tiếp dưới đây:
1. **Lắng nghe sự kiện click xóa:** Khi Admin bấm nút Xóa trên bảng, sự kiện click gọi hàm **`confirmDeleteVehicle(id, plate)`** được kích hoạt tại [vehicles.php (Dòng 770)](file:///d:/bt3/FE/admin/pages/vehicles.php#L770).
2. **Điền thông tin và gán sự kiện xóa động:** Hàm `confirmDeleteVehicle` điền biển kiểm soát của xe cần xóa vào nhãn cảnh báo `#delete_license_plate_label` tại [vehicles.php (Dòng 771)](file:///d:/bt3/FE/admin/pages/vehicles.php#L771), đồng thời thiết lập gán sự kiện click động cho nút **"Xác nhận xóa"** (`#delete_vehicle_btn`) trong Modal tại [vehicles.php (Dòng 775-798)](file:///d:/bt3/FE/admin/pages/vehicles.php#L775-L798) trỏ tới hàm ẩn xử lý gửi yêu cầu xóa.
3. **Hiển thị Modal xác nhận xóa:** JS mở Modal xác nhận `#modalDeleteVehicle` bằng cách gọi `.show()` tại [vehicles.php (Dòng 800)](file:///d:/bt3/FE/admin/pages/vehicles.php#L800).
4. **Chặn spam và gửi yêu cầu xóa:** Khi Admin bấm **"Xác nhận xóa"** trong Modal, nút chuyển sang trạng thái loading và bị disabled tại [vehicles.php (Dòng 776-777)](file:///d:/bt3/FE/admin/pages/vehicles.php#L776-L777), sau đó thực hiện gọi hàm `fetch()` của JavaScript gửi request `GET` tới API Backend: `../../BE/vehicles/delete.php?id=${id}` tại [vehicles.php (Dòng 779)](file:///d:/bt3/FE/admin/pages/vehicles.php#L779).
5. **Xác thực quyền Admin và kiểm tra xe tại Backend:** File `delete.php` xác thực quyền admin qua `check_admin_login()` tại [delete.php (Dòng 9)](file:///d:/bt3/BE/vehicles/delete.php#L9), thực hiện lấy thông tin biển số xe và kiểm tra xe tồn tại bằng SQL qua `$stmt->execute()` tại [delete.php (Dòng 23-31)](file:///d:/bt3/BE/vehicles/delete.php#L23-L31).
6. **Kiểm tra ràng buộc lỗi vi phạm:** Backend truy vấn xem xe này có lịch sử vi phạm giao thông nào trong bảng `violations` hay không tại [delete.php (Dòng 35-37)](file:///d:/bt3/BE/violations/delete.php#L35-L37) bằng lệnh:
   ```sql
   SELECT COUNT(*) FROM violations WHERE vehicle_id = ?
   ```
7. **Từ chối nếu xe có lỗi phạt nguội:** Nếu số lỗi vi phạm > 0, API từ chối xóa và trả về mã lỗi `400` kèm thông báo lỗi qua `echo json_encode()` tại [delete.php (Dòng 39-43)](file:///d:/bt3/BE/vehicles/delete.php#L39-L43).
8. **Thực hiện lệnh xóa xe:** Nếu xe không có lỗi vi phạm nào, tiến hành thực hiện lệnh SQL xóa xe tại [delete.php (Dòng 46-47)](file:///d:/bt3/BE/vehicles/delete.php#L46-L47) qua `$stmt->execute()`: `DELETE FROM vehicles WHERE id = ?`.
9. **Ghi nhật ký xóa và phản hồi:** Backend gọi hàm `write_audit_log($conn, ...)` tại [delete.php (Dòng 50)](file:///d:/bt3/BE/vehicles/delete.php#L50), trả về JSON thành công qua `echo json_encode()` tại [delete.php (Dòng 52-55)](file:///d:/bt3/BE/vehicles/delete.php#L52-L55).
10. **Đóng Modal và cập nhật giao diện:** JS đóng Modal xác nhận bằng cách gọi API Bootstrap Modal `bootstrap.Modal.getInstance(...).hide()` tại [vehicles.php (Dòng 784)](file:///d:/bt3/FE/admin/pages/vehicles.php#L784). JS tiếp tục bắt phản hồi JSON qua chuỗi xử lý `.then()`:
    - Nếu thành công: Gọi hàm `showToast(...)` hiển thị thông báo thành công màu xanh lá (`bg-success`), và gọi hàm `loadVehicles(currentPage, currentSearch)` tại [vehicles.php (Dòng 788)](file:///d:/bt3/FE/admin/pages/vehicles.php#L788) để tải lại danh sách xe mới nhất.
    - Nếu thất bại: Gọi hàm `showToast(...)` hiển thị Toast màu đỏ (`bg-danger`) kèm nguyên nhân lỗi.

#### 3. Cách bấm/thao tác trên giao diện (How to Click/Operate)
- Nhấp vào nút màu đỏ có hình thùng rác ở cột Thao tác của xe cần xóa.
- Khi Modal cảnh báo xác nhận xóa hiện ra, xem kỹ biển kiểm soát hiển thị trên modal.
- Bấm nút đỏ **"Xác nhận xóa"** để hoàn tất việc xóa, hoặc bấm **"Hủy bỏ"** để giữ lại.

#### 4. Tại sao lại hiện lên (Why it Appears)
- Nút Xóa xuất hiện ở cột thao tác của từng hàng phương tiện để quản trị viên dọn dẹp các xe nhập sai thông tin hoặc xe không còn đăng ký lưu hành. Hệ thống hiển thị modal trung gian và kiểm tra khóa ngoại ở backend để tránh việc xóa nhầm làm mất dữ liệu phạt nguội quan trọng liên kết với xe đó.

---

### D. Chức năng TÌM KIẾM/BỘ LỌC phương tiện
#### 1. Vị trí trong mã nguồn (Where in Code)
- **Form tìm kiếm (HTML):** [vehicles.php (Dòng 28-43)](file:///d:/bt3/FE/admin/pages/vehicles.php#L28-L43) sử dụng `#search-form` chứa input tìm kiếm `#search-input` và nút submit.
- **Hành vi xử lý (JS):** [vehicles.php (Dòng 265-270)](file:///d:/bt3/FE/admin/pages/vehicles.php#L265-L270) lắng nghe sự kiện `submit` của `#search-form`.
- **API Backend xử lý:** [BE/vehicles/list.php (Dòng 11-45)](file:///d:/bt3/BE/vehicles/list.php#L11-L45) tiếp nhận và lọc theo tham số query string `search`.

#### 2. Cách thức hoạt động trong Code (How it Runs)
Hệ thống thực hiện chức năng này qua các bước liên tiếp dưới đây:
1. **Lắng nghe sự kiện submit tìm kiếm:** Khi Admin nhập từ khóa và bấm **"Tìm kiếm"** hoặc nhấn `Enter`, sự kiện `submit` của biểu mẫu `#search-form` được kích hoạt tại [vehicles.php (Dòng 265)](file:///d:/bt3/FE/admin/pages/vehicles.php#L265).
2. **Chặn reload trang và gán biến tìm kiếm:** JS chặn reload trang bằng cách gọi `e.preventDefault()` tại [vehicles.php (Dòng 266)](file:///d:/bt3/FE/admin/pages/vehicles.php#L266) và gán giá trị tìm kiếm vào biến toàn cục `currentSearch` tại [vehicles.php (Dòng 268)](file:///d:/bt3/FE/admin/pages/vehicles.php#L268).
3. **Kích hoạt hàm tải lại:** JS gọi hàm **`loadVehicles(1, currentSearch)`** tại [vehicles.php (Dòng 269)](file:///d:/bt3/FE/admin/pages/vehicles.php#L269) để thực hiện cuộc gọi AJAX.
4. **Gửi yêu cầu tìm kiếm lên máy chủ:** Hàm `loadVehicles` gọi hàm `fetch()` của JavaScript gửi yêu cầu `GET` đến API: `../../BE/vehicles/list.php?page=1&search=...` tại [vehicles.php (Dòng 389)](file:///d:/bt3/FE/admin/pages/vehicles.php#L389).
5. **Xác thực quyền Admin và nhận từ khóa tại Backend:** File `list.php` xác thực quyền admin qua `check_admin_login()` tại [list.php (Dòng 9)](file:///d:/bt3/BE/vehicles/list.php#L9) và đón nhận tham số `search` từ phương thức `GET` tại [list.php (Dòng 11)](file:///d:/bt3/BE/vehicles/list.php#L11).
6. **Xây dựng câu truy vấn SQL tìm kiếm:** Backend xây dựng câu truy vấn SQL động sử dụng toán tử không phân biệt hoa thường `ILIKE` tại [list.php (Dòng 19-22)](file:///d:/bt3/BE/vehicles/list.php#L19-L22):
   ```sql
   WHERE license_plate ILIKE :search OR owner_name ILIKE :search OR vehicle_type ILIKE :search OR brand ILIKE :search OR model ILIKE :search
   ```
7. **Thực thi phân trang và truy vấn dữ liệu:** Backend thực thi truy vấn đếm tổng số dòng phù hợp để tính toán phân trang qua `$stmt->execute()` tại [list.php (Dòng 25-28)](file:///d:/bt3/BE/vehicles/list.php#L25-L28), sau đó truy vấn danh sách phương tiện theo giới hạn phân trang qua `$stmt->execute()` tại [list.php (Dòng 36-44)](file:///d:/bt3/BE/vehicles/list.php#L36-L44).
8. **Phản hồi dữ liệu JSON:** Backend phản hồi lại cho Frontend dưới dạng JSON qua hàm `echo json_encode(...)` tại [list.php (Dòng 46-55)](file:///d:/bt3/BE/vehicles/list.php#L46-L55).
9. **Vẽ lại bảng dữ liệu trên giao diện:** JS bắt kết quả trả về trong chuỗi các hàm `.then()` tại [vehicles.php (Dòng 390-459)](file:///d:/bt3/FE/admin/pages/vehicles.php#L390-L459), gọi hàm render danh sách phương tiện để vẽ lại các thẻ `<tr>` của bảng dữ liệu tại [vehicles.php (Dòng 397-458)](file:///d:/bt3/FE/admin/pages/vehicles.php#L397-L458) và gọi hàm render phân trang để vẽ thanh điều hướng phân trang.

#### 3. Cách bấm/thao tác trên giao diện (How to Click/Operate)
- Nhập từ khóa tìm kiếm (Biển số xe, tên chủ phương tiện, hãng xe, loại xe,...) vào thanh tìm kiếm ở đầu trang quản trị.
- Bấm nút **"Tìm kiếm"** hoặc nhấn phím **Enter**. Để hiển thị lại toàn bộ danh sách, xóa trống ô tìm kiếm và bấm **"Tìm kiếm"**.

#### 4. Tại sao lại hiện lên (Why it Appears)
- Thanh tìm kiếm luôn hiển thị cố định ở đầu trang quản lý phương tiện để giúp quản trị viên lọc nhanh các phương tiện cần xử lý mà không cần cuộn hoặc phân trang thủ công.

---
---

## 2. QUẢN LÝ VI PHẠM (VIOLATION MANAGEMENT)

### A. Chức năng THÊM (Ghi nhận) vi phạm mới
#### 1. Vị trí trong mã nguồn (Where in Code)
- **Nút kích hoạt (HTML):** [violations.php (Dòng 23-25)](file:///d:/bt3/FE/admin/pages/violations.php#L23-L25). Là nút đỏ nằm góc trên bên phải giao diện quản lý lỗi:
  ```html
  <button type="button" class="btn btn-danger d-flex align-items-center gap-2 px-3 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreateViolation">
      <i class="bi bi-plus-circle-fill"></i> Ghi nhận vi phạm mới
  </button>
  ```
- **Giao diện Modal Form (HTML):** [violations.php (Dòng 138-197)](file:///d:/bt3/FE/admin/pages/violations.php#L138-L197) với `id="modalCreateViolation"`.
- **JS điền dữ liệu danh sách xe vào Dropdown:** [violations.php (Dòng 517-527)](file:///d:/bt3/FE/admin/pages/violations.php#L517-L527) (`function populateVehiclesDropdown(vehicles)`).
- **Xử lý JavaScript gửi đi:** [violations.php (Dòng 271-307)](file:///d:/bt3/FE/admin/pages/violations.php#L271-L307) lắng nghe sự kiện `submit` của `#createViolationForm`.
- **API Backend xử lý:** [BE/violations/create.php](file:///d:/bt3/BE/violations/create.php).

#### 2. Cách thức hoạt động trong Code (How it Runs)
Hệ thống thực hiện chức năng này qua các bước liên tiếp dưới đây:
1. **Nạp danh sách xe vào Form khi khởi động:** Khi trang web nạp, hàm `loadViolations(...)` được tự động gọi, tiếp đó API lấy danh sách toàn bộ xe tại [violations.php (Dòng 411-413)](file:///d:/bt3/FE/admin/pages/violations.php#L411-L413) và điền sẵn vào thẻ `<select>` của form thêm mới thông qua hàm **`populateVehiclesDropdown(vehicles)`** tại [violations.php (Dòng 517-527)](file:///d:/bt3/FE/admin/pages/violations.php#L517-L527).
2. **Lắng nghe sự kiện gửi biên bản:** Khi bấm nút **"Ghi nhận"** trên form, sự kiện `submit` của biểu mẫu `#createViolationForm` kích hoạt hàm xử lý sự kiện tại [violations.php (Dòng 271)](file:///d:/bt3/FE/admin/pages/violations.php#L271).
3. **Chặn reload trang và khóa nút submit:** JS chặn reload trang mặc định qua `e.preventDefault()` tại [violations.php (Dòng 271-272)](file:///d:/bt3/FE/admin/pages/violations.php#L271-L272), nút submit chuyển sang trạng thái disable tại [violations.php (Dòng 279-280)](file:///d:/bt3/FE/admin/pages/violations.php#L279-L280) để ngăn click nhiều lần.
4. **Gửi dữ liệu ghi nhận vi phạm lên máy chủ:** JS đóng gói thông tin form thành JSON và thực hiện gọi hàm `fetch()` gửi yêu cầu `POST` tới API Backend: `../../BE/violations/create.php` tại [violations.php (Dòng 282-289)](file:///d:/bt3/FE/admin/pages/violations.php#L282-L289).
5. **Xác thực quyền Admin tại Backend:** File `create.php` tiếp nhận request, gọi hàm `check_admin_login()` tại [create.php (Dòng 9)](file:///d:/bt3/BE/violations/create.php#L9) để xác thực quyền quản trị viên.
6. **Kiểm tra tính đầy đủ dữ liệu:** Backend kiểm tra các trường bắt buộc tại [create.php (Dòng 23-27)](file:///d:/bt3/BE/violations/create.php#L23-L27).
7. **Lấy thông tin phương tiện liên quan:** Backend truy vấn thông tin phương tiện từ bảng `vehicles` qua `$conn->prepare` / `$stmt->execute` để lấy số điện thoại, email hoặc tên chủ xe tại [create.php (Dòng 31-39)](file:///d:/bt3/BE/violations/create.php#L31-L39).
8. **Lưu biên bản vi phạm vào Cơ sở dữ liệu:** Backend thực hiện câu lệnh SQL `INSERT INTO violations (...)` vào DB PostgreSQL qua `$conn->prepare` / `$stmt->execute` tại [create.php (Dòng 42-53)](file:///d:/bt3/BE/violations/create.php#L42-L53).
9. **Ghi nhật ký audit log:** Backend ghi audit log qua hàm `write_audit_log($conn, ...)` tại [create.php (Dòng 58)](file:///d:/bt3/BE/violations/create.php#L58).
10. **Quy trình gửi thông báo tự động:** Backend xác định kênh gửi (SMS, Email, hoặc Portal) dựa trên thông tin liên hệ của chủ xe tại [create.php (Dòng 61-77)](file:///d:/bt3/BE/violations/create.php#L61-L77), thực hiện SQL lưu trữ vào bảng `notifications` qua `$stmt->execute()` tại [create.php (Dòng 78-82)](file:///d:/bt3/BE/violations/create.php#L78-L82):
    ```sql
    INSERT INTO notifications (violation_id, channel, recipient, message, sent_at) VALUES (?, ?, ?, ?, NOW())
    ```
11. **Phản hồi từ Backend:** Backend trả về kết quả JSON báo thành công qua `echo json_encode(...)` tại [create.php (Dòng 84-88)](file:///d:/bt3/BE/violations/create.php#L84-L88).
12. **Xử lý kết quả ở Frontend và làm mới giao diện:** JS bắt kết quả và chuyển tiếp qua chuỗi hàm xử lý phản hồi `.then()` tại [violations.php (Dòng 290-306)](file:///d:/bt3/FE/admin/pages/violations.php#L290-L306):
    - Gọi hàm `showToast(...)` hiển thị Toast xanh báo thành công.
    - Ẩn Modal bằng cách gọi API Modal Bootstrap `bootstrap.Modal.getInstance(...).hide()`.
    - Reset form bằng `reset()`.
    - Gọi hàm **`loadViolations(currentPage)`** để tải lại danh sách vi phạm mới nhất lên bảng quản lý.

#### 3. Cách bấm/thao tác trên giao diện (How to Click/Operate)
- Nhấp vào nút màu đỏ **"Ghi nhận vi phạm mới"** ở góc trên bên phải.
- Trong form modal:
  - Chọn phương tiện vi phạm (tìm biển số - chủ xe từ dropdown).
  - Chọn ngày giờ xảy ra vi phạm.
  - Điền Tỉnh/Thành phố và địa chỉ cụ thể nơi xảy ra lỗi.
  - Nhập mô tả lỗi vi phạm (ví dụ: *"Chạy quá tốc độ quy định từ 10-20km/h"*).
  - Điền Số quyết định xử phạt, Hạn nộp phạt, và chọn Trạng thái xử lý ban đầu (thường là *"Chưa nộp phạt"*).
- Bấm nút đỏ **"Ghi nhận"** ở cuối form để lưu, hoặc **"Hủy bỏ"** để hủy.

#### 4. Tại sao lại hiện lên (Why it Appears)
- Nút này luôn xuất hiện cố định khi Admin mở trang Quản lý vi phạm, cho phép cán bộ chức năng ghi nhận và nạp các quyết định xử phạt phạt nguội mới của phương tiện lên hệ thống để người dân tra cứu.

---

### B. Chức năng SỬA biên bản vi phạm
#### 1. Vị trí trong mã nguồn (Where in Code)
- **Nút kích hoạt trong bảng (HTML):** [violations.php (Dòng 473-475)](file:///d:/bt3/FE/admin/pages/violations.php#L473-L475):
  ```html
  <button type="button" class="btn btn-outline-warning btn-sm px-2 rounded-3 text-dark border-secondary border-opacity-25" title="Chỉnh sửa" onclick="editViolation(${v.id})">
      <i class="bi bi-pencil-square"></i>
  </button>
  ```
- **Giao diện Modal Chỉnh sửa (HTML):** [violations.php (Dòng 200-221)](file:///d:/bt3/FE/admin/pages/violations.php#L200-L221) có `id="modalEditViolation"`.
- **Hàm JavaScript lấy dữ liệu cũ & định dạng thời gian:** [violations.php (Dòng 654-758)](file:///d:/bt3/FE/admin/pages/violations.php#L654-L758) (`function editViolation(id)`).
- **Xử lý JavaScript gửi form cập nhật:** [violations.php (Dòng 309-346)](file:///d:/bt3/FE/admin/pages/violations.php#L309-L346) lắng nghe sự kiện `submit` của `#editViolationForm`.
- **API Backend xử lý cập nhật:** [BE/violations/edit.php](file:///d:/bt3/BE/violations/edit.php).

#### 2. Cách thức hoạt động trong Code (How it Runs)
Hệ thống thực hiện chức năng này qua các bước liên tiếp dưới đây:
1. **Lắng nghe sự kiện click sửa:** Khi bấm Sửa vi phạm, sự kiện click gọi hàm **`editViolation(id)`** được kích hoạt tại [violations.php (Dòng 654)](file:///d:/bt3/FE/admin/pages/violations.php#L654).
2. **Hiển thị Modal và spinner loading:** JS hiển thị modal `#modalEditViolation` bằng cách khởi tạo `new bootstrap.Modal(...)` và gọi `.show()`, nạp Spinner loading tại [violations.php (Dòng 658-664)](file:///d:/bt3/FE/admin/pages/violations.php#L658-L664).
3. **Yêu cầu dữ liệu cũ từ máy chủ:** JS gọi hàm `fetch()` gửi yêu cầu `GET` đến API `../../BE/violations/view.php?id=${id}` tại [violations.php (Dòng 666)](file:///d:/bt3/FE/admin/pages/violations.php#L666).
4. **Truy vấn cơ sở dữ liệu tại Backend:** File `view.php` kết nối DB PostgreSQL và gọi `$conn->prepare` / `$stmt->execute` để truy vấn thông tin vi phạm kèm phương tiện liên quan tại [view.php (Dòng 21-29)](file:///d:/bt3/BE/violations/view.php#L21-L29), trả về JSON qua hàm `echo json_encode()`.
5. **Đổ dữ liệu và định dạng biểu mẫu:** JS nhận dữ liệu trong hàm xử lý phản hồi `.then()` tại [violations.php (Dòng 675-757)](file:///d:/bt3/FE/admin/pages/violations.php#L675-L757), tự động định dạng ngày giờ để điền vào các ô nhập liệu tại [violations.php (Dòng 688-707)](file:///d:/bt3/FE/admin/pages/violations.php#L688-L707), tạo thẻ `<option>` chọn xe tại [violations.php (Dòng 678-685)](file:///d:/bt3/FE/admin/pages/violations.php#L678-L685), và ghi đè toàn bộ Form vào thân modal `#editViolationBody`.
6. **Lắng nghe sự kiện gửi biểu mẫu cập nhật:** Khi Admin chỉnh sửa thông tin và nhấn **"Cập nhật"**, sự kiện `submit` của biểu mẫu `#editViolationForm` kích hoạt hàm xử lý sự kiện tại [violations.php (Dòng 309)](file:///d:/bt3/FE/admin/pages/violations.php#L309).
7. **Chặn reload trang và gửi yêu cầu cập nhật:** JS chặn reload trang qua `e.preventDefault()` tại [violations.php (Dòng 310)](file:///d:/bt3/FE/admin/pages/violations.php#L310), gom dữ liệu form thành JSON, và gọi hàm `fetch()` gửi yêu cầu `POST` tới API: `../../BE/violations/edit.php` tại [violations.php (Dòng 313-328)](file:///d:/bt3/FE/admin/pages/violations.php#L313-L328).
8. **Xác thực và cập nhật dữ liệu tại Backend:** File `edit.php` xác thực admin qua `check_admin_login()` tại [edit.php (Dòng 9)](file:///d:/bt3/BE/violations/edit.php#L9), kiểm tra tính hợp lệ dữ liệu và thực hiện cập nhật SQL `UPDATE violations SET ... WHERE id = ?` qua `$conn->prepare` / `$stmt->execute` tại [edit.php (Dòng 32-40)](file:///d:/bt3/BE/violations/edit.php#L32-L40).
9. **Ghi nhật ký cập nhật và phản hồi:** Backend ghi log hoạt động UPDATE qua hàm `write_audit_log(...)` tại [edit.php (Dòng 43)](file:///d:/bt3/BE/violations/edit.php#L43), trả về JSON báo thành công qua `echo json_encode()` tại [edit.php (Dòng 45-48)](file:///d:/bt3/BE/violations/edit.php#L45-L48).
10. **Cập nhật lại giao diện:** JS bắt kết quả trong chuỗi xử lý `.then()` tại [violations.php (Dòng 329-345)](file:///d:/bt3/FE/admin/pages/violations.php#L329-L345), gọi hàm `showToast(...)` hiển thị thông báo thành công màu xanh lá, đóng Modal qua `bootstrap.Modal.getInstance(...).hide()`, và gọi hàm **`loadViolations(currentPage)`** để cập nhật lại danh sách bảng vi phạm.

#### 3. Cách bấm/thao tác trên giao diện (How to Click/Operate)
- Nhấp vào nút màu vàng có icon cây bút chì ở cột Thao tác của lỗi vi phạm cần sửa trên bảng.
- Form modal hiện ra điền đầy đủ thông tin lỗi vi phạm cũ.
- Thay đổi thông tin lỗi vi phạm, đặc biệt là cập nhật trạng thái xử lý thành **"Đã nộp phạt"** sau khi người vi phạm đóng tiền phạt.
- Bấm nút vàng **"Cập nhật"** ở chân modal để hoàn tất, hoặc bấm **"Hủy bỏ"** để đóng modal.

#### 4. Tại sao lại hiện lên (Why it Appears)
- Nút Sửa được render động bên cạnh mỗi bản ghi vi phạm. Đây là công cụ đắc lực để Admin cập nhật trạng thái nộp phạt của người dân hoặc chỉnh sửa các sai sót thông tin địa điểm, số quyết định phạt nguội.

---

### C. Chức năng XÓA biên bản vi phạm
#### 1. Vị trí trong mã nguồn (Where in Code)
- **Nút kích hoạt trong bảng (HTML):** [violations.php (Dòng 476-478)](file:///d:/bt3/FE/admin/pages/violations.php#L476-L478):
  ```html
  <button type="button" class="btn btn-outline-danger btn-sm px-2 rounded-3 border-secondary border-opacity-25" title="Xóa" onclick="confirmDeleteViolation(${v.id})">
      <i class="bi bi-trash-fill"></i>
  </button>
  ```
- **Modal xác nhận xóa (HTML):** [violations.php (Dòng 224-244)](file:///d:/bt3/FE/admin/pages/violations.php#L224-L244) có `id="modalDeleteViolation"`.
- **Hàm JavaScript hiển thị Modal & gán sự kiện xóa:** [violations.php (Dòng 761-791)](file:///d:/bt3/FE/admin/pages/violations.php#L761-L791) (`function confirmDeleteViolation(id)`).
- **API Backend xử lý xóa:** [BE/violations/delete.php](file:///d:/bt3/BE/violations/delete.php).

#### 2. Cách thức hoạt động trong Code (How it Runs)
Hệ thống thực hiện chức năng này qua các bước liên tiếp dưới đây:
1. **Lắng nghe sự kiện click xóa:** Khi bấm nút Xóa ở cột Thao tác, sự kiện click gọi hàm **`confirmDeleteViolation(id)`** được kích hoạt tại [violations.php (Dòng 761)](file:///d:/bt3/FE/admin/pages/violations.php#L761).
2. **Gán sự kiện click xóa động:** Hàm `confirmDeleteViolation` gán sự kiện click cho nút **"Xác nhận xóa"** (`#delete_violation_btn`) tại [violations.php (Dòng 764-787)](file:///d:/bt3/FE/admin/pages/violations.php#L764-L787) trỏ tới hàm ẩn xử lý gửi yêu cầu xóa.
3. **Hiển thị Modal cảnh báo xóa:** JS mở Modal cảnh báo màu đỏ `#modalDeleteViolation` bằng cách gọi `.show()` tại [violations.php (Dòng 789-790)](file:///d:/bt3/FE/admin/pages/violations.php#L789-L790).
4. **Chặn spam và gửi yêu cầu xóa:** Khi người dùng click nút **"Xác nhận xóa"** trong Modal, nút chuyển sang trạng thái loading và disabled tại [violations.php (Dòng 765-766)](file:///d:/bt3/FE/admin/pages/violations.php#L765-L766), sau đó JS gửi request `GET` tới API Backend: `../../BE/violations/delete.php?id=${id}` tại [violations.php (Dòng 768)](file:///d:/bt3/FE/admin/pages/violations.php#L768).
5. **Xác thực quyền Admin và nhận tham số tại Backend:** File `delete.php` xác thực quyền admin thông qua `check_admin_login()` tại [delete.php (Dòng 9)](file:///d:/bt3/BE/violations/delete.php#L9), nhận tham số `id` và thực hiện kiểm tra tính hợp lệ tại [delete.php (Dòng 12-19)](file:///d:/bt3/BE/violations/delete.php#L12-L19).
6. **Xóa ràng buộc khóa ngoại (Thông báo liên quan):** Do bảng `notifications` liên kết khóa ngoại với bảng `violations` (`violation_id`), để tránh lỗi vi phạm khóa ngoại (foreign key constraint) khiến PostgreSQL từ chối xóa, Backend thực hiện xóa các thông báo liên quan trước tại [delete.php (Dòng 23-24)](file:///d:/bt3/BE/violations/delete.php#L23-L24):
   ```sql
   DELETE FROM notifications WHERE violation_id = ?
   ```
7. **Thực thi lệnh xóa biên bản vi phạm:** Sau khi dọn sạch bảng con `notifications`, Backend tiến hành câu lệnh SQL xóa vi phạm tại [delete.php (Dòng 27-28)](file:///d:/bt3/BE/violations/delete.php#L27-L28):
   ```sql
   DELETE FROM violations WHERE id = ?
   ```
8. **Ghi nhật ký xóa và phản hồi:** Backend ghi nhật ký audit log (`DELETE` trên đối tượng `Violation`) tại [delete.php (Dòng 31)](file:///d:/bt3/BE/violations/delete.php#L31), trả về JSON báo thành công tại [delete.php (Dòng 33-36)](file:///d:/bt3/BE/violations/delete.php#L33-L36).
9. **Đóng Modal và cập nhật giao diện:** JS bắt kết quả trả về, đóng Modal tại [violations.php (Dòng 773)](file:///d:/bt3/FE/admin/pages/violations.php#L773). Nếu thành công, hiển thị Toast thông báo thành công màu xanh lá và tải lại danh sách vi phạm qua hàm **`loadViolations(currentPage)`** tại [violations.php (Dòng 775-780)](file:///d:/bt3/FE/admin/pages/violations.php#L775-L780).

#### 3. Cách bấm/thao tác trên giao diện (How to Click/Operate)
- Nhấp vào nút màu đỏ có icon hình thùng rác ở cột thao tác của lỗi vi phạm cần xóa.
- Khi Modal cảnh báo xác nhận xóa hiện ra, xem kỹ thông tin cảnh báo.
- Bấm nút đỏ **"Xác nhận xóa"** để xóa hẳn, hoặc bấm **"Hủy bỏ"** để hủy thao tác.

#### 4. Tại sao lại hiện lên (Why it Appears)
- Sinh ra động cho mỗi biên bản vi phạm để Admin có thể gỡ bỏ các lỗi vi phạm bị nhập trùng lặp hoặc bản án bị hủy bỏ sau khi khiếu nại thành công. Hệ thống tự động xóa thông báo liên kết trong DB giúp tránh lỗi nghẽn ràng buộc hệ thống.

---

### D. Chức năng TÌM KIẾM/BỘ LỌC vi phạm
#### 1. Vị trí trong mã nguồn (Where in Code)
- **Form bộ lọc (HTML):** [violations.php (Dòng 28-71)](file:///d:/bt3/FE/admin/pages/violations.php#L28-L71) sử dụng `#filter-form` gồm các trường nhập biển kiểm soát, chọn Tỉnh/Thành phố, Trạng thái nộp phạt, và chọn Ngày vi phạm.
- **Hành vi xử lý (JS):** [violations.php (Dòng 260-268)](file:///d:/bt3/FE/admin/pages/violations.php#L260-L268) lắng nghe sự kiện `submit` của `#filter-form` để lấy dữ liệu lọc và gọi `loadViolations(1)`.
- **API Backend xử lý:** [BE/violations/list.php (Dòng 11-94)](file:///d:/bt3/BE/violations/list.php#L11-L94) tiếp nhận các tham số lọc để xây dựng câu lệnh truy vấn PostgreSQL.

#### 2. Cách thức hoạt động trong Code (How it Runs)
Hệ thống thực hiện chức năng này qua các bước liên tiếp dưới đây:
1. **Lắng nghe sự kiện lọc:** Khi Admin chọn hoặc điền thông tin lọc rồi bấm nút lọc (hình phễu) hoặc nhấn `Enter`, sự kiện `submit` của `#filter-form` được kích hoạt tại [violations.php (Dòng 260)](file:///d:/bt3/FE/admin/pages/violations.php#L260).
2. **Chặn reload trang và thu thập tham số:** JS chặn reload trang qua `e.preventDefault()` tại [violations.php (Dòng 260)](file:///d:/bt3/FE/admin/pages/violations.php#L260), thu thập các giá trị từ form tại [violations.php (Dòng 261-266)](file:///d:/bt3/FE/admin/pages/violations.php#L261-L266): Biển kiểm soát (`filter-search-plate`), Tỉnh/Thành phố (`filter-province`), Trạng thái (`filter-status`), và Ngày vi phạm (`filter-date`).
3. **Kích hoạt hàm tải lại:** JS gọi hàm **`loadViolations(1)`** tại [violations.php (Dòng 267)](file:///d:/bt3/FE/admin/pages/violations.php#L267) để thực hiện cuộc gọi AJAX.
4. **Gửi yêu cầu lọc lên máy chủ:** Hàm `loadViolations` thực hiện AJAX GET đến API: `../../BE/violations/list.php?page=1&search_plate=...&province=...` tại [violations.php (Dòng 393-399)](file:///d:/bt3/FE/admin/pages/violations.php#L393-L399).
5. **Nhận tham số tại Backend:** File `list.php` nhận các bộ lọc từ `$_GET` tại [list.php (Dòng 12-15)](file:///d:/bt3/BE/violations/list.php#L12-L15).
6. **Xây dựng câu SQL lọc động:** Backend xây dựng mảng `$where_clauses` động dựa trên các bộ lọc có giá trị tại [list.php (Dòng 35-53)](file:///d:/bt3/BE/violations/list.php#L35-L53):
   - Tỉnh thành: Khớp chính xác `v.province = :province`.
   - Trạng thái: Lọc theo nhóm trạng thái paid (`'paid', 'Đã nộp phạt', 'Đã xử lý', 'Đã nộp'`) hoặc ngược lại.
   - Ngày vi phạm: Trích xuất và so sánh ngày `v.violated_at::date = :violated_at`.
   - Biển số: Tìm kiếm không phân biệt hoa thường `ve.license_plate ILIKE :search_plate`.
7. **Thực thi phân trang và truy vấn dữ liệu:** Backend thực thi truy vấn đếm tổng số dòng phù hợp để phân trang tại [list.php (Dòng 61-71)](file:///d:/bt3/BE/violations/list.php#L61-L71) và truy vấn danh sách vi phạm phân trang tại [list.php (Dòng 80-94)](file:///d:/bt3/BE/violations/list.php#L80-L94) từ DB PostgreSQL.
8. **Phản hồi dữ liệu JSON:** Backend trả về danh sách vi phạm kèm danh mục provinces phục vụ dropdown lọc tại [list.php (Dòng 97-109)](file:///d:/bt3/BE/violations/list.php#L97-L109).
9. **Cập nhật giao diện quản lý:** JS bắt kết quả, vẽ lại các hàng vi phạm khớp tiêu chuẩn lên bảng quản lý tại [violations.php (Dòng 407-488)](file:///d:/bt3/FE/admin/pages/violations.php#L407-L488) và điều chỉnh phân trang.

#### 3. Cách bấm/thao tác trên giao diện (How to Click/Operate)
- Điền biển số xe hoặc chọn Tỉnh/Thành, Trạng thái xử lý hoặc Ngày vi phạm tại thanh bộ lọc trên cùng.
- Nhấp vào nút màu đen có hình phễu ở cuối thanh bộ lọc để thực hiện lọc. Để xóa bộ lọc và quay lại ban đầu, bấm reset các ô lọc về mặc định và nhấp lại nút lọc.

#### 4. Tại sao lại hiện lên (Why it Appears)
- Bộ lọc và tìm kiếm vi phạm luôn hiển thị ở trên cùng bảng vi phạm để quản trị viên có thể theo dõi và thống kê các lỗi phạt nguội theo địa phương, ngày tháng hoặc rà soát tiến độ đóng phạt một cách thuận tiện nhất.

---
---

## 3. CỔNG TRA CỨU CÔNG CỘNG (CUSTOMER VEHICLE LOOKUP)

### A. Chức năng TRA CỨU vi phạm giao thông (Lookup)
#### 1. Vị trí trong mã nguồn (Where in Code)
- **Form nhập tìm kiếm (HTML):** [hero.php (Dòng 21-38)](file:///d:/bt3/FE/customer/components/hero.php#L21-L38). Là một Form HTML gửi tham số qua phương thức GET:
  ```html
  <form action="index.php" method="GET" class="w-100">
      <input type="text" name="license_plate" ...>
      <button type="submit">Tra cứu</button>
  </form>
  ```
- **Bộ điều hướng / Định tuyến:** [index.php (Dòng 16-18)](file:///d:/bt3/FE/customer/index.php#L16-L18). Nếu phát hiện tham số `license_plate` trong URL, trang sẽ tự động nạp nạp trang kết quả: `pages/result.php`.
- **Trang hiển thị kết quả (HTML/JS):** [result.php](file:///d:/bt3/FE/customer/pages/result.php).
- **API Backend xử lý tìm kiếm:** [BE/customer/lookup.php](file:///d:/bt3/BE/customer/lookup.php).

#### 2. Cách thức hoạt động trong Code (How it Runs)
Hệ thống thực hiện chức năng này qua các bước liên tiếp dưới đây:
1. **Gửi biển số cần tra cứu:** Khi người dân nhập biển số xe và bấm nút **"Tra cứu"** hoặc nhấn `Enter`, trình duyệt chuyển hướng trang sang địa chỉ: `index.php?license_plate=BIEN_SO_XE` tại [hero.php (Dòng 21-38)](file:///d:/bt3/FE/customer/components/hero.php#L21-L38).
2. **Điều hướng nạp trang kết quả:** File `index.php` ở Front-End đón nhận tham số và nạp giao diện `pages/result.php` tại [index.php (Dòng 16-18)](file:///d:/bt3/FE/customer/index.php#L16-L18).
3. **Yêu cầu thông tin từ máy chủ:** JS trong `result.php` trích xuất biển số xe từ PHP tại [result.php (Dòng 257)](file:///d:/bt3/FE/customer/pages/result.php#L257) và thực hiện gọi AJAX `fetch` gửi đến Backend tại [result.php (Dòng 265)](file:///d:/bt3/FE/customer/pages/result.php#L265):
   ```javascript
   fetch(`../../BE/customer/lookup.php?license_plate=${encodeURIComponent(licensePlate)}`)
   ```
4. **Nhận biển số và chuẩn hóa tại Backend:** File `lookup.php` lấy biển số từ tham số `license_plate` tại [lookup.php (Dòng 7)](file:///d:/bt3/BE/customer/lookup.php#L7), tiến hành xóa toàn bộ dấu cách và dấu gạch ngang, chuyển sang chữ viết hoa tại [lookup.php (Dòng 17)](file:///d:/bt3/BE/customer/lookup.php#L17) để chống lỗi nhập sai định dạng:
   ```php
   $normalized_input = str_replace([' ', '-'], '', strtoupper($license_plate));
   ```
5. **Truy vấn thông tin phương tiện:** Backend tìm kiếm thông tin xe bằng câu lệnh SQL tại [lookup.php (Dòng 20-27)](file:///d:/bt3/BE/customer/lookup.php#L20-L27):
   ```sql
   SELECT * FROM vehicles WHERE REPLACE(REPLACE(UPPER(license_plate), '-', ''), ' ', '') = ? LIMIT 1
   ```
6. **Xử lý nếu không tồn tại xe:** Nếu không tìm thấy xe, API trả về lỗi 404/JSON báo không tìm thấy tại [lookup.php (Dòng 29-36)](file:///d:/bt3/BE/customer/lookup.php#L29-L36).
7. **Truy vấn danh sách vi phạm:** Nếu tìm thấy xe, API tiếp tục lấy danh sách tất cả các vi phạm bằng cách khớp `vehicle_id` tại [lookup.php (Dòng 39-46)](file:///d:/bt3/BE/customer/lookup.php#L39-L46):
   ```sql
   SELECT * FROM violations WHERE vehicle_id = ? ORDER BY violated_at DESC
   ```
8. **Phản hồi dữ liệu JSON:** Backend trả về JSON chứa toàn bộ dữ liệu xe và mảng danh sách lỗi vi phạm liên quan tại [lookup.php (Dòng 48-52)](file:///d:/bt3/BE/customer/lookup.php#L48-L52).
9. **Cập nhật giao diện tra cứu:** JS nhận dữ liệu tại [result.php (Dòng 272-282)](file:///d:/bt3/FE/customer/pages/result.php#L272-L282):
   - **Trường hợp tìm thấy:** JS render thông số xe ở cột bên trái tại [result.php (Dòng 301-336)](file:///d:/bt3/FE/customer/pages/result.php#L301-L336) và render danh sách lỗi vi phạm ở cột bên phải tại [result.php (Dòng 337-470)](file:///d:/bt3/FE/customer/pages/result.php#L337-L470).
   - **Trường hợp không tìm thấy:** Hiển thị thông báo Alert lỗi màu đỏ thông qua hàm `showError` tại [result.php (Dòng 288-299)](file:///d:/bt3/FE/customer/pages/result.php#L288-L299).

#### 3. Cách bấm/thao tác trên giao diện (How to Click/Operate)
- Truy cập vào trang chủ Cổng tra cứu vi phạm giao thông dành cho Khách hàng.
- Tại ô nhập liệu chính ở giữa trang, điền biển kiểm soát cần tra cứu (ví dụ: `30A-12345`).
- Nhấn nút **"Tra cứu"** hoặc bấm phím `Enter`.
- Xem kết quả hiển thị trên màn hình. Nếu có lỗi vi phạm, các quyết định và trạng thái đóng phạt sẽ được liệt kê trực quan.

#### 4. Tại sao lại hiện lên (Why it Appears)
- Đây là chức năng cốt lõi nhất của ứng dụng dành cho công dân. Nó không yêu cầu bất kỳ tài khoản đăng nhập nào, giúp tăng tính minh bạch thông tin phạt nguội của CSGT và giúp chủ xe dễ dàng kiểm tra, chấp hành đóng phạt đúng hạn. Giao diện kết quả chỉ xuất hiện khi có tham số biển số xe được gửi lên từ thanh tìm kiếm.
