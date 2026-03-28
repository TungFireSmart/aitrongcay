# WordPress mapping notes

Mục tiêu của thư mục này là giữ một lớp tham chiếu nhẹ để khi chuyển từ static site sang WordPress không phải tách lại từ đầu.

## Gợi ý mapping

- `theme/partials/public-header.html` -> `header.php` hoặc `template-parts/site/header-public.php`
- `theme/partials/public-footer.html` -> `footer.php` hoặc `template-parts/site/footer-public.php`
- `index.html` -> front page (`front-page.php`)
- `how-it-works.html` -> page template / slug `cach-hoat-dong`
- `packages.html` -> landing page / service page / CPT archive nếu sau này có nhiều gói
- `digital-experience.html` -> feature landing page / block collection
- `food-safety.html` -> trust page / pillar content
- `your-garden-story.html` -> brand story page
- `faq.html` -> page template có accordion block
- `signup/register.html` -> contact/register page tích hợp form plugin
- `auth/login.html` -> portal-entry page hoặc external app handoff page

## Reusable content groups đã được làm rõ

1. Header public
2. Footer public
3. Hero + CTA band
4. Trust / credibility sections
5. Package comparison sections
6. FAQ accordion
7. Registration CTA / form block

## Ghi chú triển khai

- JS hiện tại đã hỗ trợ active nav theo `pathname` và mobile menu, có thể tái dùng logic khi render trong theme.
- CSS đã tách rõ các pattern như `footer-top`, `info-band`, `value-strip`, `pricing-grid`, phù hợp để đưa thành block/pattern.
- Nếu sang WordPress, ưu tiên biến các vùng sau thành editable fields hoặc ACF/Gutenberg patterns:
  - Hero copy
  - KPI / metric cards
  - Gói vườn
  - FAQ items
  - Footer company info
