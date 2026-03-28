# Vườn thuê số hóa — Full UI Spec theo Mẫu 1 “Vườn Riêng Trong Tầm Tay”

Phiên bản này là gói UI/UX để owner duyệt **trước khi đi vào DB/schema**.

Bám các quyết định đã chốt:
- Đối tượng giai đoạn 1: **hộ gia đình / cá nhân**
- Trục cảm xúc chính: **cảm giác sở hữu**
- USP phải luôn hiện diện: **trải nghiệm số mạnh, webcam 24/7, minh bạch chất lượng, an toàn thực phẩm**
- Tech direction: **WordPress + Blocksy + WooCommerce + custom portal plugin**
- Portal không phải “khu quản trị kỹ thuật”, mà là **không gian sở hữu số** của khách hàng

---

## 1) Tư tưởng thiết kế chốt cho toàn bộ hệ thống

### 1.1. Big idea
**“Một khu vườn riêng của gia đình, luôn ở trong tầm mắt và trong tầm kiểm soát.”**

Website public phải bán được 3 cảm giác ngay từ đầu:
1. **Đây là vườn của tôi**, không phải một dịch vụ mơ hồ.
2. **Tôi nhìn thấy được**, không phải chỉ nghe cam kết.
3. **Tôi yên tâm cho gia đình dùng**, vì chất lượng được chứng minh.

Portal phải kéo dài đúng cảm giác đó mỗi ngày:
- mở ra là thấy vườn,
- thấy tình trạng hôm nay,
- thấy lịch sử chăm sóc,
- thấy tiến trình lớn lên,
- thấy an toàn thực phẩm được ghi nhận rõ ràng.

### 1.2. Brand personality
- Ấm áp, sáng, tử tế
- Hiện đại nhưng không lạnh
- Premium vừa phải, không xa cách
- Công nghệ là công cụ tạo niềm tin, không phải thứ để khoe kỹ thuật

### 1.3. UX principles
1. **Ownership first**: luôn dùng ngôn ngữ “vườn của bạn / gia đình bạn / khu vườn này”.
2. **See before trust**: ảnh thật, dữ liệu thật, nhật ký thật đứng trước lời hứa.
3. **Low anxiety**: thông tin kỹ thuật được giải thích dễ hiểu.
4. **Daily habit**: portal phải khiến khách muốn mở mỗi ngày, dù chỉ 30–60 giây.
5. **Family-friendly**: ngôn ngữ đơn giản, visual sạch, không quá dày số liệu.
6. **WordPress-realistic**: public site ưu tiên cấu trúc dễ dựng bằng Blocksy + Gutenberg/blocks; portal là custom plugin nhưng vẫn nên dùng design language đồng bộ.

---

# 2) Design system ngắn gọn

## 2.1. Màu sắc

### Core palette
- **Forest Green** `#1F6B45` — màu chủ đạo thương hiệu, dùng cho CTA chính, heading nhấn, trust blocks
- **Fresh Green** `#7BC47F` — accent tích cực, badge “đang phát triển tốt”, điểm nhấn đồ họa
- **Cream** `#F7F4EC` — nền chính ấm, giúp premium hơn trắng tinh
- **White** `#FFFFFF` — nền card, modal, webcam canvas, form
- **Charcoal** `#243127` — text chính
- **Soft Gold** `#C8A96B` — highlight premium, plan recommended, thành tựu/seasonal milestone

### Supporting palette
- **Mint Mist** `#EAF5EA` — success soft background
- **Sky Data** `#DDEFF6` — data/info cards nhẹ
- **Warm Sand** `#EFE6D6` — khối storytelling / family moments
- **Alert Amber** `#D89B2B` — cảnh báo cần chú ý
- **Soft Red** `#C95C54` — lỗi / bất thường quan trọng

### Semantic use
- Success: `#2D8A57`
- Warning: `#D89B2B`
- Danger: `#C95C54`
- Info: `#4F8FA8`
- Neutral border: `#D9E2D8`
- Soft divider: `#E9EEE7`

## 2.2. Typography

### Font đề xuất
- **Heading:** Be Vietnam Pro SemiBold/Bold
- **Body/UI:** Inter Regular/Medium
- Nếu cần 1 font duy nhất cho dễ triển khai: **Be Vietnam Pro** toàn hệ thống

### Type scale
- H1: 52/60 desktop, 34/42 mobile
- H2: 40/48 desktop, 28/36 mobile
- H3: 30/38 desktop, 24/32 mobile
- H4: 24/32
- Body L: 18/30
- Body M: 16/26
- Body S: 14/22
- Caption: 12/18

### Tone chữ
- Heading: chắc, ấm, sáng sủa
- Body: dễ đọc, ít “marketing sáo”
- Không dùng quá nhiều chữ in hoa; chỉ dùng cho badge/eyebrow nhỏ

