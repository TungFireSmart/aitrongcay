# Vườn thuê số hóa — Sitemap + Wireframe v1

## 0) Nguyên tắc thiết kế đã bám

- Đối tượng giai đoạn 1: **hộ gia đình / cá nhân**.
- Trục cảm xúc chính: **cảm giác sở hữu**, không phải “đi thuê một dịch vụ vô danh”.
- Trục giá trị chính:
  - **trải nghiệm số mạnh**,
  - **webcam 24/7**,
  - **kiểm soát chất lượng / an toàn thực phẩm**,
  - **theo dõi minh bạch bằng dữ liệu**.
- Tech constraint:
  - Website public chạy trên **WordPress**,
  - Customer portal là phần đăng nhập gắn với **WordPress + MySQL**,
  - Có lớp **AI agents** hỗ trợ tư vấn, giải thích dữ liệu, nhắc việc, phát hiện bất thường.
- Cách chia sản phẩm số:
  - **Public website** để bán niềm tin + chuyển đổi,
  - **Logged-in portal** để duy trì cảm giác sở hữu và gắn bó hằng ngày.

---

# 1) Sitemap tổng thể

## 1.1. Public website (WordPress)

### A. Trang lõi chuyển đổi
1. **Trang chủ**
2. **Cách hoạt động**
3. **Chọn gói vườn**
   - Gói Mini
   - Gói Family
   - Gói Premium
   - Add-ons / nâng cấp
4. **Trải nghiệm số & webcam 24/7**
5. **Chất lượng & an toàn thực phẩm**
6. **Câu chuyện “vườn của bạn”**
7. **Bảng giá / đăng ký tư vấn**

### B. Trang hỗ trợ quyết định
8. **FAQ**
9. **Review / nhật ký khách hàng**
10. **Thư viện hình ảnh / video / live demo**
11. **Blog / kiến thức trồng sạch tại nhà**
12. **Về chúng tôi**
13. **Liên hệ**

### C. Hệ thống chuyển đổi
14. **Đăng ký / tạo tài khoản**
15. **Đăng nhập**
16. **Onboarding form trước khi nhận vườn**
17. **Trang cảm ơn / xác nhận đăng ký**

---

## 1.2. Customer portal (logged-in)

### A. Dashboard / sở hữu số
1. **Tổng quan vườn của tôi**
2. **Live webcam 24/7**
3. **Tình trạng vườn realtime**
4. **Nhật ký chăm sóc & can thiệp**
5. **Lịch gieo trồng / thu hoạch**
6. **Chất lượng & an toàn thực phẩm**
7. **Album tăng trưởng / timelapse**

### B. Điều khiển / tương tác
8. **Điều khiển vườn**
   - tưới / dinh dưỡng / ánh sáng / lịch tự động (tùy mức độ thực tế triển khai)
9. **Yêu cầu hỗ trợ / xử lý sự cố**
10. **Chat với AI gardener**
11. **Thông báo & cảnh báo**

### C. Quản lý tài khoản / thương mại
12. **Gói dịch vụ của tôi**
13. **Thanh toán / hóa đơn**
14. **Thiết bị / cảm biến / camera**
15. **Hồ sơ gia đình / địa điểm đặt vườn**
16. **Cài đặt quyền riêng tư / chia sẻ cho người thân**

---

## 1.3. AI / hệ thống nền (không nhất thiết lộ hết ra menu)

1. **AI tư vấn chọn gói**
2. **AI giải thích dữ liệu cảm biến**
3. **AI cảnh báo bất thường**
4. **AI trả lời “hôm nay vườn tôi thế nào?”**
5. **AI tóm tắt tuần / tháng**

---

# 2) Wireframe mô tả từng trang chính

> Mục tiêu ở đây là wireframe mức cấu trúc nội dung + hành vi UX, chưa đi vào visual design.

---

## 2.1. Public pages

## 2.1.1. Trang chủ

