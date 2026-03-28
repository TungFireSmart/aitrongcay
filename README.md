# aitrongcay

Prototype website tĩnh cho dự án **vườn thuê số hóa**.

Mục tiêu checkpoint hiện tại: người pull repo về là biết **mở gì trước**, **xem gì để review**, và cảm nhận dự án đang tiến gần hơn tới một website có thể triển khai thật.

## Mở dự án theo cách dễ nhất

### Cách 1 — khuyến nghị: chạy local server

Từ thư mục repo:

```bash
npm start
```

Sau đó mở:

- `http://127.0.0.1:4173/start-here.html` → trang dẫn review
- `http://127.0.0.1:4173/index.html` → homepage public
- `http://127.0.0.1:4173/portal/dashboard.html` → portal demo

> Không cần cài framework hay build. Đây là static site, đã có sẵn server nhẹ bằng Node trong repo.

### Cách 2 — mở trực tiếp file HTML

Nếu chưa muốn chạy server, vẫn có thể mở trực tiếp:

- `start-here.html`
- `index.html`

Tuy nhiên cách chạy local server vẫn nên dùng hơn vì trải nghiệm giống website thật hơn.

---

## Nên review theo thứ tự nào

### 1) `start-here.html`
Trang định hướng review. Dành cho người mở repo lần đầu.

### 2) `index.html`
Homepage public: định vị mô hình, tạo cảm giác sở hữu, dẫn vào các phần quan trọng.

### 3) `how-it-works.html`
Giải thích mô hình theo ngôn ngữ dễ hiểu, giảm cảm giác “ý tưởng lạ”.

### 4) `packages.html`
Xem cấu trúc gói dịch vụ và mức dễ thương mại hóa.

### 5) `digital-experience.html`
Phần làm rõ yếu tố camera / portal / trải nghiệm số.

### 6) `portal/dashboard.html`
Trang quan trọng nhất để cảm nhận dự án đã gần “website chạy được” hơn: có dashboard, webcam, care log, quality và AI gardener.

### 7) `signup/register.html` + `signup/onboarding.html`
Xem hành trình khách đi từ quan tâm sang để lại thông tin và kích hoạt.

---

## Cấu trúc thư mục chính

```text
aitrongcay/
├── start-here.html              # điểm vào để review
├── index.html                   # homepage public
├── how-it-works.html
├── packages.html
├── digital-experience.html
├── food-safety.html
├── your-garden-story.html
├── faq.html
├── auth/
├── signup/
├── portal/
├── assets/
│   ├── css/styles.css
│   └── js/main.js
├── scripts/
│   └── serve.mjs                # local static server
└── package.json
```

---

## Những gì đã được làm để repo dễ review hơn

- Thêm `start-here.html` làm entrypoint rõ ràng cho người không chuyên IT.
- Thêm local server nhẹ bằng Node để mở site như website thật, không cần framework.
- README được tổ chức lại theo hướng:
  - mở gì trước
  - review gì theo thứ tự nào
  - từng trang dùng để đánh giá điều gì
- Homepage và portal vẫn giữ vai trò là hai điểm nhấn chính để thể hiện:
  - cảm giác sở hữu khu vườn
  - trải nghiệm số có camera / care log / AI summary
  - website nhìn giống một sản phẩm lifestyle-tech hơn là bản demo rời rạc

---

## Các khu chính trong prototype

### Public pages
- `index.html`
- `how-it-works.html`
- `packages.html`
- `digital-experience.html`
- `food-safety.html`
- `your-garden-story.html`
- `faq.html`

### Signup / auth
- `signup/register.html`
- `signup/onboarding.html`
- `auth/login.html`

### Portal demo
- `portal/dashboard.html`
- `portal/webcam.html`
- `portal/status.html`
- `portal/care-log.html`
- `portal/quality-safety.html`
- `portal/ai-gardener.html`

---

## Bản chất hiện tại của dự án

- Đây vẫn là **prototype HTML/CSS/JS tĩnh**.
- Các form và tương tác đang ở mức demo UX.
- Mục tiêu của repo này là giúp review nhanh hướng website, trải nghiệm khách hàng và cảm giác sản phẩm.
- Repo không giải thích phần kỹ thuật sâu; ưu tiên để người xem tập trung vào website, flow và trải nghiệm.