## 2.3. Spacing & layout
- Base unit: **8px**
- Section vertical: 96–120 desktop, 64–80 mobile
- Card padding: 24 hoặc 32
- Border radius:
  - Card lớn: 24px
  - Card thường: 20px
  - Input/button: 14–16px
  - Webcam/live tile: 20px
- Max content width: 1200–1280px
- Grid:
  - Desktop: 12 cột
  - Tablet: 8 cột
  - Mobile: 4 cột

## 2.4. Shadows & surfaces
- Shadow 1: rất nhẹ cho card thường
- Shadow 2: trung bình cho pricing nổi bật / hero mockup
- Không dùng shadow nặng; ưu tiên border mềm + tương phản nền

## 2.5. Iconography
- Icon line bo góc mềm
- Chủ đề: leaf, shield, droplet, webcam, timeline, sparkle, family, package, AI assist
- Nét không quá tech/cyber

## 2.6. Ảnh & visual language
- Ảnh thật: rau xanh sạch, ánh sáng tự nhiên, cảm giác buổi sáng, gia đình thật
- UI mockup: sáng, sạch, rõ, ít chart phức tạp
- Webcam frame: phải tạo cảm giác “live nhưng an tâm”, không kiểu camera an ninh lạnh lẽo
- Minh họa thêm: line illustration nhẹ cho empty states / onboarding / support

## 2.7. Components cốt lõi

### Buttons
- Primary: nền Forest Green, chữ trắng
- Secondary: nền trắng, viền xanh đậm
- Tertiary: text link có icon arrow
- Ghost small: cho thao tác nhẹ trong portal

### Chips / badges
- `Đang phát triển tốt`
- `Sắp thu hoạch`
- `Có cảnh báo nhẹ`
- `Dữ liệu đã cập nhật`
- `Camera đang hoạt động`
- `Gói phổ biến nhất`

### Cards
- Feature card
- Trust evidence card
- Pricing card
- Journal/log card
- Sensor stat card
- AI summary card
- Family share card

### Form elements
- Input bo góc mềm
- Dropdown có label rõ
- Stepper cho onboarding
- Date + time + status display dễ nhìn

### Portal widgets
- Live camera widget
- Today summary widget
- Garden health widget
- Harvest countdown widget
- Care log timeline widget
- Food safety evidence widget
- Alert center widget
- AI ask box

---

# 3) IA và điều hướng tổng thể

## 3.1. Public website main nav
- Trang chủ
- Cách hoạt động
- Gói vườn
- Trải nghiệm số
- An toàn thực phẩm
- Review
- FAQ
- CTA chính: `Xem gói vườn`
- CTA phụ: `Đăng ký tư vấn`
- Link nhỏ: `Đăng nhập`

## 3.2. Portal navigation
### Sidebar / tab chính
1. Tổng quan vườn của tôi
2. Live webcam 24/7
3. Tình trạng vườn realtime
4. Nhật ký chăm sóc
5. Lịch gieo trồng & thu hoạch
6. Chất lượng & an toàn thực phẩm
7. Album tăng trưởng / timelapse
8. Điều khiển vườn
9. Hỗ trợ & sự cố
10. AI gardener
11. Thông báo
12. Gói dịch vụ của tôi
13. Thanh toán & hóa đơn
14. Thiết bị / camera / cảm biến
15. Hồ sơ gia đình
16. Cài đặt chia sẻ & riêng tư

### Portal top bar
- Garden switcher (nếu sau này 1 user có nhiều vườn)
- Trạng thái tổng quan hôm nay
- Icon thông báo
- Nút `Hỏi AI`
- Avatar/account

---

# 4) Public website — full page-by-page UI spec

## 4.1. Trang chủ

### Mục tiêu
- Chốt định vị cực nhanh
- Tạo cảm giác sở hữu
- Chứng minh điểm khác biệt số hóa
- Đẩy user sang gói vườn hoặc đăng ký tư vấn

### Section 1 — Header
- Logo trái
- Menu ở giữa
- `Xem gói vườn` là CTA primary
- `Đăng ký tư vấn` là CTA secondary
- `Đăng nhập` nhỏ góc phải
- Sticky khi scroll, nền trắng/cream mờ nhẹ

### Section 2 — Hero
**Nội dung:**
- Eyebrow: `Vườn thuê số hóa cho gia đình hiện đại`
- H1: `Sở hữu một khu vườn sạch của riêng gia đình — luôn ở trong tầm mắt, 24/7`
- Supporting text: nhấn webcam, dữ liệu minh bạch, theo dõi tăng trưởng, an toàn thực phẩm nhìn thấy được
- CTA 1: `Chọn gói phù hợp`
- CTA 2: `Xem demo vườn realtime`
- 3 proof chips dưới CTA:
  - `Webcam 24/7`
  - `Nhật ký chăm sóc minh bạch`
  - `Theo dõi chất lượng theo lô`