**Mục tiêu:** chốt 3 thứ rất nhanh: đây là gì, vì sao đáng tin, và vì sao nó cho cảm giác “vườn của tôi”.

**Wireframe:**
1. **Header**
   - Logo
   - Menu: Cách hoạt động / Gói vườn / Trải nghiệm số / An toàn thực phẩm / FAQ
   - CTA nổi bật: `Xem gói vườn` + `Đăng ký tư vấn`
   - Login nhỏ ở góc phải

2. **Hero section**
   - Headline kiểu: `Sở hữu trải nghiệm trồng rau sạch như một khu vườn riêng — dù bạn không cần tự làm tất cả`
   - Subheadline nhấn: webcam 24/7, dữ liệu minh bạch, kiểm soát chất lượng, cảm giác sở hữu
   - 2 CTA:
     - `Chọn gói phù hợp`
     - `Xem demo vườn realtime`
   - Visual: mockup điện thoại + desktop hiển thị dashboard / webcam / tăng trưởng cây

3. **3 giá trị cốt lõi**
   - `Nhìn thấy 24/7`
   - `Theo dõi chất lượng rõ ràng`
   - `Có cảm giác đây là vườn của chính mình`

4. **How it works mini flow**
   - Chọn gói
   - Khởi tạo vườn
   - Theo dõi qua app/portal
   - Thu hoạch / nhận rau / tái gieo trồng

5. **Khối “vườn của bạn trên màn hình”**
   - Ảnh webcam
   - chỉ số môi trường
   - timeline chăm sóc
   - caption: “không chỉ mua rau, mà đang đồng hành cùng một khu vườn riêng”

6. **Khối an toàn thực phẩm / minh bạch**
   - Quy trình chuẩn hóa
   - Nhật ký can thiệp
   - Dữ liệu môi trường
   - Lô thu hoạch / truy xuất

7. **Gói vườn nổi bật**
   - 3 cards gói
   - khác nhau theo số khay, số loại cây, mức tự động, số camera/thiết bị, hỗ trợ AI
   - CTA từng gói

8. **Review / bằng chứng xã hội**
   - ảnh khách / trích lời nhận xét
   - nhật ký thực tế

9. **FAQ rút gọn**

10. **Final CTA**
   - Form ngắn: tên + số điện thoại + nhu cầu
   - nút `Bắt đầu với vườn của tôi`

11. **Footer**
   - điều khoản, chính sách, liên hệ, social

---

## 2.1.2. Cách hoạt động

**Mục tiêu:** giải tỏa sự mơ hồ vì mô hình mới.

**Wireframe:**
1. Hero giải thích ngắn mô hình
2. Sơ đồ 5 bước lớn
   - chọn gói
   - set up vườn tiêu chuẩn
   - kết nối tài khoản portal
   - theo dõi / tương tác từ xa
   - thu hoạch / chăm sóc liên tục
3. Phân biệt rõ:
   - phần nào hệ thống làm
   - phần nào khách theo dõi / quyết định
4. Khối “Bạn kiểm soát được gì?”
   - xem camera
   - xem nhật ký
   - nhận cảnh báo
   - đưa yêu cầu hỗ trợ
   - tinh chỉnh một số chế độ nếu gói cho phép
5. Khối “Bạn không phải lo gì?”
   - kỹ thuật nền
   - vận hành chuẩn
   - xử lý lỗi chuyên môn
6. CTA sang trang gói

---

## 2.1.3. Chọn gói vườn

**Mục tiêu:** giúp cá nhân/hộ gia đình tự định vị nhanh.

**Wireframe:**
1. Hero + bộ lọc nhanh
   - mục tiêu: rau ăn lá / cho con nhỏ / trải nghiệm số / quà tặng gia đình
   - quy mô gia đình: 1-2 người / 3-4 người / 5+ người
