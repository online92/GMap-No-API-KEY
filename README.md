# Custom Map Plugin - Bản đồ Địa điểm không cần API

Plugin WordPress đơn giản và mạnh mẽ giúp bạn tạo và quản lý danh sách các địa điểm, hiển thị chúng trên bản đồ Google Maps mà **không cần sử dụng API Key**. Lý tưởng cho việc hiển thị danh sách cửa hàng, chi nhánh, hoặc bất kỳ địa điểm nào bạn muốn.


## ✨ Tính năng nổi bật

* **Không cần API Key:** Hiển thị bản đồ Google Maps chỉ bằng cách sử dụng kinh độ và vĩ độ, giúp bạn tiết kiệm chi phí và không cần phải đăng ký Google Cloud Platform phức tạp.
* **Quản lý địa điểm trực quan:** Giao diện quản trị thân thiện bên trong WordPress giúp bạn dễ dàng Thêm, Sửa, Xóa các địa điểm.
* **Thông tin địa điểm đầy đủ:** Lưu trữ tên, địa chỉ, số điện thoại, hình ảnh, link Google Maps và tọa độ cho mỗi địa điểm.
* **Tải ảnh dễ dàng:** Tích hợp với trình tải đa phương tiện (Media Uploader) của WordPress để chọn ảnh đại diện cho địa điểm.
* **Tùy biến giao diện:** Dễ dàng thay đổi màu sắc của tên, địa chỉ và nút "Chỉ đường" để phù hợp với giao diện website của bạn.
* **Shortcode đơn giản:** Chỉ cần chèn shortcode `[custom_map]` vào bất kỳ trang hoặc bài viết nào để hiển thị bản đồ và danh sách địa điểm.
* **Thiết kế đáp ứng (Responsive):** Tự động điều chỉnh bố cục trên các thiết bị từ máy tính để bàn đến điện thoại di động.
* **Lấy tọa độ thông minh:** Tự động trích xuất kinh độ và vĩ độ khi bạn dán một link từ Google Maps vào.

---

## 🚀 Cài đặt

1.  Tải về file `.zip` của plugin.
2.  Trong trang quản trị WordPress, đi tới **Gói mở rộng** (Plugins) > **Cài mới** (Add New).
3.  Nhấp vào nút **Tải lên plugin** (Upload Plugin).
4.  Chọn file `.zip` bạn vừa tải về và nhấp **Cài đặt** (Install Now).
5.  Sau khi cài đặt xong, nhấp vào **Kích hoạt Plugin** (Activate Plugin).

Một menu mới có tên **"Bản đồ Địa điểm"** sẽ xuất hiện trên thanh công cụ bên trái của bạn.

---

## ⚙️ Hướng dẫn sử dụng

### 1. Thêm địa điểm mới

* Trong trang quản trị, vào **Bản đồ Địa điểm**.
* Nhấp vào nút **"Thêm Địa điểm mới"**.
* Điền đầy đủ các thông tin:
    * **Tên địa điểm:** Tên cửa hàng, chi nhánh...
    * **Số điện thoại:** Số liên hệ.
    * **Địa chỉ:** Địa chỉ chi tiết.
    * **Kinh độ & Vĩ độ:** Tọa độ GPS của địa điểm.
        * **Mẹo:** Mở Google Maps, nhấp chuột phải vào vị trí mong muốn, tọa độ sẽ hiện ra và bạn chỉ cần nhấp để sao chép.
    * **Link Google Maps:** Dán link chỉ đường từ Google Maps vào đây. Plugin sẽ tự động lấy tọa độ giúp bạn.
    * **Hình ảnh:** Nhấp "Chọn hình" để tải lên hoặc chọn ảnh từ thư viện.
* Nhấn **"Lưu địa điểm"**.

### 2. Hiển thị bản đồ ra ngoài website

* Tạo một Trang (Page) mới hoặc sửa một trang có sẵn.
* Trong trình soạn thảo văn bản, chèn shortcode sau vào vị trí bạn muốn hiển thị bản đồ:
    ```
    [custom_map]
    ```
* Lưu trang lại và xem kết quả.

### 3. Tùy chỉnh hiển thị

* Trong trang quản trị, vào **Bản đồ Địa điểm** > tab **Cài đặt Hiển thị**.
* Tại đây bạn có thể thay đổi màu sắc và bố cục hiển thị của bản đồ.
* Nhấn **"Lưu Cài đặt"** sau khi hoàn tất.

---

## 📄 Giấy phép (License)

Plugin này được phát hành dưới giấy phép **GPLv2 (or later)**.
Điều này có nghĩa là bạn được tự do sử dụng, sửa đổi và phân phối lại mã nguồn.
