# Cấu hình thanh toán payOS

Ứng dụng tạo QR/link thanh toán bằng API payOS. Không đưa khóa thanh toán vào mã nguồn hoặc Git.

## 1. Chuẩn bị payOS

Tạo kênh thanh toán đã xác thực trên payOS và lấy `Client ID`, `API Key`, `Checksum Key`.

## 2. Cấu hình PHP/Laragon

Sao chép `configs/payments.local.example.php` thành `configs/payments.local.php` rồi điền ba khóa từ payOS:

```text
client_id     = Client ID của kênh
api_key       = API Key của kênh
checksum_key  = Checksum Key của kênh
app_public_url = https://ten-mien-cua-ban/HHDM_Fashion/
```

File `payments.local.php` bị Git bỏ qua để không đẩy khóa bí mật lên kho mã nguồn. `app_public_url` phải là HTTPS và trỏ tới thư mục cài ứng dụng. Khởi động lại Apache sau khi điền xong.

## 3. Cài bảng dữ liệu

Chạy lần lượt `database/create_order_items.sql`, `database/create_order_payments.sql` và `database/add_order_delivery_tracking.sql` trên database `website_ban_hang_thoi_trang`.

Để bật yêu cầu hủy đơn và ghi nhận hoàn tiền, chạy thêm `database/add_order_cancellation_workflow.sql` đúng một lần trên cùng database.

## 4. Đăng ký webhook

Trong kênh payOS, đặt webhook URL là:

```text
https://ten-mien-cua-ban/index.php?page=payment_webhook
```

Máy chủ cần truy cập công khai qua HTTPS để payOS gửi thông báo. Khi phát triển trên Laragon, dùng một HTTPS tunnel trỏ về Laragon và đăng ký URL tunnel đó làm webhook. Nếu đổi domain/tunnel thì cập nhật lại `APP_PUBLIC_URL` và webhook trên payOS.

Sau khi khách quét QR, payOS gửi webhook xác thực tới máy chủ. Nếu khách ở lại trên trang QR, trang sẽ kiểm tra trạng thái link thanh toán định kỳ và tự chuyển tới `index.php?page=payment_success&order=...` khi payOS xác nhận đủ số tiền. URL quay về từ payOS cũng được kiểm tra trạng thái trực tiếp với API trước khi hiển thị thành công.