2. Bảng 3 gói chính
   - tên gói
   - phù hợp cho ai
   - số lượng khay/module
   - webcam / sensor / AI level
   - báo cáo chất lượng
   - mức hỗ trợ
   - giá từ ... / tháng
   - CTA: `Chọn gói này`
3. So sánh chi tiết
4. Add-ons
   - thêm camera
   - thêm loại cây đặc biệt
   - gói ảnh timelapse
   - gói chia sẻ cho gia đình
5. FAQ về chi phí / cam kết / đổi gói
6. CTA đăng ký

---

## 2.1.4. Trải nghiệm số & webcam 24/7

**Mục tiêu:** bán “wow moment” công nghệ.

**Wireframe:**
1. Hero với khung video / ảnh live
2. Khối tính năng:
   - live webcam
   - snapshot theo mốc
   - timelapse tăng trưởng
   - dashboard cảm biến
   - cảnh báo app / web
   - AI tóm tắt sức khỏe vườn
3. Khối mockup app/portal nhiều màn hình
4. Case thực tế một ngày sử dụng
   - sáng mở app xem cây
   - trưa nhận cảnh báo dinh dưỡng / nhiệt độ
   - tối xem ảnh timelapse / nhật ký
5. CTA: `Xem thử giao diện khách hàng`

---

## 2.1.5. Chất lượng & an toàn thực phẩm

**Mục tiêu:** tạo niềm tin đủ mạnh để trả tiền định kỳ.

**Wireframe:**
1. Hero nhấn “sạch phải nhìn thấy được, không chỉ nghe nói”
2. 4 lớp niềm tin
   - quy trình chuẩn
   - dữ liệu cảm biến
   - nhật ký chăm sóc
   - truy xuất theo lô / đợt thu hoạch
3. Khối “Bạn sẽ xem được gì trong portal?”
   - nhiệt độ / độ ẩm / EC / pH (nếu có)
   - thời điểm can thiệp
   - cảnh báo bất thường
   - lịch sử thu hoạch
4. Khối “Những gì AI giúp giải thích”
   - chỉ số nào đang tốt
   - chỉ số nào cần chú ý
   - vì sao rau đợt này chậm nhanh hơn
5. FAQ về an toàn thực phẩm
6. CTA sang đăng ký

---

## 2.1.6. Câu chuyện “vườn của bạn”

**Mục tiêu:** tăng chiều sâu cảm xúc, tránh mô hình bị hiểu như thuê thiết bị khô khan.

**Wireframe:**
1. Hero storytelling
2. Khối cá nhân hóa
   - đặt tên vườn
   - chọn loại cây ưu thích
   - theo dõi theo mùa vụ
   - chia sẻ với con / bố mẹ / người thân
3. Khối “Gia đình gắn bó với khu vườn như thế nào?”
4. Gallery ảnh trước-sau / quá trình lớn lên
5. CTA tạo tài khoản / chọn gói

---

## 2.1.7. FAQ

**Nhóm câu hỏi nên có:**
- Tôi có thật sự sở hữu gì không?
- Tôi xem webcam bằng cách nào?
- Nếu camera/cảm biến lỗi thì sao?
- Tôi có điều khiển được gì?
- Rau được kiểm soát an toàn ra sao?
- Nếu tôi bận, hệ thống vẫn vận hành chứ?
- Có thể đổi loại cây / đổi gói không?
- Gia đình tôi có thể cùng xem không?

**Wireframe:**
- search box
- category tabs
- accordion questions
- sticky CTA `Chưa rõ? Đăng ký tư vấn`

---

## 2.1.8. Đăng ký / onboarding form

**Mục tiêu:** thu lead chất lượng mà không tạo ma sát quá sớm.

**Wireframe:**
1. Step 1: thông tin cơ bản
   - tên
   - số điện thoại
   - email
2. Step 2: nhu cầu
   - số người trong gia đình
   - mục tiêu chính
   - ngân sách dự kiến
