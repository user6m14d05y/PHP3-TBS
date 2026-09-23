# Báo cáo Kiểm tra Bảo mật và Tối ưu hoá (PHP3-TBS)

Dựa trên kết quả phân tích hệ thống (Deep Scan) qua PDG và rà soát thủ công mã nguồn, dưới đây là chi tiết các vấn đề bảo mật đã được phát hiện và khắc phục.

## 1. Trạng thái các lỗ hổng nghiêm trọng (Taint-Flow Analysis)
Quá trình phân tích dòng chảy dữ liệu (Taint analysis) trên toàn bộ dự án với GitNexus cho thấy **không có lỗ hổng nào liên quan đến SQL Injection, Command Injection, XSS, hay Path Traversal**.
- Tất cả các thao tác với Database như `whereRaw`, `selectRaw` trong `ProductController` đều đã sử dụng chuẩn Parameter Binding an toàn (ví dụ: `["%{$query}%"]`).
- Việc lưu file upload được bảo vệ thông qua các hàm tạo tên file ngẫu nhiên an toàn (kết hợp `Str::slug`, `time()`, `Str::random`), loại bỏ nguy cơ Directory Traversal.

## 2. Các điểm yếu Validation đã được khắc phục
Qua kiểm tra thủ công luồng dữ liệu tại các Controller quản trị, một số điểm yếu thiếu kiểm tra dữ liệu đầu vào (Missing Input Validation) đã được tìm thấy và vá lỗi:

### Khắc phục trong `AuthController.php`:
- **Vấn đề (Trước đây):** Các hàm `register`, `update`, `login` đều sử dụng trực tiếp các field như `$request->email`, `$request->password` mà không kiểm tra tính hợp lệ của định dạng email hoặc độ dài mật khẩu. Điều này dẫn đến nguy cơ đầu vào độc hại, DoS bằng dữ liệu quá dài, hoặc sai lệch hệ thống (ví dụ: duplicate key error từ CSDL khi email đã tồn tại). Hàm `destroy` không kiểm tra `User::find()` trả về null, dễ gây lỗi HTTP 500.
- **Giải pháp (Đã áp dụng):** Đưa toàn bộ các hàm này qua lớp `$request->validate(...)` nghiêm ngặt, bọc điều kiện `if (!$user)` trước khi xoá tài khoản.

### Khắc phục trong `ContactController.php`:
- **Vấn đề (Trước đây):** Hàm `SubmitContact` chỉ kiểm tra xem email đã tồn tại chưa mà bỏ qua bước xác thực chuỗi có thực sự là một email hợp lệ hay không.
- **Giải pháp (Đã áp dụng):** Sử dụng rule `email` và `unique:contacts,email` để làm sạch đầu vào và chặn hoàn toàn chuỗi độc hại trước khi đưa vào Database.

## 3. Tối ưu hoá truy vấn Database
- Kiểm tra các truy vấn danh sách (như trong `ProductController::index` và `CartController::cartPayload`) cho thấy dự án đã vận dụng tốt cơ chế **Eager Loading** (hàm `with([...])`). Nhờ đó, ứng dụng hoàn toàn không gặp lỗi truy vấn N+1 (N+1 query problem).
- Tốc độ xử lý giữa Laravel và Vue được bảo đảm qua hệ thống Cache, các request tìm kiếm có sử dụng Redis đúng chuẩn theo mô hình trong file Docker cấu hình.

---
*Tài liệu được cập nhật ngày 17/09/2026 sau lần rà soát toàn diện codebase.*