**Visual:**
- Bên phải là cụm mockup desktop + mobile
- Một khung camera live lớn
- 3 floating cards:
  - `Nhiệt độ / độ ẩm hôm nay`
  - `AI nhận định: vườn ổn định`
  - `Còn 6 ngày tới thu hoạch`

### Section 3 — 3 giá trị cốt lõi
3 card ngang:
1. **Nhìn thấy vườn 24/7**
2. **Theo dõi rõ chất lượng từng đợt**
3. **Cảm giác đây là vườn của chính mình**

Mỗi card gồm icon + tiêu đề + 2 dòng mô tả + micro proof.

### Section 4 — How it works mini flow
4 bước ngang có minh họa nhỏ:
1. Chọn gói vườn phù hợp gia đình
2. Hệ thống kích hoạt khu vườn số cho bạn
3. Bạn theo dõi bằng portal, webcam và AI
4. Thu hoạch, tiếp tục mùa vụ, lưu lại hành trình phát triển

### Section 5 — “Vườn của bạn trên màn hình”
Đây là section quan trọng nhất sau hero.

**Layout:** 2 cột
- Trái: webcam lớn + 3 thumbnail snapshot theo mốc sáng/trưa/tối
- Phải: dashboard summary mô phỏng
  - tình trạng vườn hôm nay
  - nhật ký gần nhất
  - nhắc việc / cảnh báo nhẹ
  - thu hoạch sắp tới

**Copy trọng tâm:**
- “Không chỉ mua rau sạch. Gia đình bạn đang đồng hành với cả một quá trình lớn lên.”

### Section 6 — Minh bạch chất lượng & an toàn thực phẩm
4 evidence cards:
1. Quy trình chăm sóc chuẩn hóa
2. Dữ liệu môi trường được ghi nhận
3. Nhật ký can thiệp rõ ràng theo thời gian
4. Truy xuất đợt thu hoạch / lô sản phẩm

Dưới 4 card có một strip chứng cứ:
- “Xem được gì trong portal?”
  - nhiệt độ
  - độ ẩm
  - EC/pH (nếu có)
  - lịch sử chăm sóc
  - xác nhận đợt thu hoạch

### Section 7 — Gói vườn nổi bật
3 pricing cards:
- Mini
- Family
- Premium

**Card structure:**
- Tên gói
- Subline “phù hợp cho ai”
- 4–6 bullet giá trị
- Giá từ / tháng
- CTA `Xem chi tiết gói`
- Family có badge `Phổ biến nhất`

### Section 8 — Review & nhật ký khách hàng
- Carousel review có ảnh gia đình thật
- Kế bên là timeline “7 ngày đầu” mẫu
  - Ngày 1: kích hoạt vườn
  - Ngày 3: xem camera lần đầu
  - Ngày 5: nhận AI summary
  - Ngày 7: thấy thay đổi rõ của cây

### Section 9 — FAQ rút gọn
5–6 câu:
- Tôi sẽ xem được những gì?
- Đây là vườn thật hay chỉ là gói dịch vụ?
- Gia đình tôi có thể cùng theo dõi không?
- Nếu camera bị gián đoạn thì sao?
- Rau thu hoạch được quản lý thế nào?

### Section 10 — Final CTA
- Headline: `Bắt đầu với khu vườn số đầu tiên của gia đình bạn`
- Short form:
  - Họ tên
  - Số điện thoại
  - Quy mô gia đình
  - Mục tiêu chính
- CTA: `Bắt đầu với vườn của tôi`
- Secondary reassurance: `Tư vấn miễn phí, chưa cần thanh toán ngay`

### Section 11 — Footer
- Logo / mô tả ngắn
- Menu chính
- Liên hệ
- Chính sách
- Mạng xã hội
- Copyright

---

## 4.2. Trang Cách hoạt động

### Mục tiêu
Giải tỏa mô hình mới bằng UX rất dễ hiểu.

### Cấu trúc
1. Hero giải thích ngắn “Bạn sở hữu trải nghiệm — hệ thống vận hành nền”
2. 5 bước lớn
   - Chọn gói
   - Kích hoạt vườn tiêu chuẩn
   - Nhận tài khoản portal
   - Theo dõi / tương tác / nhận AI summary
   - Thu hoạch & tiếp tục mùa vụ
3. Khối tách bạch trách nhiệm
   - Hệ thống lo gì
   - Khách kiểm soát được gì
4. Khối minh họa dòng thời gian 30 ngày đầu
5. Khối FAQ mini về mô hình vận hành
6. CTA sang gói vườn

### UI note
- Dùng step cards lớn, ít chữ, nhiều icon + mini screenshot
- Có một infographic “vườn vật lý ↔ portal số ↔ gia đình”

---

## 4.3. Trang Chọn gói vườn

### Mục tiêu
Giúp khách tự định vị nhanh và so sánh dễ.