3. Step 3: ưu tiên trải nghiệm
   - muốn thiên về an toàn thực phẩm / trải nghiệm số / quà tặng / cho trẻ nhỏ
4. Step 4: tạo tài khoản mật khẩu hoặc OTP
5. Thank-you screen
   - xác nhận gửi thành công
   - CTA `Khám phá giao diện portal demo`

---

# 2.2. Logged-in customer portal

## 2.2.1. Tổng quan vườn của tôi (Dashboard)

**Mục tiêu:** đây phải là “trang neo cảm xúc” mạnh nhất của toàn hệ thống.

**Wireframe:**
1. **Top bar**
   - logo / tên vườn của khách (`Vườn Nhà Linh`, `Vườn Bé Na`...)
   - quick switch nếu nhiều vườn
   - chuông thông báo
   - avatar

2. **Hero dashboard card**
   - ảnh live mới nhất hoặc webcam preview
   - trạng thái tổng quát: `Ổn định`, `Cần chú ý`, `Đang chăm sóc`, `Sắp thu hoạch`
   - AI summary 1-2 câu: “Hôm nay vườn phát triển tốt, xà lách lô 02 còn khoảng 4 ngày tới thu hoạch.”

3. **4 chỉ số chính**
   - sức khỏe tổng quan
   - môi trường
   - lịch can thiệp gần nhất
   - dự báo thu hoạch

4. **Timeline hôm nay / 24h qua**
   - ai/chăm sóc viên đã làm gì
   - hệ thống tự động đã can thiệp gì
   - có cảnh báo nào

5. **Khối hành động nhanh**
   - xem webcam
   - xem an toàn thực phẩm
   - chat AI
   - yêu cầu hỗ trợ

6. **Khối lô cây / module đang hoạt động**
   - từng card cây/khay
   - ảnh nhỏ
   - tuổi cây
   - tình trạng
   - ETA thu hoạch

7. **Footer portal / nav dưới mobile**
   - Dashboard / Camera / Nhật ký / Chất lượng / AI

---

## 2.2.2. Live webcam 24/7

**Mục tiêu:** đây là proof-of-ownership mạnh nhất.

**Wireframe:**
1. Video player live lớn
2. Chọn camera / góc nhìn
3. Thanh mốc thời gian snapshot gần đây
4. Nút:
   - chụp ảnh
   - xem timelapse
   - báo sự cố camera
5. Bên phải / bên dưới:
   - trạng thái camera
   - trạng thái môi trường lúc đang xem
   - note AI: “hiện không thấy dấu hiệu bất thường”
6. Tab phụ:
   - Live
   - Lịch sử ảnh
   - Timelapse

---

## 2.2.3. Tình trạng vườn realtime

**Mục tiêu:** chuyển dữ liệu cảm biến thành thứ người thường hiểu được.

**Wireframe:**
1. Header + trạng thái chung
2. Các card chỉ số
   - nhiệt độ
   - độ ẩm
   - ánh sáng
   - dinh dưỡng / EC
   - pH
   - nước / bồn / bơm (nếu có)
3. Biểu đồ 24h / 7 ngày
4. AI explanation box
   - “chỉ số nào ổn”
   - “chỉ số nào cần để ý”
   - “ảnh hưởng đến cây ra sao”
5. Khối threshold / ngưỡng an toàn dạng dễ hiểu
   - xanh / vàng / đỏ
6. Nút CTA
   - `Hỏi AI về chỉ số này`
   - `Tạo yêu cầu kiểm tra`

---

## 2.2.4. Nhật ký chăm sóc & can thiệp

**Mục tiêu:** tạo độ tin cậy bằng audit trail.

**Wireframe:**
1. Bộ lọc theo ngày / loại sự kiện
2. Timeline dọc
   - gieo trồng
   - kiểm tra định kỳ
   - điều chỉnh dinh dưỡng
   - xử lý bất thường
   - thu hoạch
3. Mỗi event có:
   - timestamp
   - người/hệ thống thực hiện
   - mô tả việc làm
   - ảnh trước/sau nếu có
