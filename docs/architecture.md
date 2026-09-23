# Phân tích Kiến trúc và Luồng thực thi (PHP3-TBS)

Dựa trên dữ liệu từ Knowledge Graph của GitNexus, dự án này là một hệ thống Website thương mại điện tử với cấu trúc **Backend (Laravel)** và **Frontend (Vue.js)**. Frontend bao gồm các trang quản trị (Admin) và trang khách hàng (Client), đồng thời được tích hợp một số script prerender SEO để hỗ trợ SEO.

Dưới đây là phân tích chi tiết về tất cả các Module và các Luồng thực thi chính (Execution Processes) của hệ thống.

---

## 1. Phân tích chi tiết các Modules (Functional Areas)

### 1.1 Module: Controllers (Backend - 75 symbols, 96% cohesion)
- **Mô tả:** Nơi chứa logic điều hướng và xử lý request chính của Laravel.
- **Thành phần:** 
  - Quản lý người dùng và phiên đăng nhập: `AuthController.php`.
  - Quản lý giỏ hàng: `CartController.php` (xử lý thêm, sửa, xoá, lấy thông tin giỏ hàng cho user).
  - Quản lý danh mục: `CategoryController.php`, `CategoryItemController.php`.
  - Quản lý an ninh: `BlacklistController.php`.

### 1.2 Module: Services (Backend - 11 symbols, 84% cohesion)
- **Mô tả:** Lớp nghiệp vụ (Business Logic) tái sử dụng ở backend, giúp Controller giảm bớt sự cồng kềnh.
- **Thành phần:** Nổi bật là `CouponService.php` kết hợp với `CouponController.php` để xử lý các logic phức tạp như: `validateConditions`, `calculateDiscount`, `reserve`, `validateForUser`.

### 1.3 Module: Admin (Frontend - 42 symbols, 87% cohesion)
- **Mô tả:** Giao diện quản trị (Vue.js SPA).
- **Thành phần:** `frontend/src/pages/Admin/*`
  - Các trang quản lý danh sách: `index.vue` (Products), `category.vue` (Danh mục), `contact.vue` (Liên hệ), `coupon.vue` (Mã giảm giá).
  - Xử lý các nghiệp vụ lấy danh sách (fetch), chuyển trang (pagination), thêm/sửa/xoá (save, delete).

### 1.4 Module: Products (Frontend - 32 symbols, 92% cohesion)
- **Mô tả:** Cụm nghiệp vụ rất phức tạp liên quan đến Form tạo/sửa sản phẩm.
- **Thành phần:** `frontend/src/pages/Admin/Products/ProductForm.vue`
  - Xử lý tính toán giá (calculateSalePrice, calculateDiscountPercent).
  - Xử lý biến thể của sản phẩm (normalizeVariantForPayload).
  - Quản lý ảnh (handleGallery, handleThumbnail).
  - Quản lý lỗi form và định dạng (formatCurrency, normalizeSlug).

### 1.5 Module: Home / Client (Frontend - 17 symbols, 82% cohesion)
- **Mô tả:** Các trang hiển thị cho người dùng cuối và các hàm tiện ích SEO ở Frontend.
- **Thành phần:** `frontend/src/pages/Client/Home/*` và `frontend/src/utils/seo.js`
  - Render sản phẩm, giá cả (getProductPrice, formatPrice, getMinPrice).
  - Cập nhật linh hoạt thẻ Meta SEO (updateProductSeo, updateProductListSeo, buildProductSchema, setMetaTag, setJsonLd).

### 1.6 Module: Scripts (Frontend Tools - 18 symbols, 80% cohesion)
- **Mô tả:** Kịch bản Node.js/JS chạy để prerender SEO cho dự án Vue SPA.
- **Thành phần:** `frontend/scripts/*.mjs`
  - `generate-seo-files.mjs` & `prerender-product-pages.mjs`: Xử lý nạp dữ liệu sản phẩm, render tĩnh các thẻ meta và Schema JSON-LD (buildProductSchema, injectStaticSeo, writeProductPage) để các công cụ tìm kiếm dễ dàng crawl dữ liệu thay vì chờ CSR (Client-Side Rendering).
  - `seo-env.mjs`: Xử lý load biến môi trường.