### Cấu trúc
1. Hero + bộ lọc nhanh
   - Quy mô gia đình
   - Ưu tiên: rau sạch / trải nghiệm số / theo dõi cho con nhỏ / quà tặng
2. Pricing cards 3 gói
3. Bảng so sánh chi tiết
4. Add-ons
5. FAQ giá / cam kết / nâng cấp
6. Sticky CTA đăng ký tư vấn

### Nội dung từng card gợi ý
#### Mini
- Cho 1–2 người
- Webcam cơ bản
- Dashboard theo dõi cơ bản
- AI summary định kỳ

#### Family
- Cho 3–4 người
- Webcam ổn định + dữ liệu chi tiết hơn
- Nhật ký chăm sóc đầy đủ
- Chia sẻ cho người thân

#### Premium
- Trải nghiệm sở hữu mạnh nhất
- Timelapse / album tăng trưởng nâng cao
- Mức giải thích AI sâu hơn
- Ưu tiên hỗ trợ / nhiều quyền tùy biến hơn

### UI note
- Giá không nên là thứ to nhất; `phù hợp cho ai` và `trải nghiệm nhận được` phải nổi hơn

---

## 4.4. Trang Trải nghiệm số & webcam 24/7

### Mục tiêu
Bán điểm khác biệt lớn nhất của sản phẩm.

### Cấu trúc
1. Hero với visual webcam/live UI lớn
2. Các tính năng số chính
   - Live webcam
   - Snapshot theo mốc
   - Timelapse
   - Dashboard cảm biến
   - AI summary
   - Cảnh báo bất thường
3. Multi-screen mockup gallery
4. “Một ngày với khu vườn số”
5. Khối so sánh trước/sau với mô hình trồng sạch truyền thống thiếu minh bạch
6. CTA `Xem giao diện khách hàng`

### UI note
- Nên có video demo hoặc animated prototype
- Đây là trang phù hợp chạy ads retargeting

---

## 4.5. Trang Chất lượng & an toàn thực phẩm

### Mục tiêu
Biến niềm tin thành thứ nhìn thấy được.

### Cấu trúc
1. Hero: `Sạch phải nhìn thấy được`
2. 4 lớp chứng minh
3. Khối “Bạn sẽ xem được gì trong portal?”
4. Khối “AI giúp giải thích chỉ số ra sao?”
5. Mẫu nhật ký chăm sóc
6. Mẫu chứng từ/đợt thu hoạch/truy xuất
7. FAQ về chất lượng
8. CTA đăng ký tư vấn

### UI note
- Dùng tone trust: xanh đậm + cream + white
- Có thể dùng cards dạng tài liệu/evidence panel

---

## 4.6. Trang Câu chuyện “Vườn của bạn”

### Mục tiêu
Kéo cảm xúc sở hữu lên mức sâu hơn.

### Cấu trúc
1. Hero storytelling về gia đình hiện đại muốn rau sạch nhưng thiếu thời gian
2. 3 tình huống use case
   - Gia đình có con nhỏ
   - Người sống thành thị muốn kết nối với việc trồng trọt
   - Quà tặng ý nghĩa cho bố mẹ / người thân
3. Hành trình từ “mua rau” sang “đồng hành cùng khu vườn”
4. Các khoảnh khắc cảm xúc
   - xem mầm đầu tiên
   - chờ tới ngày thu hoạch
   - chia sẻ với con cái/người thân
5. CTA `Chọn gói cho gia đình tôi`

### UI note
- Trang này nhiều ảnh và copy cảm xúc hơn, ít technical hơn

---

## 4.7. Trang Bảng giá / Đăng ký tư vấn

### Mục tiêu
Trang chuyển đổi tập trung.

### Cấu trúc
1. Hero ngắn
2. Form tư vấn chính
3. Tóm tắt 3 gói
4. Lý do để lại thông tin ngay
5. FAQ thanh toán / quy trình sau khi đăng ký
6. Hotline / Zalo / hỗ trợ trực tiếp

### Form fields
- Họ tên
- Số điện thoại
- Khu vực
- Quy mô gia đình
- Mục tiêu chính
- Gói quan tâm
- Ghi chú

---

## 4.8. FAQ

### Cấu trúc nhóm câu hỏi
- Mô hình hoạt động
- Theo dõi webcam và portal
- Chất lượng và an toàn thực phẩm
- Giá / hợp đồng / thanh toán
- Hỗ trợ kỹ thuật

### UX note
- Có sticky mini TOC bên trái trên desktop
- Sau mỗi nhóm có mini CTA `Cần tư vấn riêng?`

---

## 4.9. Review / nhật ký khách hàng

### Mục tiêu
Social proof thật, không giả cảm.

### Cấu trúc
1. Hero với số liệu xã hội vừa đủ
2. Review cards
3. Nhật ký theo tuần của một số khách mẫu
4. Video / ảnh / screenshot portal thật
5. CTA tư vấn