4. CTA xuất báo cáo / chia sẻ

---

## 2.2.5. Lịch gieo trồng / thu hoạch

**Mục tiêu:** làm khách thấy mình đang có “mùa vụ” thật.

**Wireframe:**
1. Calendar / timeline view
2. Upcoming milestones
   - gieo lứa mới
   - chăm sóc định kỳ
   - ngày dự kiến thu hoạch
3. Khối “đề xuất kế tiếp”
   - nên trồng gì tiếp theo
   - có thể đổi cơ cấu cây không
4. CTA:
   - `Yêu cầu đổi cây`
   - `Tư vấn kế hoạch mới`

---

## 2.2.6. Chất lượng & an toàn thực phẩm

**Mục tiêu:** đây là trang giữ chân và tạo justification chi phí hàng tháng.

**Wireframe:**
1. Summary card
   - trạng thái lô hiện tại
   - mức độ an tâm / trust score nội bộ hiển thị dễ hiểu
2. Truy xuất theo lô thu hoạch
3. Các chứng cứ / nhật ký liên quan
   - dữ liệu môi trường
   - thời điểm can thiệp
   - kiểm tra nội bộ
4. AI explanation block
   - “vì sao lô này đạt trạng thái tốt”
   - “nếu có điểm cần chú ý thì là gì”
5. Download / share report

---

## 2.2.7. Album tăng trưởng / timelapse

**Mục tiêu:** tăng gắn bó cảm xúc, rất hợp với hộ gia đình và trẻ nhỏ.

**Wireframe:**
1. Hero gallery
2. So sánh theo ngày tuần
3. Timelapse video
4. CTA tải ảnh / chia sẻ riêng cho gia đình
5. Khối “khoảnh khắc đáng nhớ”
   - nảy mầm
   - lớn nhanh
   - sắp thu hoạch

---

## 2.2.8. Điều khiển vườn

**Lưu ý:** chỉ mở những control thực sự an toàn; không nên hứa quá sâu nếu vận hành thực tế chưa đủ.

**Wireframe:**
1. Trạng thái chế độ hiện tại
   - Auto / Semi-auto / Manual request
2. Các control khả dụng theo gói
   - bật/tắt lịch ánh sáng đã định nghĩa
   - yêu cầu chu kỳ tưới kiểm tra
   - đổi profile chăm sóc đã được duyệt
3. Mọi control đều có:
   - mô tả tác động
   - mức độ ảnh hưởng
   - xác nhận trước khi gửi
4. Event log ngay bên dưới
5. Nếu control nhạy cảm:
   - thay bằng `Gửi yêu cầu tới đội vận hành`

---

## 2.2.9. Chat với AI gardener

**Mục tiêu:** biến dữ liệu kỹ thuật thành trợ lý gần gũi, dễ dùng mỗi ngày.

**Wireframe:**
1. Chat window
2. Prompt gợi ý
   - `Hôm nay vườn tôi thế nào?`
   - `Bao giờ thu hoạch được?`
   - `Chỉ số nào đang bất thường?`
   - `Tuần này có gì cần chú ý?`
3. AI trả lời có cấu trúc:
   - tóm tắt
   - dữ liệu liên quan
   - khuyến nghị
   - nếu cần escalte sang hỗ trợ thật
4. Deep links sang camera / chỉ số / nhật ký liên quan

---

## 2.2.10. Thông báo & cảnh báo

**Wireframe:**
1. Danh sách thông báo theo mức độ
   - thông tin
   - cần chú ý
   - khẩn
2. Bộ lọc theo loại
3. Mỗi item có CTA hành động
   - xem camera
   - xem chỉ số
   - chat AI
   - tạo ticket hỗ trợ

---

## 2.2.11. Yêu cầu hỗ trợ / xử lý sự cố