### 1.7 Module: Utility / API (Cluster_59 - 5 symbols, 89% cohesion)
- **Mô tả:** Các hàm xử lý đường dẫn nhỏ lẻ và thư viện gọi API tập trung.
- **Thành phần:** 
  - `frontend/src/utils/api.js`: Xử lý assetUrl, imageUrl, videoUrl, isAbsoluteUrl, trimLeadingSlash.
  - `frontend/src/utils/http.js` *(Mới được thêm vào)*: HTTP Client (Axios Interceptors) tập trung, chuyên trách gắn Access Token tự động và xử lý lỗi mạng (401 Unauthorized). Cải thiện tính bảo trì cho toàn bộ ứng dụng Frontend.

---

## 2. Phân tích các Luồng thực thi chính (Top Execution Processes)

GitNexus ghi nhận 71 luồng thực thi trong hệ thống. Dưới đây là phân nhóm các luồng quan trọng nhất diễn ra xuyên suốt ứng dụng (Cross-Community Processes):

### 2.1 Luồng nghiệp vụ Giảm giá & Giỏ hàng (Coupon & Cart)
- **Apply → ItemValue / AssertAnyMatch**: Khi người dùng áp dụng một mã giảm giá, luồng chạy qua `CouponController` chuyển xuống `CouponService` để tính toán dựa trên trị giá đơn hàng (`itemValue`) và xác nhận sản phẩm có thuộc danh sách được giảm hay không (`assertAnyMatch`).
- **Store / Reserve → ItemValue**: Quá trình tạo mới hoặc giữ chỗ (reserve) coupon cũng kiểm tra tương tự để đảm bảo tính hợp lệ của coupon trước khi lưu trữ.

### 2.2 Luồng tối ưu hoá tìm kiếm (SEO & Prerendering)
- **LoadProductData / UpdateProductListSeo → ResolveUrl / NormalizeText**: Trong lúc duyệt danh sách sản phẩm ở Frontend, tiện ích `seo.js` liên tục được gọi để `normalizeText` (chuẩn hoá chuỗi) và `resolveUrl` (tạo liên kết tuyệt đối cho ảnh/sản phẩm), từ đó ghi vào Meta tags hoặc JSON-LD để đẩy lên Head của trang.
- **WriteStaticPage → NormalizeText**: Khi script `prerender-product-pages.mjs` chạy ở quá trình build, nó lấy text sản phẩm, làm sạch và ghi vào các file HTML tĩnh chuẩn bị cho bot SEO.

### 2.4 Luồng Đặt hàng & Xử lý Đơn hàng (Checkout & Order Lifecycle)
- **Delivery Check → GeoDistance**: Khi chọn địa chỉ nhận hàng, hệ thống dùng `GeoDistanceService` (công thức Haversine) tính khoảng cách từ shop đến vị trí người dùng, và `ShippingFeeService` tính phí giao hàng Standard / Express tương ứng.
- **Checkout → DB Transaction**: Quá trình tạo đơn hàng gom kiểm tra tồn kho (`lockForUpdate`), áp mã giảm giá (`CouponService`), trừ tồn kho biến thể sản phẩm, lưu thông tin giao hàng và dọn dẹp giỏ hàng trong một giao dịch DB duy nhất.
- **Cancel Order → Stock & Voucher Restoration**: Khi hủy đơn, `OrderStatusService` được gọi bên trong DB transaction để hoàn lại số lượng tồn kho cho từng biến thể và giải phóng lượt sử dụng mã giảm giá (`CouponService::release`).
- **Admin Order State Transition**: Admin chuyển trạng thái đơn hàng theo đúng vòng đời (`awaiting_payment/paid` → `processing` → `shipping` → `completed`), nếu vi phạm thứ tự hợp lệ sẽ bị từ chối bằng HTTP 422.

---
**Tổng kết:** Dự án PHP3-TBS có kiến trúc chia tách khá rõ ràng giữa xử lý nghiệp vụ (Controllers, Services), giao diện người dùng (Client, Admin) và bộ công cụ hỗ trợ SEO đặc biệt (Scripts) phục vụ cho thương mại điện tử. Module Order & Delivery mới được hoàn thiện khép kín toàn bộ luồng E2E từ giỏ hàng, thanh toán đến quản lý đơn hàng cho cả khách hàng và quản trị viên.