### UI note
- Ưu tiên screenshot thật + ảnh người thật
- Có thể filter theo “gia đình nhỏ / ưu tiên rau cho con / mê công nghệ”

---

## 4.10. Thư viện hình ảnh / video / live demo

### Mục tiêu
Cho khách “thấy tận mắt”.

### Cấu trúc
- Gallery theo tab: webcam, portal, tăng trưởng cây, thu hoạch, video demo
- CTA `Đăng ký xem demo thực tế`

---

## 4.11. Blog / kiến thức

### Mục tiêu
SEO + nuôi niềm tin.

### Chuyên mục
- Rau sạch cho gia đình
- Cách đọc chỉ số vườn
- Hành trình trồng trọt số hóa
- Chăm sóc cây tại nhà
- Câu chuyện khách hàng

### Card style
- Ảnh lớn, tag, tiêu đề rõ, excerpt ngắn

---

## 4.12. Về chúng tôi

### Mục tiêu
Tăng trust doanh nghiệp.

### Cấu trúc
- Sứ mệnh
- Vì sao làm mô hình này
- Năng lực vận hành / công nghệ / tiêu chuẩn
- Đội ngũ / quy trình / cam kết
- CTA liên hệ

---

## 4.13. Liên hệ
- Form liên hệ
- Hotline / Zalo / email
- Giờ hỗ trợ
- Bản đồ / khu vực phục vụ
- FAQ nhanh

---

## 4.14. Đăng ký / tạo tài khoản

### Mục tiêu
Làm mềm cảm giác bước vào hệ thống.

### Cấu trúc
- Hero nhỏ: `Bắt đầu hành trình với khu vườn của bạn`
- Form email/SĐT/password hoặc social login nếu sau này cần
- Khối giải thích tài khoản sẽ dùng để làm gì
- Link đăng nhập nếu đã có tài khoản

---

## 4.15. Đăng nhập

### Mục tiêu
Nhanh, yên tâm, rõ ràng.

### Cấu trúc
- Card đăng nhập tối giản
- Minh họa nhẹ bên phải: preview portal hoặc webcam tile
- Link quên mật khẩu
- Support link

---

## 4.16. Onboarding form trước khi nhận vườn

### Mục tiêu
Thu thông tin để cá nhân hóa trải nghiệm.

### Step flow
1. Thông tin gia đình
2. Mục tiêu sử dụng
3. Mức độ quan tâm tới theo dõi số
4. Tùy chọn chia sẻ cho người thân
5. Xác nhận thông tin

### UX note
- Stepper 5 bước rất nhẹ
- Cuối form có màn “Sắp hoàn tất khu vườn số của bạn”

---

## 4.17. Trang cảm ơn / xác nhận đăng ký

### Mục tiêu
Giữ đà cảm xúc sau chuyển đổi.

### Nội dung
- Xác nhận gửi thành công
- Nêu bước tiếp theo
- CTA phụ xem demo portal hoặc đọc FAQ
- Có timeline nhỏ “tiếp theo sẽ là gì”

---

# 5) Customer portal — full page-by-page UI spec

## 5.1. Nguyên tắc portal
Portal phải cho cảm giác:
- đang bước vào **tài sản số** của gia đình mình,
- không phải dashboard khô khan,
- nhưng vẫn đủ tin cậy và đủ dữ liệu.

### Khung layout chung
- Sidebar trái cố định trên desktop
- Topbar gọn có trạng thái hôm nay
- Nội dung dạng widget cards
- Mỗi trang đều nên có 1 hero state nhỏ kiểu “Hôm nay vườn của bạn thế nào?”

---

## 5.2. Tổng quan vườn của tôi (Dashboard)

### Mục tiêu
Là trang mở vào mỗi ngày. Trong 10 giây phải trả lời được:
- vườn hôm nay ổn không,
- có gì mới,
- còn bao lâu thu hoạch,
- camera có hoạt động không.

### Cấu trúc
1. Welcome header
   - `Chào anh/chị, đây là tình trạng vườn của gia đình hôm nay`
   - Garden name
   - Season/day count
2. Hero summary row
   - Garden health score / trạng thái
   - Camera status
   - Harvest countdown
   - Latest update time
3. Main live preview card
   - webcam preview lớn
   - nút vào live full screen
4. Hôm nay có gì mới
   - 3–5 update cards
5. AI summary card
   - `Hôm nay vườn ổn định...`
6. Chăm sóc gần đây timeline
7. Cảnh báo / nhắc việc
8. Shortcut cards
   - xem chất lượng
   - xem nhật ký
   - hỏi AI
   - gửi yêu cầu hỗ trợ

### Widget priority
1. Live preview
2. Hôm nay summary
3. Harvest countdown
4. Latest care log
5. Alerts

---

## 5.3. Live webcam 24/7

### Mục tiêu
Trang “wow” và là neo cảm giác sở hữu.

