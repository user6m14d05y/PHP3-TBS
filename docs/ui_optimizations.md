# Báo cáo Tối ưu hoá Giao diện (UI/UX) và Responsive

Tài liệu này ghi nhận các nâng cấp về giao diện, trải nghiệm người dùng (UX), và khả năng hiển thị đa thiết bị (Responsive) trên Frontend của dự án PHP3-TBS.

## 1. Tối ưu Animation và Trải nghiệm Tương tác (Transitions)
Phần lớn các thành phần tĩnh đã được bổ sung hiệu ứng chuyển động mượt mà (Smooth Transitions), tập trung vào các luồng thao tác của khách hàng.

### 1.1 Khối Card Sản Phẩm (`Index.vue` & `product.vue`)
- **Hiệu ứng nổi (Hover lift):** Khi người dùng di chuột hoặc chạm, khối thẻ sản phẩm sẽ lướt nhẹ lên trên (`hover:-translate-y-2`) trong thời gian 500ms với gia tốc `ease-out`. 
- **Đổ bóng đa tầng (Dynamic Shadows):** Bo góc mềm (`rounded-xl`), viền shadow mặc định siêu nhỏ (`shadow-sm`) và sẽ mở rộng toả bóng (`group-hover:shadow-xl`) đi kèm hiệu ứng chuyển đổi từ từ (`transition-shadow duration-500`).
- **Mục đích:** Khơi gợi sự chú ý, tạo cảm giác sang trọng (premium) phù hợp với một website bán hoa thiết kế.

## 2. Tối ưu Hiển thị Đa thiết bị (Responsive Tablet/Desktop)
Thay vì chỉ tập trung vào Điện thoại (Mobile) và Máy tính bàn (Desktop) như cấu trúc cũ, các bản cập nhật mới đã lấp đầy khoảng trống ở thiết bị Máy tính bảng (Tablet - độ phân giải từ 640px đến 1024px).

### 2.1 Lưới Danh mục (Category Grid) - Trang Chủ
- **Tình trạng cũ:** Sử dụng lưới `grid-cols-2 md:grid-cols-5`. Trên màn hình iPad dọc, 5 khối danh mục bị ép chung một hàng khiến chữ và ảnh bị bóp méo, chật chội.
- **Cấu hình mới:** `grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5`.
- **Mục đích:** Giao diện co giãn tuyến tính. Trên điện thoại hiển thị 2 khối/hàng, Tablet nhỏ hiển thị 3 khối, Tablet lớn (iPad Pro) hiển thị 4 khối, và Desktop trải đủ 5 khối, giúp ảnh thumbnail bo tròn luôn giữ được tỷ lệ đẹp nhất.

### 2.2 Tối ưu Banner Video
- Cấu hình banner Video toàn màn hình đã được tinh chỉnh để chặn quá tải máy chủ (như tài liệu ở `security_audit.md`), nhưng trên khía cạnh UI, thẻ `poster` được thêm vào đảm bảo người dùng luôn nhìn thấy ảnh nền nghệ thuật đầu tiên, loại bỏ hoàn toàn các khung màu đen rỗng trong lúc đợi trình duyệt tải video nền.

---
*Cập nhật: 17/09/2026*
