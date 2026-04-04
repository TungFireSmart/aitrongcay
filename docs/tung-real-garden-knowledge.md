# Khu vườn thực tế — Phí Ngọc Tùng (`phingoctung@btk.vn`)

## Mục tiêu
Chuyển portal `aitrongcay.com` từ dữ liệu mẫu sang dữ liệu thật cho tài khoản của anh Tùng, đồng thời chuẩn bị lớp tri thức nền để triển khai trợ lý AI theo đúng 4 chậu cây thực tế.

## Tài khoản
- Email account: `phingoctung@btk.vn`
- Profile mong muốn trên portal:
  - tên hiển thị: **Phí Ngọc Tùng**
  - mã vườn: **TUNG-01**
  - trạng thái: **4 chậu thật • đang theo dõi thực tế**

## Thiết bị
- Có **4 đèn riêng**:
  - Chậu cà chua → **Đèn 1**
  - Chậu rau cải cúc → **Đèn 2**
  - Chậu sâm ngọc linh → **Đèn 3**
  - Chậu rau cải xoong → **Đèn 4**
- Có **1 máy bơm chung** cho cả 4 chậu.

## Hồ sơ chậu cây hiện tại

### 1) P-001 — Chậu cà chua
- Tên hiển thị: **Chậu cà chua**
- Thiết bị: Đèn 1
- Bơm: dùng chung
- Tình trạng quan sát từ ảnh:
  - cây phát triển tốt
  - đã có quả non
  - tán lá nhìn khỏe
  - có vài lá gốc ngả vàng nhẹ
- Gợi ý mô tả AI hiện tại:
  - "Chậu cà chua đang phát triển tốt, đã có quả non. Nên tiếp tục theo dõi lá gốc, độ ẩm giá thể và tốc độ lớn của quả."
- Dữ liệu còn thiếu:
  - pH thật
  - nhiệt độ thật
  - độ ẩm thật
  - lịch bật đèn / bơm thực tế
  - ảnh cận chùm quả / lá / ngọn / thiết bị

### 2) P-002 — Chậu rau cải cúc
- Tên hiển thị: **Chậu rau cải cúc**
- Thiết bị: Đèn 2
- Bơm: dùng chung
- Tình trạng quan sát từ ảnh:
  - cây con mới lên
  - nhiều cốc đã nảy mầm
  - mật độ chưa đồng đều hoàn toàn
  - có khả năng sau này cần tỉa bớt ở các vị trí mọc dày
- Gợi ý mô tả AI hiện tại:
  - "Cải cúc đang ở giai đoạn cây con, cần theo dõi độ đồng đều nảy mầm, ẩm độ và khả năng cần tỉa bớt ở những cốc mọc dày."
- Dữ liệu còn thiếu:
  - pH / nhiệt độ / độ ẩm thật
  - lịch sáng thực tế
  - ảnh gần hơn của từng cốc hoặc cụm cây con

### 3) P-003 — Chậu sâm ngọc linh
- Tên hiển thị: **Chậu sâm ngọc linh**
- Thiết bị: Đèn 3
- Bơm: dùng chung
- Tình trạng quan sát từ ảnh:
  - khay/cốc cây con giai đoạn rất sớm
  - đã nhú ở nhiều vị trí
  - mức phát triển chưa đồng đều
  - cần theo dõi kỹ ổn định môi trường, đặc biệt ẩm độ
- Gợi ý mô tả AI hiện tại:
  - "Sâm ngọc linh đang ở giai đoạn cây con rất sớm, nên theo dõi sát độ ẩm và tỷ lệ phát triển đồng đều giữa các cốc."
- Dữ liệu còn thiếu:
  - pH / nhiệt độ / độ ẩm thật
  - lịch đèn thực tế
  - ảnh bổ sung rõ lá non, thân non và tổng thể khay

### 4) P-004 — Chậu rau cải xoong
- Tên hiển thị: **Chậu rau cải xoong**
- Thiết bị: Đèn 4
- Bơm: dùng chung
- Tình trạng quan sát từ ảnh:
  - mới gieo hạt
  - chưa thấy mầm trên bề mặt giá thể
  - giai đoạn cần theo dõi kỹ độ ẩm bề mặt và thời điểm bắt đầu nảy mầm
- Gợi ý mô tả AI hiện tại:
  - "Cải xoong vừa gieo hạt, chưa thấy mầm. Ưu tiên giữ nền ổn định và theo dõi mốc bắt đầu nảy mầm."
- Dữ liệu còn thiếu:
  - pH / nhiệt độ / độ ẩm thật
  - lịch đèn thực tế
  - ảnh các mốc sau gieo hạt

## Hướng phát triển dữ liệu thật
1. **Portal trước**
   - map đúng account `phingoctung@btk.vn`
   - hiển thị 4 chậu thật thay cho dữ liệu demo
   - thay video/ảnh mẫu bằng media thật khi có file upload được
2. **Tri thức AI sau**
   - xây hồ sơ theo từng chậu
   - thêm nhật ký thay đổi theo mốc
   - thêm rule trả lời riêng cho cây con / cây mới gieo / cây đã ra quả
   - nối thông số cảm biến thật khi có nguồn

## Trạng thái triển khai trong code (2026-04-03)
- Nguồn dữ liệu portal cho profile + 4 chậu thật của anh Tùng đã được gom riêng tại:
  - `wp-content/themes/aitrongcay/inc/portal-garden-data.php`
- Template portal đang đọc từ file này thay vì hardcode toàn bộ data ngay trong `template-parts/virtual/portal.php`.
- Ý nghĩa của bước này:
  - dễ cập nhật thêm ảnh/video/thông số thật theo từng chậu
  - dễ tái sử dụng cùng một data source cho care log / quality / AI page sau này
  - giảm nguy cơ lệch dữ liệu giữa các màn portal

## Tri thức nền ban đầu cho trợ lý AI

### Nguyên tắc trả lời
- Ưu tiên ngắn gọn, thực tế, an toàn.
- Không bịa thông số khi chưa có dữ liệu thật.
- Nếu chưa có số đo thực tế, phải nói rõ là đang đánh giá theo ảnh quan sát.
- Với chậu cây giai đoạn sớm, ưu tiên nhắc theo dõi ổn định môi trường hơn là đưa khuyến nghị mạnh tay.

### Ưu tiên theo dõi hiện tại
1. **Cải xoong** → mới gieo hạt, cần theo dõi nảy mầm
2. **Sâm ngọc linh** → cây con phát triển chưa đồng đều
3. **Cải cúc** → cây con đang lên, theo dõi mật độ và ẩm độ
4. **Cà chua** → đang khỏe, theo dõi lá gốc và quả non

### Mẫu câu AI nên dùng
- "Theo ảnh hiện tại, chậu này đang ở giai đoạn ..."
- "Em chưa có số đo cảm biến thật của chậu này, nên nhận định hiện tại đang dựa trên ảnh và trạng thái đã ghi nhận."
- "Việc cần ưu tiên lúc này là giữ điều kiện ổn định và theo dõi thêm trong các mốc tiếp theo."

## Việc còn cần bổ sung từ owner
- Ảnh/video thật có thể upload được vào site hoặc gửi ở định dạng/file mà agent lưu local được
- Thông số thật cho từng chậu:
  - pH
  - nhiệt độ
  - độ ẩm
  - trạng thái đèn
  - trạng thái bơm
- Lịch vận hành thực tế:
  - giờ bật/tắt đèn
  - chu kỳ bơm
  - ghi chú tưới/phun sương nếu có
- Nhật ký chăm sóc / thay dung dịch / bón dinh dưỡng / xử lý sự cố