### Cấu trúc
1. Webcam live lớn
2. Camera controls nhẹ
   - full screen
   - snapshot
   - chọn mốc thời gian snapshot nếu có
3. Camera status panel
   - online/offline
   - cập nhật gần nhất
   - góc nhìn
4. Snapshot timeline
5. Timelapse preview shortcut
6. FAQ mini nếu camera gián đoạn

### UI note
- Không nên quá nhiều control như camera security app
- Ưu tiên trải nghiệm ấm, sạch, đơn giản

---

## 5.4. Tình trạng vườn realtime

### Mục tiêu
Biến dữ liệu thành thứ gia đình hiểu được.

### Cấu trúc
1. Header state
   - `Hôm nay vườn đang phát triển tốt`
2. Sensor cards
   - nhiệt độ
   - độ ẩm
   - ánh sáng
   - EC/pH nếu có
3. Trend mini charts
4. Chỉ số nào đáng chú ý hôm nay
5. AI giải thích các chỉ số
6. History range selector

### UX note
- Mỗi chỉ số đều có text giải thích ngắn “Mức này có ý nghĩa gì?”
- Tránh chart quá phức tạp

---

## 5.5. Nhật ký chăm sóc & can thiệp

### Mục tiêu
Cho khách thấy minh bạch vận hành thật.

### Cấu trúc
1. Timeline theo ngày
2. Mỗi log card gồm:
   - thời gian
   - hạng mục chăm sóc
   - người/hệ thống thực hiện
   - ghi chú ngắn
   - ảnh nếu có
3. Filter theo loại sự kiện
4. AI digest “tuần này đã có gì diễn ra?”

### UI note
- Dạng timeline đứng rất phù hợp
- Có thể group theo ngày để dễ quét

---

## 5.6. Lịch gieo trồng & thu hoạch

### Mục tiêu
Tăng kỳ vọng tích cực và nhịp theo dõi dài hạn.

### Cấu trúc
1. Current season progress bar
2. Các mốc quan trọng
   - gieo trồng
   - giai đoạn phát triển
   - dự kiến thu hoạch
3. Calendar / timeline
4. Upcoming milestones
5. Lịch sử mùa vụ trước nếu có

### UI note
- Nên có cảm giác “journey” chứ không chỉ lịch khô

---

## 5.7. Chất lượng & an toàn thực phẩm

### Mục tiêu
Là trang trust mạnh nhất trong portal.

### Cấu trúc
1. Trust summary hero
2. 4 nhóm chứng cứ
   - điều kiện môi trường
   - nhật ký chăm sóc
   - đợt thu hoạch
   - truy xuất / xác nhận
3. Batch/harvest cards
4. Download/export chứng từ nếu sau này cần
5. AI giải thích “vì sao đợt này đạt trạng thái tốt”

### UI note
- Thiết kế giống hồ sơ minh bạch, không phải giấy tờ khô cứng

---

## 5.8. Album tăng trưởng / timelapse

### Mục tiêu
Tạo niềm vui cảm xúc và khả năng chia sẻ.

### Cấu trúc
1. Hero collage ảnh tăng trưởng
2. Tabs: ảnh theo mốc / timelapse / cột mốc mùa vụ
3. Bộ lọc theo tuần/tháng/đợt
4. CTA tải ảnh / chia sẻ cho gia đình

### UX note
- Đây là một tính năng giữ chân rất mạnh
- Có thể thêm “khoảnh khắc đáng nhớ” badge

---

## 5.9. Điều khiển vườn

### Mục tiêu
Chỉ hiển thị các quyền điều khiển thật sự được phép, tránh fake control.

### Cấu trúc
1. Intro card giải thích mức độ điều khiển của gói hiện tại
2. Các module điều khiển khả dụng
   - tưới
   - ánh sáng
   - chế độ chăm sóc
   - lịch tự động
3. Safety notes / giới hạn
4. Lịch sử lệnh điều khiển

### UX note
- Nếu giai đoạn đầu chưa có full control thật, nên dùng wording như:
  - `Gửi yêu cầu điều chỉnh`
  - `Đề xuất chế độ ưu tiên`
  thay vì làm user tưởng mình điều khiển trực tiếp mọi thứ

---

## 5.10. Hỗ trợ & xử lý sự cố

### Mục tiêu
Giảm lo lắng khi có trục trặc.

### Cấu trúc
1. Quick help state
2. Tạo yêu cầu hỗ trợ
3. Danh sách vấn đề thường gặp
4. Trạng thái ticket / yêu cầu đang mở
5. Link chat AI hoặc hotline

### UI note
- Tone phải rất trấn an, không làm user thấy hệ thống mong manh

---

## 5.11. AI gardener

### Mục tiêu
Tạo lớp đồng hành thông minh.

### Cấu trúc
1. Chat interface
2. Suggested prompts
   - `Hôm nay vườn của tôi thế nào?`
   - `Bao lâu nữa thu hoạch?`
   - `Chỉ số nào cần chú ý?`
   - `Tại sao cây tuần này lớn chậm hơn?`
