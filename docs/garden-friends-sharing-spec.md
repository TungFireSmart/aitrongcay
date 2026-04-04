# Aitrongcay — Friends & Garden Sharing Spec

## Mục tiêu
Cho phép người dùng Aitrongcay:
- kết bạn với nhau,
- mời bạn vào một khu vườn cụ thể,
- gán quyền `owner`, `co_owner`, `viewer`,
- phân biệt rõ ai được điều khiển thiết bị và ai chỉ được xem.

---

## Phạm vi phiên bản đầu

### Có trong v1
- Gửi / nhận / chấp nhận / từ chối lời mời kết bạn
- Danh sách bạn bè
- Mời bạn vào khu vườn
- 3 vai trò:
  - `owner`
  - `co_owner`
  - `viewer`
- Chặn quyền điều khiển thiết bị ở backend theo role
- Hiển thị UI theo quyền

### Chưa làm ngay trong v1
- Chat giữa bạn bè
- Public social graph phức tạp
- Nhiều chủ vườn ngang quyền hệ thống
- Block/report nâng cao
- Nhật ký hoạt động chi tiết theo từng hành động

---

## Vai trò & quyền

### owner
Quyền:
- xem toàn bộ khu vườn
- điều khiển thiết bị
- sửa cài đặt khu vườn
- mời / xóa thành viên
- đổi quyền `co_owner` / `viewer`

Không nên cho phép:
- tự xóa owner cuối cùng

### co_owner
Quyền:
- xem toàn bộ khu vườn
- điều khiển thiết bị
- ghi chú / thao tác chăm sóc nếu tính năng có sẵn

Không nên mặc định cho phép:
- đổi owner
- xóa khu vườn
- xóa owner gốc
- mời người khác (để owner giữ kiểm soát ở v1)

### viewer
Quyền:
- xem thông tin khu vườn
- xem ảnh, thông số, nhật ký, trạng thái thiết bị

Không được:
- điều khiển thiết bị
- sửa dữ liệu khu vườn
- mời thêm người khác

---

## Mô hình dữ liệu đề xuất

### 1) friendships
Dùng để lưu quan hệ bạn bè người dùng-người dùng.

| field | type | note |
|---|---|---|
| id | bigint PK | |
| requester_user_id | bigint | người gửi lời mời |
| addressee_user_id | bigint | người nhận |
| status | varchar(20) | `pending`, `accepted`, `rejected`, `blocked`, `cancelled` |
| created_at | datetime | |
| responded_at | datetime nullable | |
| unique_pair_key | varchar(64) | khóa chuẩn hóa cặp user để tránh trùng |

Rule:
- chỉ có 1 friendship active/pending cho 1 cặp user
- cặp A-B và B-A phải chuẩn hóa cùng một `unique_pair_key`

### 2) garden_members
Dùng để lưu user nào thuộc khu vườn nào, với quyền gì.

| field | type | note |
|---|---|---|
| id | bigint PK | |
| garden_id | bigint | id khu vườn |
| user_id | bigint | thành viên |
| role | varchar(20) | `owner`, `co_owner`, `viewer` |
| status | varchar(20) | `invited`, `active`, `removed`, `declined` |
| invited_by_user_id | bigint nullable | ai mời |
| created_at | datetime | |
| updated_at | datetime | |

Rule:
- mỗi user chỉ có 1 membership active/invited trên 1 garden
- luôn phải còn ít nhất 1 `owner`

### 3) garden_invites (optional nếu muốn tách riêng)
Nếu muốn luồng invite rõ ràng hơn có thể tách riêng bảng lời mời vào vườn.
Nếu muốn đơn giản v1, có thể dùng luôn `garden_members.status = invited`.

Khuyến nghị v1:
- **chưa cần bảng riêng**
- dùng `garden_members` là đủ

---

## Ánh xạ với WordPress

### Dữ liệu user
- tận dụng `wp_users`

### Khu vườn
Có thể đang là một thực thể nội bộ / custom object.
Nếu chưa có table riêng cho garden, cần chuẩn hóa `garden_id` trước khi làm membership.

### Khuyến nghị kỹ thuật
Dùng **custom tables** cho:
- `aitr_friendships`
- `aitr_garden_members`

Lý do:
- query dễ hơn
- quyền dễ kiểm soát hơn
- tránh lạm dụng user meta / post meta cho quan hệ nhiều-nhiều

---

## Luồng nghiệp vụ

### A. Gửi lời mời kết bạn
1. User A nhập email/username của user B
2. Hệ thống kiểm tra:
   - user B tồn tại
   - không phải chính mình
   - chưa có quan hệ pending/accepted
3. Tạo friendship `pending`
4. User B thấy lời mời trong mục `Lời mời đã nhận`

### B. Chấp nhận kết bạn
1. User B bấm chấp nhận
2. friendship -> `accepted`
3. hai bên thấy nhau trong danh sách bạn bè

### C. Mời bạn vào khu vườn
1. Owner vào trang `Chia sẻ khu vườn`
2. Chọn một người trong danh sách bạn bè
3. Chọn quyền:
   - `co_owner`
   - `viewer`
4. Tạo `garden_members` với `status = invited`
5. Người được mời chấp nhận hoặc từ chối

