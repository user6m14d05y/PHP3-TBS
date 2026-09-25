# PHP3-TBS
Dự án Website bán hoa TBS Laravel + Vue + Docker

## Clone dự án về
```bash
# Clone dự án
git clone <url-repo>
```

## Cấu hình môi trường env
```bash
# Tạo file .env
cp .env.example .env
```

mở file .env ra và sửa lại các thông tin cho phù hợp
```bash
DB_CONNECTION=mysql
DB_HOST=[IP_ADDRESS]
DB_PORT=3307
DB_DATABASE=flower_db_server
DB_USERNAME=root
DB_PASSWORD=root
```

## Khởi động compose 
```bash
# Tạo và khởi động các container
docker-compose up -d
```

## Cài đặt Dependency & Database cho Backend
```bash
# Tạo key
docker compose exec app php artisan key:generate
# Tạo database
docker compose exec app php artisan migrate
```

## Cài đặt alert module
```bash
# Lệnh
sudo npm install sweetalert2

```

## cài sactum token
```bash
# Lệnh
docker compose exec app php artisan install:api
```


## Cấu hình Docker
```bash
# Tạo và khởi động các container
docker-compose up -d

# Dừng các container
docker-compose down

# Xóa các container và volume (nếu muốn xóa database)
docker-compose down -v
```

## Update  Pinia (State Management) 
```bash
# Cập nhật Pinia
npm install pinia axios
```

## Các Cập Nhật Gần Đây (Updates)
- **Geocoding Proxy:** Tích hợp `GeocodeController` ở Backend để gọi API của Nominatim (tránh lỗi thiếu User-Agent bị chặn khi Frontend gọi trực tiếp).
- **Giỏ Hàng (Cart API):** API `/api/cart` đã được cập nhật để trả về thêm trường `product_thumbnail`, giúp Frontend tải đúng ảnh sản phẩm trong giỏ thay vì dùng ảnh mặc định.
- **Quản lý biến môi trường (.env):** Trong Docker, lưu ý file `.env` của `backend` phải là một file độc lập (không dùng symlink) để tránh lỗi kẹt cấu hình (fallback về SQLite).
