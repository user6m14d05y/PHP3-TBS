# Project Modules Overview (PHP3-TBS)

Dựa trên phân tích từ GitNexus, dự án được chia thành 7 module (functional areas) chính với mức độ gắn kết (cohesion) khá cao. Dưới đây là mô tả tổng quan về các module trong dự án:

## 1. Controllers
- **Số lượng symbols:** 75
- **Mức độ gắn kết:** 96%
- **Mô tả:** Chứa phần lớn các file xử lý logic của backend (Laravel).
- **Các thành phần chính:**
  - `AuthController.php`: Xử lý xác thực người dùng.
  - `CartController.php`: Quản lý giỏ hàng.
  - `CategoryController.php` & `CategoryItemController.php`: Xử lý danh mục sản phẩm.
  - `BlacklistController.php`: Quản lý danh sách đen.

## 2. Admin
- **Số lượng symbols:** 42
- **Mức độ gắn kết:** 87%
- **Mô tả:** Chứa logic và giao diện cho phần quản trị (Admin Dashboard) ở frontend (Vue.js).
- **Các thành phần chính:**
  - `frontend/src/pages/Admin/Products/index.vue`: Trang quản lý danh sách sản phẩm.
  - `frontend/src/pages/Admin/category.vue`: Quản lý danh mục.
  - `frontend/src/pages/Admin/contact.vue`: Quản lý liên hệ.
  - `frontend/src/pages/Admin/coupon.vue`: Quản lý mã giảm giá.

## 3. Products
- **Số lượng symbols:** 32
- **Mức độ gắn kết:** 92%
- **Mô tả:** Chứa các logic xử lý phức tạp về sản phẩm, đặc biệt là phần form thêm/sửa sản phẩm.
- **Các thành phần chính:**
  - `frontend/src/pages/Admin/Products/ProductForm.vue`: Xử lý tính toán giá giảm, biến thể (variants), tải ảnh, validate form.

## 4. Scripts
- **Số lượng symbols:** 18
- **Mức độ gắn kết:** 80%
- **Mô tả:** (Có thể bao gồm các script cấu hình, helper, hoặc kịch bản hỗ trợ quá trình build/deploy).

## 5. Home
- **Số lượng symbols:** 17
- **Mức độ gắn kết:** 82%
- **Mô tả:** Chứa logic và giao diện hiển thị ở trang chủ cho người dùng cuối (Frontend / Client).

## 6. Services
- **Số lượng symbols:** 14
- **Mức độ gắn kết:** 88%
- **Mô tả:** Chứa các Service class ở backend nhằm tái sử dụng các logic nghiệp vụ phức tạp tách biệt khỏi Controllers.
- **Các thành phần chính:**
  - `CouponService.php`: Xử lý kiểm tra điều kiện, tính giảm giá, giữ và hoàn voucher.
  - `OrderStatusService.php`: Xử lý hủy đơn hàng, hoàn trả tồn kho và giải phóng mã giảm giá an toàn trong DB transaction.
  - `ShippingFeeService.php`: Tính phí giao hàng (Standard / Express) theo khoảng cách km.
  - `GeoDistanceService.php`: Tính khoảng cách giữa các vị trí bằng công thức Haversine.

## 7. Order & Delivery (Module mới hoàn thiện)
- **Mô tả:** Module quản lý toàn bộ quy trình đặt hàng, giao hàng, quản lý đơn hàng và địa chỉ GPS cho cả User và Admin.
- **Các thành phần Backend:**
  - `OrderController.php`: API lịch sử đơn hàng, xem chi tiết và hủy đơn của người dùng.
  - `AdminOrderController.php`: API quản trị danh sách, chi tiết và chuyển trạng thái đơn hàng (processing, shipping, completed, cancelled).
  - `CheckoutController.php`: API xử lý thanh toán, tính phí ship động, áp mã giảm giá và dọn giỏ hàng.
  - `UserAddressController.php`: API CRUD địa chỉ nhận hàng tích hợp tọa độ GPS (latitude/longitude).
- **Các thành phần Frontend:**
  - `frontend/src/pages/Client/Cart/Checkout.vue`: Trang thanh toán đơn hàng với bản đồ/tọa độ, tính phí ship tức thì và mã giảm giá.
  - `frontend/src/pages/Client/Cart/Index.vue`: Trang giỏ hàng thật kết nối Pinia store (`cart.js`).
  - `frontend/src/pages/Client/Cart/OrderSuccess.vue`: Trang hiển thị chi tiết đơn hàng sau khi đặt thành công.
  - `frontend/src/pages/Client/Auth/Order.vue`: Trang lịch sử đơn hàng cá nhân của User.
  - `frontend/src/pages/Client/Auth/Address.vue`: Trang sổ địa chỉ nhận hàng của User.
  - `frontend/src/pages/Admin/order.vue`: Trang quản lý và cập nhật trạng thái đơn hàng của Admin.
  - `frontend/src/composables/useGeocode.js`: Composable hỗ trợ lấy vị trí hiện tại/tọa độ GPS.
  - `frontend/src/stores/cart.js`: Pinia store đồng bộ badge giỏ hàng và sản phẩm theo thời gian thực.

---
*Tài liệu này được tự động trích xuất từ dữ liệu Knowledge Graph của GitNexus (Cập nhật: 09/2026).*