### D. Chấp nhận vào khu vườn
1. User nhận lời mời mở mục `Lời mời khu vườn`
2. Chấp nhận -> `status = active`
3. Khu vườn xuất hiện trong tài khoản của họ

### E. Thu hồi quyền
Owner có thể:
- đổi `co_owner -> viewer`
- đổi `viewer -> co_owner`
- remove thành viên

---

## Rule kiểm quyền backend

### Thiết bị / điều khiển
Các endpoint điều khiển như:
- bật/tắt đèn
- bật/tắt bơm
- lệnh tự động khác

phải check:
- user có membership `active`
- role thuộc `owner` hoặc `co_owner`

Nếu là `viewer`:
- trả lỗi 403
- message: `Anh/chị chỉ có quyền xem khu vườn này.`

### Xem dữ liệu vườn
Cho phép với:
- owner
- co_owner
- viewer

### Quản trị chia sẻ
Chỉ `owner` mới được:
- mời người mới
- đổi quyền
- gỡ thành viên

---

## UI / Wireframe đề xuất

### 1) Sidebar portal
Thêm các mục:
- Dashboard
- Kho ảnh
- Trợ lý AI
- **Bạn bè**
- **Chia sẻ khu vườn**

---

### 2) Trang Bạn bè

#### Block A — Tìm bạn
- input: email / username
- button: `Gửi lời mời kết bạn`

#### Block B — Lời mời đã nhận
- tên người gửi
- thời gian
- nút: `Chấp nhận` / `Từ chối`

#### Block C — Bạn bè của tôi
- avatar/tên
- trạng thái
- nút: `Mời vào khu vườn`

#### Block D — Lời mời đã gửi
- người nhận
- trạng thái: chờ phản hồi

---

### 3) Trang Chia sẻ khu vườn

#### Header
- tên khu vườn
- chủ vườn
- số người đang cùng tham gia

#### Block A — Thành viên hiện tại
Danh sách theo nhóm:
- Chủ vườn
- Đồng sở hữu
- Người xem

Mỗi item có:
- tên user
- role badge
- action menu:
  - đổi quyền
  - gỡ khỏi khu vườn

#### Block B — Mời bạn vào khu vườn
- dropdown chọn bạn bè
- radio/select role:
  - Đồng sở hữu
  - Chỉ xem
- button: `Gửi lời mời`

#### Block C — Lời mời đang chờ
- tên user
- quyền dự kiến
- trạng thái: chờ chấp nhận
- action: hủy lời mời

---

## Hiển thị quyền trong giao diện khu vườn

Trên dashboard hoặc header khu vườn, hiển thị badge:
- `Chủ vườn`
- `Đồng sở hữu`
- `Chỉ xem`

### Nếu là viewer
- các nút điều khiển vẫn có thể hiển thị nhưng bị disabled
- hoặc hiện label `Chỉ xem`

Khuyến nghị v1:
- **hiện nút nhưng disabled**
- dễ hiểu hơn việc ẩn hoàn toàn

---

## API / hàm backend đề xuất

### Friendship
- `aitr_send_friend_request($requester_id, $target_id)`
- `aitr_accept_friend_request($friendship_id, $user_id)`
- `aitr_reject_friend_request($friendship_id, $user_id)`
- `aitr_get_user_friends($user_id)`

### Garden sharing
- `aitr_invite_user_to_garden($garden_id, $target_user_id, $role, $inviter_id)`
- `aitr_accept_garden_invite($membership_id, $user_id)`
- `aitr_decline_garden_invite($membership_id, $user_id)`
- `aitr_update_garden_member_role($garden_id, $member_user_id, $role, $actor_id)`
- `aitr_remove_garden_member($garden_id, $member_user_id, $actor_id)`
- `aitr_user_can_control_garden($garden_id, $user_id)`
- `aitr_user_can_view_garden($garden_id, $user_id)`

---

## Triển khai v1 đề xuất

### Bước 1
Tạo custom tables:
- `aitr_friendships`
- `aitr_garden_members`

### Bước 2
Tạo helper functions kiểm quyền

### Bước 3
Tạo 2 trang portal:
- `/portal/ban-be/`
- `/portal/chia-se-khu-vuon/`

### Bước 4
Gắn kiểm quyền vào các endpoint điều khiển thiết bị

### Bước 5
Hiển thị trạng thái role trong dashboard

---

## Guardrails để tránh lỗi sau này

1. Không chỉ ẩn nút ở frontend — **backend phải check quyền thật**.
2. Mọi membership phải luôn có `role` + `status` rõ ràng.
3. Không cho phép user vừa là `viewer` vừa là `co_owner` trên cùng một garden.
4. Không để mất `owner` cuối cùng.
5. Khi refactor UI nhiều item, luôn kiểm tra dữ liệu binding per-item còn đủ cho mọi item.

---

## Quyết định UX chốt cho bản đầu
- Kết bạn là quan hệ user-user.
- Chia sẻ khu vườn là quan hệ user-garden.
- Có 3 role: `owner`, `co_owner`, `viewer`.
- Chỉ `owner` và `co_owner` mới điều khiển thiết bị.
- `viewer` chỉ xem.
