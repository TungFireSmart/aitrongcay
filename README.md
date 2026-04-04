# Ai trồng cây

Website cho dự án **Ai trồng cây**.

## Cách mở website trên máy local

### Cách 1: chạy local server

```bash
npm start
```

Sau đó mở:

- `http://127.0.0.1:4173/`

Hoặc:

- `http://127.0.0.1:4173/index.html`

### Cách 2: mở trực tiếp file HTML

- `index.html`

## Các trang chính

- `index.html`
- `how-it-works.html`
- `packages.html`
- `food-safety.html`
- `your-garden-story.html`
- `faq.html`
- `signup/register.html`
- `signup/onboarding.html`
- `auth/login.html`
- `portal/dashboard.html`
- `portal/webcam.html`
- `portal/status.html`
- `portal/care-log.html`
- `portal/quality-safety.html`
- `portal/ai-gardener.html`
- `portal/tools-warehouse.html`

## WordPress foundation (checkpoint 3)

Repo đã có sẵn theme WordPress nền tại:

- `wp-content/themes/aitrongcay/`

Các điểm đã chuẩn bị:

- theme WordPress tối thiểu: `style.css`, `functions.php`, `header.php`, `footer.php`, `front-page.php`, `page.php`, `index.php`
- asset đã được copy vào theme để chạy độc lập với repo tĩnh
- homepage đã có các section WordPress-ready theo đúng brand hiện tại
- map slug cho các trang public + portal
- virtual page fallback để demo chạy ngay cả khi chưa seed page thật trong WordPress
- nếu đã tạo page/menu native trong WordPress, theme sẽ ưu tiên dùng page/menu thật thay vì fallback hardcoded
- đã có starter content native đầu tiên cho các page quan trọng
- form `Đăng ký tư vấn` đã có bản WordPress-native lưu lead nội bộ trong admin qua custom post type `Leads tư vấn`
- tài liệu local-first để bật WordPress lần đầu

Tài liệu quan trọng:

- `wp-content/themes/aitrongcay/docs/page-map.md`
- `wp-content/themes/aitrongcay/docs/local-wordpress-quickstart.md`
- `wp-content/themes/aitrongcay/docs/local-launch-checklist.md`
- `wp-content/themes/aitrongcay/docs/content-migration-plan.md`
- `wp-content/themes/aitrongcay/docs/handover-huong-1.md`
- `wp-content/themes/aitrongcay/docs/deploy-smoke-test.md`
- `wp-content/themes/aitrongcay/docs/release-notes-0.3.2.md`
- `wp-content/themes/aitrongcay/docs/seed-pages.json`
- `wp-content/themes/aitrongcay/docs/starter-content/README.md`
- `wp-content/themes/aitrongcay/docs/wp-cli-seed.md`

## Thông tin liên hệ

**CÔNG TY CỔ PHẦN NGHIÊN CỨU GIẢI PHÁP VÀ PHÁT TRIỂN CÔNG NGHỆ XANH**

- Địa chỉ: Số 180A, đường Âu Cơ, Phường Tứ Liên, Quận Tây Hồ, Thành phố Hà Nội, Việt Nam
- Điện thoại: 0983.660.988 – 0876.666.114