**Wireframe:**
1. Nút tạo yêu cầu mới
2. Chọn vấn đề
   - camera
   - dữ liệu cảm biến
   - cây phát triển chậm
   - đổi loại cây
   - thanh toán
3. Upload ảnh / đính kèm
4. Mức ưu tiên
5. Lịch sử ticket và SLA dự kiến

---

## 2.2.12. Gói dịch vụ của tôi / thanh toán

**Wireframe:**
1. Gói hiện tại
2. Quyền lợi đi kèm
3. Chu kỳ thanh toán
4. Hóa đơn gần đây
5. CTA nâng cấp / thêm add-on
6. CTA gia hạn / đổi phương thức thanh toán

---

## 2.2.13. Thiết bị / cảm biến / camera

**Wireframe:**
1. Danh sách thiết bị
2. Trạng thái online/offline
3. Lần sync cuối
4. Mức pin / nguồn / kết nối nếu có
5. CTA báo lỗi

---

## 2.2.14. Hồ sơ gia đình / chia sẻ cho người thân

**Mục tiêu:** tăng adoption trong gia đình.

**Wireframe:**
1. Tên chủ tài khoản
2. Thành viên được mời xem
3. Quyền xem gì
   - chỉ xem camera
   - xem toàn bộ dashboard
   - nhận thông báo
4. CTA mời người thân
5. Cài đặt quyền riêng tư

---

# 3) Flow điều hướng cốt lõi

## Flow 1 — Khách mới từ quảng cáo / social tới đăng ký
1. Vào **Trang chủ**
2. Click `Xem gói vườn`
3. Sang **Chọn gói vườn**
4. Xem thêm **Trải nghiệm số** hoặc **An toàn thực phẩm** để tăng niềm tin
5. Click `Đăng ký tư vấn` / `Bắt đầu với vườn của tôi`
6. Điền **onboarding form**
7. Tới **trang cảm ơn**
8. Tạo tài khoản / được gửi link đăng nhập portal sau khi kích hoạt

## Flow 2 — Khách cần kiểm chứng niềm tin trước khi mua
1. Vào **Trang chủ**
2. Sang **Chất lượng & an toàn thực phẩm**
3. Xem **Cách hoạt động**
4. Xem **FAQ**
5. Xem **demo webcam / trải nghiệm số**
6. Quay lại **Chọn gói vườn**
7. Đăng ký

## Flow 3 — Người dùng đã mua, vào portal hằng ngày
1. Login
2. Vào **Dashboard**
3. Đọc AI summary + trạng thái chung
4. Click nhanh sang **Live webcam** hoặc **Tình trạng realtime**
5. Nếu có gì lạ:
   - hỏi **AI gardener**
   - hoặc tạo **Yêu cầu hỗ trợ**
6. Quay lại dashboard / xem lịch thu hoạch

## Flow 4 — Người dùng nhận cảnh báo bất thường
1. Nhận push/email/web notification
2. Mở thẳng **Thông báo & cảnh báo**
3. Click xem chi tiết
4. Deep link tới:
   - **Webcam** nếu cần xem bằng mắt
   - **Tình trạng realtime** nếu là vấn đề chỉ số
   - **Nhật ký chăm sóc** nếu cần bối cảnh
5. Hỏi **AI gardener**
6. Nếu chưa yên tâm, tạo **ticket hỗ trợ**

## Flow 5 — Người dùng theo dõi chu kỳ thu hoạch
1. Dashboard
2. Click card `Sắp thu hoạch`
3. Mở **Lịch gieo trồng / thu hoạch**
4. Xem ETA + lô cây
5. Mở **Chất lượng & an toàn thực phẩm** của lô đó
6. Quyết định nhận / đổi kế hoạch / hỏi hỗ trợ

---

# 4) Ghi chú UX quan trọng

## 4.1. Cảm giác sở hữu phải được ưu tiên hơn cảm giác “đi thuê”
- Dùng ngôn ngữ như:
  - `Vườn của tôi`
  - `Lô cây của bạn`
  - `Nhật ký vườn`
  - `Mùa vụ của gia đình bạn`