3. AI summary cards
4. History / saved answers

### UX note
- AI nên nói như người trợ lý chăm vườn dễ hiểu, không kiểu kỹ sư dữ liệu

---

## 5.12. Thông báo & cảnh báo

### Mục tiêu
Tập trung hóa mọi thay đổi quan trọng.

### Cấu trúc
- Tabs: tất cả / cảnh báo / cập nhật / mùa vụ / thanh toán
- Notification cards có mức độ ưu tiên
- Mark as read / bulk actions

### UI note
- Thông báo quan trọng dùng màu có tiết chế, tránh làm user mệt

---

## 5.13. Gói dịch vụ của tôi

### Mục tiêu
Cho khách hiểu mình đang sở hữu gói gì và các quyền lợi nào.

### Cấu trúc
1. Current plan hero
2. Quyền lợi gói
3. Add-ons đang dùng
4. Nâng cấp / đổi gói
5. FAQ hợp đồng / kỳ hạn

---

## 5.14. Thanh toán & hóa đơn

### Mục tiêu
Rõ ràng, gọn, ít ma sát.

### Cấu trúc
1. Tóm tắt thanh toán hiện tại
2. Hóa đơn gần đây
3. Phương thức thanh toán
4. Lịch sử giao dịch
5. CTA thanh toán / cập nhật phương thức

---

## 5.15. Thiết bị / cảm biến / camera

### Mục tiêu
Tăng minh bạch kỹ thuật ở mức vừa đủ.

### Cấu trúc
1. Danh sách camera/cảm biến
2. Trạng thái từng thiết bị
3. Lần cập nhật gần nhất
4. Hướng dẫn ngắn khi có gián đoạn

### UX note
- Không biến thành trang admin kỹ thuật nặng

---

## 5.16. Hồ sơ gia đình / địa điểm đặt vườn

### Mục tiêu
Cá nhân hóa portal.

### Cấu trúc
- Thông tin tài khoản chính
- Thành viên gia đình
- Địa điểm / tên gọi khu vườn
- Mục tiêu sử dụng
- Preferences

---

## 5.17. Cài đặt quyền riêng tư / chia sẻ cho người thân

### Mục tiêu
Giữ “vườn là của gia đình” nhưng vẫn chia sẻ được.

### Cấu trúc
1. Ai có thể xem gì
2. Quyền chia sẻ camera
3. Quyền xem nhật ký / album / AI chat
4. Tạo lời mời cho người thân
5. Thu hồi quyền

### UX note
- Đây là feature rất hợp concept gia đình, nên được nhấn vừa đủ ngay từ phase đầu

---

# 6) Desktop / mobile notes

## 6.1. Public website desktop
- Hero nên có bố cục 2 cột rõ, visual mockup đủ lớn
- Pricing cards hiển thị 3 cột ngang
- FAQ có TOC hoặc accordion 2 cột nếu cần
- Review section nên dùng layout ảnh + quote + timeline song song

## 6.2. Public website mobile
- Hero xếp dọc: copy -> CTA -> proof chips -> mockup
- Menu dùng drawer, nhưng phải giữ CTA `Xem gói vườn` dễ thấy
- Pricing card chuyển thành stack + sticky compare CTA
- Long forms chia step nhẹ nếu cần
- Các section trust/evidence nên dùng carousel hoặc stacked cards tránh quá dài ngang

## 6.3. Portal desktop
- Sidebar cố định rất quan trọng
- Dashboard dùng grid 12 cột:
  - live preview chiếm lớn nhất
  - summary widgets ở hàng đầu
  - timeline/log ở dưới
- Live camera page nên có khu video chiếm tối thiểu 60–70% chiều ngang nội dung chính

## 6.4. Portal mobile
- Không cố nhồi full dashboard desktop xuống mobile
- Mobile dashboard nên ưu tiên theo thứ tự:
  1. Tình trạng hôm nay
  2. Live preview nhỏ
  3. Cảnh báo
  4. Thu hoạch sắp tới
  5. Nhật ký gần đây
- Sidebar đổi thành bottom nav hoặc menu drawer
- Bottom nav gợi ý:
  - Tổng quan
  - Camera
  - Nhật ký
  - AI
  - Tài khoản
- Camera mobile phải vào nhanh trong 1 tap
- AI chat mobile nên rất thuận ngón tay, giống chat app

## 6.5. Responsive behavior notes
- Webcam ratio giữ ổn định 16:9 hoặc 4:3 tùy nguồn
- Chart phức tạp trên mobile chuyển thành card summary + “xem chi tiết”
- Bảng so sánh gói trên mobile dùng stacked comparison, không giữ table rộng

---

# 7) WordPress / Blocksy / WooCommerce / custom portal plugin mapping

