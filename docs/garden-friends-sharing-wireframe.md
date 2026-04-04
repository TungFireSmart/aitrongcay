# Wireframe — Bạn bè & Chia sẻ khu vườn

## 1) Sidebar portal
- Dashboard
- Kho ảnh
- Trợ lý AI
- Bạn bè
- Chia sẻ khu vườn

---

## 2) Trang Bạn bè

### Header
**Bạn bè**
Dùng để kết nối với người khác và mời họ vào khu vườn của mình.

### Section: Tìm bạn
- [input email / username]
- [Gửi lời mời kết bạn]

### Section: Lời mời đã nhận
- Anna — gửi 2 giờ trước — [Chấp nhận] [Từ chối]
- Việt — gửi hôm qua — [Chấp nhận] [Từ chối]

### Section: Bạn bè của tôi
- Anna — Bạn bè — [Mời vào khu vườn]
- Việt — Bạn bè — [Mời vào khu vườn]
- Hà — Bạn bè — [Mời vào khu vườn]

### Section: Lời mời đã gửi
- Linh — Đang chờ phản hồi
- Nga — Đang chờ phản hồi

---

## 3) Trang Chia sẻ khu vườn

### Header
**Chia sẻ khu vườn**
Khu vườn thực tế của anh Tùng

Badge quyền hiện tại:
- Chủ vườn

### Section: Thành viên hiện tại
#### Chủ vườn
- Phí Ngọc Tùng — Chủ vườn

#### Đồng sở hữu
- Anna — Đồng sở hữu — [Đổi quyền] [Gỡ]

#### Người xem
- Việt — Chỉ xem — [Đổi quyền] [Gỡ]
- Hà — Chỉ xem — [Đổi quyền] [Gỡ]

### Section: Mời bạn vào khu vườn
- [dropdown chọn bạn]
- [radio] Đồng sở hữu
- [radio] Chỉ xem
- [Gửi lời mời]

### Section: Lời mời đang chờ
- Linh — mời làm Đồng sở hữu — Đang chờ — [Hủy lời mời]
- Nga — mời làm Chỉ xem — Đang chờ — [Hủy lời mời]

---

## 4) Dashboard khi là viewer

### Badge ở đầu trang
- Chế độ hiện tại: **Chỉ xem**

### Card thiết bị
- Nút điều khiển vẫn hiển thị
- disabled / mờ đi
- có note nhỏ: `Anh/chị đang ở quyền chỉ xem nên không thể điều khiển thiết bị.`

---

## 5) Dashboard khi là co_owner

### Badge ở đầu trang
- Chế độ hiện tại: **Đồng sở hữu**

### Card thiết bị
- thao tác bình thường
- có thể điều khiển đèn / bơm

---

## 6) Trạng thái lời mời
- pending
- accepted
- rejected
- removed

UI nên dùng badge rõ màu:
- pending → vàng
- accepted → xanh
- rejected → xám/đỏ nhẹ
- removed → xám