- Tránh lặp lại từ quá dịch vụ như “gói thuê”, “thiết bị thuê”, “tài sản hệ thống”.
- Cho phép **đặt tên vườn**, thêm ảnh đại diện, cá nhân hóa trải nghiệm.

## 4.2. Webcam là feature chiến lược, không phải phụ kiện
- Camera phải luôn có lối vào cực ngắn từ dashboard.
- Trên mobile nên có tab riêng `Camera`.
- Mọi cảnh báo quan trọng nên có deep link sang camera nếu hợp lý.

## 4.3. Dữ liệu phải được giải thích, không chỉ hiển thị
- Người dùng gia đình không muốn đọc dashboard quá kỹ thuật.
- Mỗi khối dữ liệu nên có lớp diễn giải:
  - `Đang ổn`
  - `Cần chú ý`
  - `Ảnh hưởng gì đến cây`
  - `Có cần làm gì không`
- Đây là chỗ **AI agents** tạo khác biệt rất mạnh.

## 4.4. Niềm tin an toàn thực phẩm cần “bằng chứng nhìn thấy được”
- Không chỉ là câu chữ marketing.
- Luôn gắn:
  - dữ liệu cảm biến,
  - nhật ký chăm sóc,
  - webcam,
  - truy xuất lô,
  - báo cáo tóm tắt dễ hiểu.

## 4.5. Mobile-first cho portal
- Hành vi chính sẽ là mở điện thoại để xem nhanh.
- Dashboard mobile cần ưu tiên 5 thứ đầu:
  1. ảnh/live preview,
  2. trạng thái chung,
  3. AI summary,
  4. cảnh báo,
  5. CTA Camera / Chất lượng / AI.

## 4.6. Không hứa khả năng điều khiển quá mức khi vận hành chưa sẵn
- Nên chia rõ:
  - `Xem và theo dõi` (must-have ngay từ đầu)
  - `Yêu cầu can thiệp` (an toàn, khả thi)
  - `Điều khiển trực tiếp` (chỉ mở phần nào đủ kiểm soát rủi ro)
- UX phải trung thực để tránh vỡ kỳ vọng.

## 4.7. AI nên đóng vai “người phiên dịch + người đồng hành”
- Không biến AI thành màn hình phức tạp.
- AI nên giúp:
  - tóm tắt,
  - giải thích,
  - nhắc việc,
  - phát hiện bất thường,
  - gợi ý bước tiếp theo.
- Tất cả câu trả lời AI nên deep-link sang dữ liệu gốc để tăng trust.

## 4.8. WordPress IA nên tách rõ public content vs app shell
- Public site do WordPress quản lý page/post/landing/SEO.
- Portal nên có app-shell rõ ràng sau login, dùng chung user/auth với WordPress nhưng không bị cảm giác như blog admin.
- Menu public và menu portal phải là **2 hệ điều hướng khác nhau**.

---

# 5) Đề xuất ưu tiên triển khai UI sau owner duyệt

## Public MVP pages trước
1. Trang chủ
2. Cách hoạt động
3. Chọn gói vườn
4. Trải nghiệm số & webcam
5. Chất lượng & an toàn thực phẩm
6. FAQ
7. Đăng ký / onboarding

## Portal MVP pages trước
1. Dashboard
2. Live webcam
3. Tình trạng realtime
4. Nhật ký chăm sóc
5. Chất lượng & an toàn thực phẩm
6. AI gardener
7. Thông báo

---

# 6) Chốt định hướng ngắn gọn cho owner

Nếu chỉ giữ một câu để bám trong toàn bộ thiết kế:

**Đây không phải website bán rau sạch; đây là một trải nghiệm “sở hữu khu vườn sạch có thể nhìn thấy, theo dõi và tin tưởng bằng dữ liệu”.**