## 7.1. Nên làm bằng WordPress + Blocksy
- Trang chủ
- Cách hoạt động
- Chọn gói vườn
- Trải nghiệm số
- Chất lượng & an toàn thực phẩm
- Câu chuyện “Vườn của bạn”
- FAQ
- Review
- Blog
- Liên hệ
- Về chúng tôi
- Landing pages ads/SEO

## 7.2. Nên dùng WooCommerce cho
- Catalog gói vườn
- Add-ons
- Checkout / đăng ký ban đầu
- Quản lý đơn / thanh toán cơ bản
- Có thể dùng product type theo plan/subscription direction

## 7.3. Nên làm custom portal plugin cho
- Dashboard logged-in
- Webcam / timelapse / sensor UI
- Nhật ký chăm sóc
- Chất lượng / batch / traceability
- AI gardener
- Alert center
- Family sharing
- Thiết bị / trạng thái camera / cảm biến

## 7.4. Design consistency notes
- Public site và portal phải cùng design language nhưng portal có nền sáng sạch hơn, thiên utility hơn một chút
- Header/logo/brand token phải đồng nhất
- CTA labels nên thống nhất để tránh đứt trải nghiệm

---

# 8) Build priority đề xuất

## Phase A — Chốt trải nghiệm bán hàng trước
**Ưu tiên cao nhất** vì owner cần duyệt định hướng trước khi DB:
1. Design system nền tảng
2. Homepage hoàn chỉnh
3. Trang Chọn gói vườn
4. Trang Cách hoạt động
5. Trang Trải nghiệm số & webcam 24/7
6. Trang Chất lượng & an toàn thực phẩm
7. Form đăng ký tư vấn / onboarding đầu vào

**Lý do:** đây là cụm trang chốt narrative, USP, conversion và quyết định được phần lớn data entities sau này.

## Phase B — Chốt khung portal lõi
1. Portal dashboard
2. Live webcam
3. Tình trạng realtime
4. Nhật ký chăm sóc
5. Lịch gieo trồng & thu hoạch
6. Chất lượng & an toàn thực phẩm
7. Thông báo
8. AI gardener

**Lý do:** đây là phần xác định rõ “khách hàng sẽ dùng gì mỗi ngày”. Nếu chưa chốt cụm này thì thiết kế DB dễ lệch.

## Phase C — Chốt các page hỗ trợ retention / thương mại
1. Album tăng trưởng / timelapse
2. Gói dịch vụ của tôi
3. Thanh toán & hóa đơn
4. Hỗ trợ & sự cố
5. Thiết bị / camera / cảm biến
6. Hồ sơ gia đình
7. Cài đặt chia sẻ & riêng tư

## Phase D — Nội dung hỗ trợ marketing scale
1. Review / nhật ký khách hàng
2. Blog / SEO templates
3. Thư viện video/live demo
4. Landing pages theo use case

---

# 9) Những quyết định UI nên owner chốt sớm

1. **Mức độ “điều khiển vườn” thật trong phase 1**
   - chỉ xem + yêu cầu hỗ trợ
   - hay có một số control nhẹ
2. **Tên gọi sản phẩm chính thức**
   - vườn thuê số hóa
   - vườn số cho gia đình
   - khu vườn riêng / garden-as-a-service
3. **Cấu trúc gói chính thức**
   - Mini / Family / Premium mới là placeholder hợp lý để thiết kế trước
4. **Portal có nhấn AI mạnh ngay từ phase 1 không**
   - AI là feature hỗ trợ
   - hay là 1 trong các USP chính trên UI
5. **Chia sẻ cho người thân** có vào phase 1 hay phase 1.5
   - vì nó ảnh hưởng rõ navigation, settings, account model sau này

---

# 10) Kết luận ngắn cho owner

Nếu bám **Mẫu 1 — Vườn Riêng Trong Tầm Tay**, hướng UI mạnh nhất nên là:
- public website bán bằng **cảm giác sở hữu + niềm tin nhìn thấy được**,
- portal giữ khách bằng **thói quen xem vườn mỗi ngày**,
- mọi màn hình đều phải trả lời một câu rất rõ:
  **“Khu vườn này có thật sự là của tôi, và tôi có nhìn thấy / hiểu / kiểm soát được nó không?”**

Đây là hướng cân bằng nhất giữa:
- cảm xúc gia đình,
- độ đáng tin của thực phẩm sạch,
- USP số hóa,
- khả năng triển khai thực tế bằng WordPress + WooCommerce + custom portal plugin.

---

# 11) Deliverables trong file này
- Design system ngắn gọn
- Full page-by-page UI spec cho public website
- Full page-by-page UI spec cho customer portal
- Ghi chú desktop/mobile
- Thứ tự ưu tiên build

Nếu đi tiếp vòng sau, nên làm ngay:
1. **wireframe hi-fi homepage + portal dashboard**, rồi
2. **component inventory cho dev**, rồi
3. mới tới **mapping data requirement / DB planning**.
