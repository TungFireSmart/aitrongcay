# Vườn thuê số hóa — Bước 3: Data Architecture + MySQL + WordPress Custom Tables + AI Agents

Phiên bản này là bản owner-ready cho bước 3, đi sau UI/UX + prototype đã chốt.

Bám chặt các quyết định đã có:
- Frontend public: **WordPress + Blocksy**
- Commerce: **WooCommerce**
- Logged-in experience: **custom portal plugin** trong WordPress
- Đối tượng phase 1: **hộ gia đình / cá nhân**
- Giá trị cốt lõi phải phản ánh trong data model:
  - **cảm giác sở hữu**
  - **webcam 24/7**
  - **care log minh bạch**
  - **quality & food safety**
  - **AI gardener**
  - **family sharing**

Mục tiêu của kiến trúc dữ liệu không phải chỉ để “lưu dữ liệu kỹ thuật”, mà để phục vụ đúng trải nghiệm đã chốt:
**khách mở portal lên và cảm thấy đây là khu vườn của gia đình mình, có thể nhìn thấy, hiểu được, và tin được.**

---

## 1) Kết luận chốt nhanh cho owner

### 1.1. Nên dùng 3 lớp dữ liệu song song
1. **WordPress core tables**
   - cho user, content, media, capability, settings cơ bản.
2. **WooCommerce tables**
   - cho plan/gói, add-ons, checkout, order, payment, subscription direction.
3. **Custom portal tables trong cùng MySQL**
   - cho toàn bộ dữ liệu domain riêng của sản phẩm: garden, zone, crop cycle, camera stream, sensor readings, care logs, harvest batch, traceability, alerts, AI memory, family access.

### 1.2. Không nên nhồi domain data vào wp_postmeta / woocommerce order meta
Có thể dùng meta cho vài field nhỏ lúc MVP rất sớm, nhưng với mô hình này thì **domain data sẽ lớn, nhiều quan hệ, nhiều timeline, nhiều filter realtime**, nên nếu nhồi vào meta sẽ rất nhanh vỡ ở 4 điểm:
- query chậm,
- khó audit,
- khó scale timeline / sensor / camera,
- khó làm AI retrieval đáng tin.

### 1.3. Hướng đúng
- **WP/Woo giữ phần CMS + commerce + account base**
- **custom plugin sở hữu domain model riêng bằng custom tables**
- plugin expose data qua:
  - WP REST API / custom REST endpoints
  - internal service layer cho portal
  - event hooks cho AI agents và IoT ingestion

---

## 2) Kiến trúc dữ liệu tổng thể

## 2.1. Các domain chính
Đề xuất chia toàn hệ thống thành 10 cụm dữ liệu:

1. **Identity & Access**
   - user chính, thành viên gia đình, vai trò, quyền xem.
2. **Commerce & Entitlement**
   - gói vườn, add-ons, order, subscription, quyền lợi theo gói.
3. **Garden Ownership**
   - khu vườn nào thuộc ai, tên gọi khu vườn, trạng thái hoạt động, season hiện tại.
4. **Physical Structure**
   - garden / zone / bed / module / crop slot.
5. **Crop Lifecycle**
   - giống cây, vụ trồng, mốc sinh trưởng, dự kiến thu hoạch.
6. **Realtime & Media**
   - camera, stream endpoint, snapshots, timelapse asset, sensor devices, readings.
7. **Operations & Care Logs**
   - nhật ký chăm sóc, can thiệp hệ thống, yêu cầu điều chỉnh, incident.
8. **Quality & Food Safety**
   - batch thu hoạch, kiểm tra chất lượng, traceability, tài liệu/chứng cứ.
9. **Notifications & Support**
   - alert, ticket, customer support, delivery state.
10. **AI Layer**
   - AI summary, AI conversation, insight, anomaly explanation, memory references.

## 2.2. Cách nhìn dữ liệu theo trải nghiệm UI
Đây là cách map ngược từ từng màn portal về data backbone:

- **Dashboard** cần:
  - garden profile
  - camera latest frame
  - health summary
  - harvest countdown
  - latest care logs
  - active alerts
  - AI daily summary

- **Live webcam** cần:
  - camera registry
  - stream auth token / endpoint
  - status online-offline
  - snapshots timeline
  - timelapse group

- **Status realtime** cần:
  - sensor devices
  - sensor readings time-series
  - threshold rules
  - interpreted status summary

- **Care log** cần:
  - operation events
  - actor type: human / system / AI suggested / customer request
  - attachments / photos
  - per-zone / per-cycle relation

- **Quality & safety** cần:
  - crop cycle
  - harvest batch
  - inspection / checklist
  - environment evidence
  - care evidence
  - downloadable trace report

- **AI gardener** cần:
  - structured garden state
  - retrieval từ logs / readings / batches / camera summaries
  - answer history
  - prompt context per garden / per family

- **Family sharing** cần:
  - garden memberships
  - granular permissions
  - invitation workflow
  - audit xem ai đã xem / nhận thông báo gì nếu cần sau này

---

## 3) Phân tách WordPress core / WooCommerce / custom tables

## 3.1. WordPress core nên giữ gì

### Dùng thẳng WordPress core cho:
- `wp_users`
  - tài khoản owner chính và thành viên gia đình.
- `wp_usermeta`
  - vài cài đặt nhẹ: avatar URL, onboarding flags, preferred language, notification preference nhẹ.
- `wp_posts` / `wp_postmeta`
  - page content, FAQ, blog, landing page, media references ở mức CMS.
- `wp_terms` / taxonomy
  - category blog, category content marketing.
- WP media library
  - ảnh bài viết, asset marketing, vài file report export nếu muốn quản lý qua media.

### Không nên dùng WP core cho:
- sensor readings
- camera snapshots timeline lớn
- care log timeline chính
- harvest traceability
- AI conversation state domain-specific
- fine-grained family permissions theo garden

Lý do: đây là dữ liệu có volume lớn, nhiều query quan hệ và cần index riêng.

---

## 3.2. WooCommerce nên giữ gì

### Dùng WooCommerce cho:
- Product catalog:
  - gói Mini / Family / Premium
  - add-ons như camera nâng cao, album timelapse, family sharing plus, ưu tiên hỗ trợ...
- Cart / checkout
- Order
- Payment records cơ bản
- Coupon / khuyến mãi nếu cần
- Subscription direction (qua extension hoặc custom handling nhẹ)

### Dữ liệu nên gắn với Woo nhưng không để Woo quản hết logic
Woo chỉ nên là nơi ghi nhận giao dịch. Quyền dùng sản phẩm thực tế nên được chuyển sang **entitlement tables** của plugin:
- order trả tiền thành công -> tạo / cập nhật garden subscription entitlement
- add-on order -> bật capability trong portal

### Không nên để Woo trực tiếp quản:
- lifecycle garden
- camera access policy
- crop cycle
- quality trace records
- AI entitlement logic chi tiết

---

## 3.3. Custom tables nên giữ gì
Toàn bộ dữ liệu “vườn thật + sở hữu số + vận hành + AI” nên nằm trong custom tables có prefix riêng, ví dụ:
- `wp_gdn_*` hoặc `wp_garden_*`

Khuyến nghị chọn prefix ngắn, ví dụ `wp_gdn_`, để câu query và migration gọn hơn.

---

## 4) Đề xuất schema bảng chính và mối quan hệ

Dưới đây là schema ở mức thiết kế sản phẩm/data model, chưa phải DDL cuối.

---

## 4.1. Identity / ownership / family access

### 4.1.1. `wp_gdn_households`
Đại diện cho **đơn vị sở hữu cảm xúc** trong sản phẩm: gia đình / hộ / chủ tài khoản.

Trường chính:
- `id` PK
- `primary_wp_user_id` FK -> `wp_users.ID`
- `display_name` — ví dụ: `Vườn nhà Linh`, `Gia đình chị Mai`
- `household_type` — `individual`, `family`
- `status` — `lead`, `active`, `paused`, `cancelled`
- `timezone`
- `created_at`, `updated_at`

Vai trò UX:
- giúp không biến trải nghiệm thành “một user kỹ thuật”, mà là một household có tính cá nhân/gia đình.

### 4.1.2. `wp_gdn_household_members`
Map user vào household.

Trường chính:
- `id`
- `household_id`
- `wp_user_id`
- `role` — `owner`, `family_admin`, `viewer`, `child_viewer`, `support_viewer`
- `invited_by_user_id`
- `invite_status` — `pending`, `accepted`, `revoked`
- `can_view_camera`
- `can_view_logs`
- `can_view_quality`
- `can_chat_ai`
- `can_receive_alerts`
- `created_at`, `updated_at`

### 4.1.3. `wp_gdn_gardens`
Đơn vị “khu vườn số” hiển thị trong portal.

Trường chính:
- `id`
- `household_id`
- `garden_code` unique
- `garden_name`
- `plan_code`
- `status` — `provisioning`, `active`, `maintenance`, `paused`, `retired`
- `current_season_id` nullable
- `location_label` — tên farm/zone nội bộ nếu cần show nhẹ
- `start_date`
- `activated_at`
- `created_at`, `updated_at`

Một household có thể có nhiều gardens về sau.

---

## 4.2. Commerce / entitlement bridge

### 4.2.1. `wp_gdn_plan_catalog`
Bảng domain mirror của gói dịch vụ, không thay Woo, mà giúp portal đọc logic gói sạch hơn.

Trường chính:
- `id`
- `woo_product_id`
- `plan_code` unique
- `plan_name`
- `tier` — `mini`, `family`, `premium`
- `camera_count_limit`
- `ai_level`
- `family_member_limit`
- `sensor_pack_level`
- `supports_timelapse`
- `supports_direct_controls`
- `supports_quality_reports`
- `created_at`, `updated_at`

### 4.2.2. `wp_gdn_subscriptions`
Entitlement thật cho portal.

Trường chính:
- `id`
- `household_id`
- `garden_id`
- `woo_order_id`
- `woo_subscription_id` nullable
- `plan_catalog_id`
- `status` — `pending`, `active`, `grace_period`, `paused`, `expired`, `cancelled`
- `starts_at`, `ends_at`, `renewal_at`
- `billing_cycle`
- `created_at`, `updated_at`

### 4.2.3. `wp_gdn_subscription_features`
Lưu add-ons hoặc override capability theo từng subscription.

Trường chính:
- `id`
- `subscription_id`
- `feature_code` — `extra_camera`, `timelapse_plus`, `priority_support`, `extra_family_member`
- `feature_value`
- `status`
- `starts_at`, `ends_at`

---

## 4.3. Physical garden structure

### 4.3.1. `wp_gdn_garden_zones`
Một vườn có thể có nhiều zone/module để sau này scale mà không phải đổi model.

Trường chính:
- `id`
- `garden_id`
- `zone_code`
- `zone_name`
- `zone_type` — `leafy_bed`, `herb_rack`, `fruiting_module`, `nursery`
- `display_order`
- `status`

### 4.3.2. `wp_gdn_crop_slots`
Các vị trí trồng logic trong zone.

Trường chính:
- `id`
- `zone_id`
- `slot_code`
- `slot_label`
- `capacity`
- `status`

Lý do có slot:
- về UI có thể hiển thị “khay A”, “giàn 2”, “module rau thơm”.
- về data dễ gắn cycle / care / sensor context.

---

## 4.4. Crop lifecycle

### 4.4.1. `wp_gdn_crop_catalog`
Danh mục giống/cây trồng.

Trường chính:
- `id`
- `crop_code`
- `crop_name`
- `crop_family`
- `default_cycle_days`
- `edible_part`
- `care_profile_code`
- `is_active`

### 4.4.2. `wp_gdn_crop_cycles`
Trái tim của bài toán vận hành. Mỗi đợt/vụ trồng là một cycle.

Trường chính:
- `id`
- `garden_id`
- `zone_id`
- `slot_id` nullable
- `crop_catalog_id`
- `cycle_code` unique
- `season_label`
- `status` — `planned`, `seeded`, `growing`, `ready_for_harvest`, `harvested`, `closed`, `issue`
- `seeded_at`
- `germinated_at` nullable
- `expected_harvest_at`
- `actual_harvest_at` nullable
- `health_status` — `excellent`, `good`, `watch`, `risk`
- `health_score` decimal nullable
- `created_at`, `updated_at`

### 4.4.3. `wp_gdn_cycle_milestones`
Để render timeline “ngày 1, ngày 5, ngày 12...” cực tốt cho portal.

Trường chính:
- `id`
- `cycle_id`
- `milestone_type` — `seeded`, `sprout`, `first_leaf`, `mid_growth`, `pre_harvest`, `harvested`
- `title`
- `description`
- `milestone_at`
- `source_type` — `human`, `system`, `ai_inferred`
- `created_at`

---

## 4.5. Camera / media / timelapse

### 4.5.1. `wp_gdn_cameras`

Trường chính:
- `id`
- `garden_id`
- `zone_id` nullable
- `camera_code`
- `camera_name`
- `stream_provider`
- `stream_url_ref` — nên lưu reference/token, không nhất thiết plain URL trực tiếp
- `snapshot_base_path`
- `status` — `online`, `offline`, `degraded`, `maintenance`
- `last_seen_at`
- `is_primary`
- `created_at`, `updated_at`

### 4.5.2. `wp_gdn_camera_snapshots`
Không nên lưu binary trực tiếp trong MySQL. Chỉ lưu metadata + storage key.

Trường chính:
- `id`
- `camera_id`
- `garden_id`
- `captured_at`
- `storage_disk` — local / s3 / cloudflare / object store
- `storage_key`
- `thumbnail_key`
- `source_type` — `scheduled`, `manual`, `incident`, `milestone`
- `ai_vision_summary` nullable
- `created_at`

### 4.5.3. `wp_gdn_timelapse_assets`

Trường chính:
- `id`
- `garden_id`
- `cycle_id` nullable
- `camera_id`
- `period_type` — `daily`, `weekly`, `cycle`
- `period_start`, `period_end`
- `storage_key`
- `thumbnail_key`
- `status`
- `created_at`

---

## 4.6. Sensor devices và readings

### 4.6.1. `wp_gdn_devices`
Thiết bị vật lý.

Trường chính:
- `id`
- `garden_id`
- `zone_id` nullable
- `device_code`
- `device_type` — `sensor_hub`, `temp_humidity`, `ec_sensor`, `ph_sensor`, `light_sensor`, `pump_controller`, `camera_gateway`
- `manufacturer`
- `firmware_version`
- `connectivity_status`
- `last_seen_at`
- `created_at`, `updated_at`

### 4.6.2. `wp_gdn_sensor_readings`
Đây là bảng volume lớn nhất, cần index kỹ.

Trường chính:
- `id` big PK
- `garden_id`
- `zone_id` nullable
- `device_id`
- `reading_type` — `temperature`, `humidity`, `light`, `ec`, `ph`, `water_level`, `co2`
- `reading_value` decimal
- `reading_unit`
- `quality_flag` — `ok`, `estimated`, `suspect`, `missing_backfill`
- `recorded_at`
- `ingested_at`

Index khuyến nghị:
- `(garden_id, reading_type, recorded_at)`
- `(device_id, recorded_at)`
- partition theo tháng nếu volume tăng mạnh.

### 4.6.3. `wp_gdn_sensor_daily_rollups`
Để portal load nhanh dashboard mà không query raw data quá nhiều.

Trường chính:
- `id`
- `garden_id`
- `zone_id` nullable
- `reading_type`
- `rollup_date`
- `min_value`, `max_value`, `avg_value`
- `samples_count`
- `status_label`
- `generated_at`

---

## 4.7. Care logs / operations / intervention

### 4.7.1. `wp_gdn_care_events`
Bảng sống còn để tạo niềm tin minh bạch.

Trường chính:
- `id`
- `garden_id`
- `zone_id` nullable
- `cycle_id` nullable
- `event_type` — `watering`, `nutrient_adjustment`, `pruning`, `inspection`, `cleaning`, `transplant`, `issue_check`, `harvest`, `system_auto_action`, `customer_request`
- `event_title`
- `event_notes`
- `actor_type` — `operator`, `system`, `ai`, `customer`
- `actor_ref_id` nullable
- `severity` — `info`, `important`, `warning`
- `started_at` nullable
- `completed_at`
- `visibility` — `internal_only`, `customer_visible`
- `created_at`, `updated_at`

### 4.7.2. `wp_gdn_care_event_media`

Trường chính:
- `id`
- `care_event_id`
- `media_type` — `image`, `video`, `document`
- `storage_key`
- `caption`
- `created_at`

### 4.7.3. `wp_gdn_control_requests`
Cho phase đầu nếu chưa mở control trực tiếp quá sâu.

Trường chính:
- `id`
- `garden_id`
- `requested_by_wp_user_id`
- `request_type` — `check_watering`, `change_crop_preference`, `camera_check`, `support`
- `request_payload_json`
- `status` — `submitted`, `reviewing`, `approved`, `executed`, `rejected`, `cancelled`
- `submitted_at`, `resolved_at`

---

## 4.8. Quality, harvest, food safety, traceability

### 4.8.1. `wp_gdn_harvest_batches`
Mỗi đợt thu hoạch là một batch có thể truy xuất.

Trường chính:
- `id`
- `garden_id`
- `cycle_id`
- `batch_code` unique
- `harvested_at`
- `quantity_value`
- `quantity_unit`
- `quality_status` — `passed`, `watch`, `hold`, `rejected`
- `released_for_customer_at` nullable
- `created_at`, `updated_at`

### 4.8.2. `wp_gdn_quality_checks`
Checklist / kết quả kiểm tra.

Trường chính:
- `id`
- `garden_id`
- `cycle_id` nullable
- `batch_id` nullable
- `check_type` — `routine`, `pre_harvest`, `post_harvest`, `incident`
- `check_code`
- `performed_by`
- `performed_at`
- `result_status` — `pass`, `warning`, `fail`
- `summary`
- `details_json`
- `created_at`

### 4.8.3. `wp_gdn_traceability_records`
Bản tóm tắt truy xuất sẵn cho portal / export.

Trường chính:
- `id`
- `garden_id`
- `batch_id`
- `trace_code`
- `trace_summary`
- `report_storage_key` nullable
- `published_at`
- `created_at`

### 4.8.4. `wp_gdn_quality_evidence_links`
Liên kết mềm từ batch/check sang readings, events, snapshots.

Trường chính:
- `id`
- `owner_type` — `batch`, `quality_check`, `trace_record`
- `owner_id`
- `evidence_type` — `care_event`, `sensor_rollup`, `sensor_reading`, `snapshot`, `document`
- `evidence_id`
- `label`
- `created_at`

Bảng này rất quan trọng vì giúp AI và UI nói chuyện bằng “evidence graph”, không chỉ text rời.

---

## 4.9. Alerts / notifications / support

### 4.9.1. `wp_gdn_alerts`

Trường chính:
- `id`
- `garden_id`
- `zone_id` nullable
- `alert_type` — `sensor_out_of_range`, `camera_offline`, `growth_delay`, `quality_watch`, `harvest_ready`, `subscription_issue`
- `severity` — `info`, `warning`, `critical`
- `status` — `open`, `acknowledged`, `resolved`, `suppressed`
- `title`
- `message`
- `source_type` — `rule_engine`, `operator`, `ai`
- `source_ref_id` nullable
- `opened_at`, `resolved_at`
- `created_at`, `updated_at`

### 4.9.2. `wp_gdn_notifications`
Notification delivery per user/member.

Trường chính:
- `id`
- `garden_id`
- `household_member_id`
- `alert_id` nullable
- `notification_type` — `in_app`, `email`, `sms`, `zalo`, `push`
- `title`
- `body`
- `status` — `queued`, `sent`, `delivered`, `read`, `failed`
- `sent_at`, `read_at`
- `created_at`

### 4.9.3. `wp_gdn_support_tickets`

Trường chính:
- `id`
- `garden_id`
- `created_by_wp_user_id`
- `ticket_type`
- `priority`
- `subject`
- `description`
- `status`
- `assigned_to` nullable
- `created_at`, `updated_at`, `resolved_at`

---

## 4.10. AI layer

### 4.10.1. `wp_gdn_ai_conversations`
Phiên chat AI gardener.

Trường chính:
- `id`
- `garden_id`
- `household_member_id`
- `session_title`
- `status`
- `created_at`, `updated_at`

### 4.10.2. `wp_gdn_ai_messages`

Trường chính:
- `id`
- `conversation_id`
- `sender_type` — `user`, `ai`, `system`
- `message_text`
- `structured_payload_json` nullable
- `created_at`

### 4.10.3. `wp_gdn_ai_insights`
Các insight đã chuẩn hóa, có thể render ra dashboard.

Trường chính:
- `id`
- `garden_id`
- `cycle_id` nullable
- `insight_type` — `daily_summary`, `weekly_summary`, `harvest_forecast`, `anomaly_explanation`, `care_recommendation`, `camera_summary`
- `title`
- `summary_text`
- `confidence_score`
- `status` — `draft`, `published`, `retracted`
- `valid_from`, `valid_to` nullable
- `created_by_agent`
- `created_at`

### 4.10.4. `wp_gdn_ai_evidence_refs`
Map insight / AI answer với nguồn dữ liệu thật.

Trường chính:
- `id`
- `owner_type` — `ai_message`, `ai_insight`
- `owner_id`
- `ref_type` — `sensor_rollup`, `sensor_reading`, `care_event`, `batch`, `quality_check`, `snapshot`, `alert`
- `ref_id`
- `weight`
- `created_at`

Bảng này cực quan trọng để AI có khả năng “giải thích lại vì sao em nói vậy” và deep-link ra dữ liệu gốc trong portal.

---

## 5) Mối quan hệ cốt lõi cần owner hiểu

Quan hệ xương sống:

- **household** 1 - n **household_members**
- **household** 1 - n **gardens**
- **garden** 1 - n **subscriptions**
- **garden** 1 - n **zones**
- **zone** 1 - n **crop_slots**
- **garden/zone/slot** 1 - n **crop_cycles**
- **garden/zone** 1 - n **cameras**
- **garden/zone** 1 - n **devices**
- **device** 1 - n **sensor_readings**
- **garden/cycle** 1 - n **care_events**
- **cycle** 1 - n **harvest_batches**
- **batch** 1 - n **quality_checks** / **traceability_records**
- **garden** 1 - n **alerts**
- **garden/member** 1 - n **ai_conversations**
- **ai insights / ai answers** n - n **domain evidence** qua bảng refs

Cách hiểu đơn giản:
**Household sở hữu Garden -> Garden có nhiều Cycle, Camera, Device, Care Events, Harvest Batch -> AI chỉ là lớp đọc/giải thích/nhắc việc trên cùng graph dữ liệu đó.**

---

## 6) Data flow giữa website, portal, IoT/camera, care logs, harvest/batches

## 6.1. Flow A — Từ website public tới portal sở hữu

1. Khách vào website WordPress.
2. Xem gói qua page builder / WooCommerce product.
3. Checkout / để lại lead / mua gói.
4. Khi order đạt trạng thái hợp lệ:
   - tạo `household`
   - tạo `garden` ở trạng thái `provisioning`
   - tạo `subscription`
   - gắn owner vào `household_members`
5. Khi vận hành kích hoạt xong:
   - cập nhật `garden.status = active`
   - tạo zone / slot / camera / device records ban đầu
   - gửi onboarding portal

=> Từ đây UI “vườn của tôi” có dữ liệu nền để hiện.

---

## 6.2. Flow B — IoT / sensor ingestion

1. Sensor gateway hoặc integration service gửi readings vào ingestion endpoint.
2. Plugin/service validate:
   - device hợp lệ không
   - timestamp hợp lệ không
   - reading type hợp lệ không
3. Ghi vào `wp_gdn_sensor_readings`.
4. Job nền tạo:
   - rollups ngày / giờ
   - threshold evaluation
   - alert nếu out-of-range
5. Dashboard / status page đọc chủ yếu từ:
   - recent raw readings cho realtime
   - daily/hourly rollups cho chart nhẹ
   - AI insight để diễn giải dễ hiểu

Khuyến nghị:
- ingestion nên đi qua service layer tách khỏi request frontend thông thường.
- về sau có thể dùng queue để tránh nghẽn WordPress request lifecycle.

---

## 6.3. Flow C — Camera / webcam / snapshots / timelapse

1. Camera registry có trong `wp_gdn_cameras`.
2. Stream live có thể đi qua provider riêng, không ép WordPress stream trực tiếp.
3. Scheduler hoặc media worker:
   - chụp snapshot định kỳ
   - upload object storage
   - ghi metadata vào `wp_gdn_camera_snapshots`
4. Batch job tổng hợp snapshots thành `timelapse_assets`.
5. AI vision agent có thể đọc subset snapshots để tạo:
   - daily visual summary
   - growth pattern note
   - camera anomaly detection

Khuyến nghị mạnh:
- video/image file nên ở object storage, MySQL chỉ giữ metadata.

---

## 6.4. Flow D — Care log / vận hành minh bạch

1. Nhân sự vận hành hoặc automation tạo `care_event`.
2. Nếu có ảnh, đính kèm qua `care_event_media`.
3. Event có thể gắn vào `cycle`, `zone`, `garden`.
4. Nếu event quan trọng:
   - sinh notification
   - feed vào AI summary
   - trở thành evidence cho quality / traceability
5. Portal care log page đọc trực tiếp timeline này.

Điểm rất quan trọng về UX:
- chính care log là bằng chứng “có người/hệ thống đang chăm vườn thật”.
- vì vậy event model phải rõ actor, thời gian, loại việc, ghi chú, bằng chứng.

---

## 6.5. Flow E — Harvest / batch / quality / food safety

1. Cycle tới ngày thu hoạch -> tạo `harvest_batch`.
2. Kiểm tra chất lượng -> `quality_checks`.
3. Liên kết evidence từ:
   - care events
   - sensor rollups
   - snapshots
   - docs nội bộ
4. Tạo `traceability_record`.
5. Portal quality page render:
   - batch summary
   - pass/warning state
   - AI explanation
   - download trace report nếu có

Đây là luồng biến “cam kết sạch” thành **traceable evidence system**.

---

## 6.6. Flow F — Family sharing

1. Owner gửi lời mời người thân.
2. Tạo record `household_members` với `invite_status = pending`.
3. Khi accept:
   - member có quyền theo scope
   - notification routing mở theo quyền
4. UI chỉ hiển thị đúng phần được phép xem:
   - camera only
   - dashboard + AI
   - full view

Nên làm access theo member-garden capability, không chỉ role chung chung.

---

## 6.7. Flow G — AI gardener

1. AI cần 3 lớp input:
   - **structured state**: garden, cycle, health score, alerts, forecast
   - **evidence graph**: care logs, quality checks, sensor rollups, snapshots
   - **conversation context**: user hỏi gì trước đó, là owner hay viewer
2. AI trả ra:
   - chat answer
   - daily summary
   - anomaly explanation
   - harvest ETA note
   - recommendation / escalation
3. Mỗi output AI nên lưu:
   - text summary
   - confidence
   - references tới evidence
4. Khi confidence thấp hoặc dữ liệu xung đột:
   - AI không nên bịa
   - thay vào đó escalate sang support / operator check

---

## 7) Hướng tích hợp AI agents

## 7.1. Vai trò AI nên chia thành 4 agent class

### A. `AI Gardener Assistant`
Vai trò customer-facing.
- trả lời câu hỏi tự nhiên
- tóm tắt tình trạng vườn
- giải thích chỉ số
- nhắc mốc mùa vụ

Nguồn đọc chính:
- gardens
- crop_cycles
- sensor rollups
- care_events
- alerts
- harvest_batches
- quality_checks
- snapshots summaries

### B. `AI Insight Generator`
Vai trò batch/background.
- chạy hằng ngày / hằng tuần
- tạo `daily_summary`, `weekly_summary`, `harvest_forecast`
- ghi vào `wp_gdn_ai_insights`

### C. `AI Anomaly Watcher`
Vai trò giám sát.
- phát hiện sensor drift
- camera offline kéo dài
- tăng trưởng chậm bất thường
- thiếu care events so với expected cadence

Nên kết hợp rule-based trước, AI tăng chất lượng giải thích sau.

### D. `AI Quality Narrator`
Vai trò trust layer.
- tổng hợp evidence cho batch/trace report
- giải thích ngôn ngữ dễ hiểu cho user family
- không thay cho kết luận kiểm định thật, chỉ là lớp diễn giải minh bạch

---

## 7.2. Nguyên tắc tích hợp AI đúng cho sản phẩm này

### Nguyên tắc 1: AI không là source of truth
Source of truth vẫn là:
- sensor readings,
- care logs,
- batch/quality records,
- camera metadata,
- subscription & access data.

AI chỉ là lớp:
- đọc,
- tóm tắt,
- phát hiện pattern,
- giải thích,
- nhắc việc.

### Nguyên tắc 2: AI phải bám evidence refs
Mọi insight quan trọng nên truy ra được nguồn.
Đặc biệt với các câu như:
- “hôm nay vườn ổn định”
- “còn 5 ngày tới thu hoạch”
- “đợt này chất lượng tốt”

### Nguyên tắc 3: dùng precomputed summaries cho UI nhanh
Không nên mỗi lần mở dashboard lại gọi model lớn để nghĩ lại từ đầu.
Nên có:
- cron/job tạo insight sẵn
- chat mới gọi AI runtime khi user hỏi sâu hơn

### Nguyên tắc 4: AI output cần lifecycle
- draft
- published
- expired / retracted

Điều này quan trọng vì forecast và summary có tính thời điểm.

---

## 7.3. Cách nối AI vào WordPress thực tế

### Hướng đơn giản phase đầu
- WordPress custom plugin làm orchestration chính
- cron/job nội bộ chạy summary theo lịch
- gọi AI API ngoài khi cần
- lưu output vào `wp_gdn_ai_insights` và `wp_gdn_ai_messages`

### Hướng scale hơn về sau
- tách AI workers/service riêng
- WordPress giữ auth + UI + business orchestration
- worker riêng xử lý:
  - batch summarization
  - vision summaries
  - anomaly detection
  - embedding / retrieval index

Khuyến nghị owner-level:
- **phase 1 không cần tách microservice quá sớm**
- nhưng data schema nên chuẩn bị để sau này tách worker ra không đau.

---

## 8) Build order dữ liệu đề xuất

Không nên xây toàn bộ schema một lúc. Nên làm theo thứ tự phục vụ trực tiếp MVP UI.

## Phase 1 — Ownership + commerce bridge + portal shell
Mục tiêu: vào portal thấy “đây là vườn của tôi”.

Làm trước:
- `households`
- `household_members`
- `gardens`
- `plan_catalog`
- `subscriptions`
- `garden_zones`
- `cameras`
- `devices`

Lúc này đã đủ dựng:
- login
- dashboard shell
- plan entitlement
- family access khung cơ bản
- thiết bị/camera registry

---

## Phase 2 — Crop lifecycle + care log
Mục tiêu: portal bắt đầu có cảm giác sống.

Làm tiếp:
- `crop_catalog`
- `crop_cycles`
- `cycle_milestones`
- `care_events`
- `care_event_media`
- `control_requests`

Lúc này đã dựng được:
- dashboard meaningful
- care log timeline
- harvest countdown cơ bản
- season story

---

## Phase 3 — Realtime data + rollups + alerts
Mục tiêu: webcam/status thật sự hữu ích hằng ngày.

Làm tiếp:
- `sensor_readings`
- `sensor_daily_rollups`
- `camera_snapshots`
- `timelapse_assets`
- `alerts`
- `notifications`

Lúc này đã dựng được:
- status realtime
- chart cơ bản
- alerts
- daily summary input cho AI

---

## Phase 4 — Quality / harvest / traceability
Mục tiêu: chốt trust layer và khác biệt với “dịch vụ rau sạch” thông thường.

Làm tiếp:
- `harvest_batches`
- `quality_checks`
- `traceability_records`
- `quality_evidence_links`

Lúc này đã dựng được:
- quality & safety page có substance thật
- batch trace report
- evidence graph cho AI narrator

---

## Phase 5 — AI layer
Mục tiêu: tăng retention và cảm giác được đồng hành.

Làm tiếp:
- `ai_conversations`
- `ai_messages`
- `ai_insights`
- `ai_evidence_refs`

Lúc này mới nên mở rộng mạnh:
- daily AI summary
- AI gardener chat sâu hơn
- anomaly explanations
- family-friendly explanations

---

## Phase 6 — Tối ưu scale / analytics / advanced control
Chỉ làm sau khi MVP đã sống:
- hourly rollups riêng
- event bus / queues
- embedding index riêng
- advanced device command audit
- per-member notification preference sâu hơn

---

## 9) Khuyến nghị kỹ thuật quan trọng để tránh đi sai sớm

## 9.1. MySQL chỉ giữ metadata media, không giữ file video/ảnh nặng
- snapshot/timelapse/video ở object storage
- DB lưu path, checksum, timestamps, ownership refs

## 9.2. Sensor readings nên có retention strategy
- raw giữ đủ dài cho phân tích ngắn-trung hạn
- dashboard thường đọc rollups
- tránh portal query raw table nặng liên tục

## 9.3. Mọi bảng timeline quan trọng cần `created_at`, `updated_at`, actor/source
Đặc biệt:
- care events
- alerts
- AI insights
- control requests
- quality checks

Vì sản phẩm này sống bằng minh bạch và auditability.

## 9.4. Tránh over-model phase đầu
Không cần model quá sâu kiểu ERP nông nghiệp ngay từ đầu.
Với phase 1 hộ gia đình/cá nhân, quan trọng nhất là 6 trải nghiệm:
- ownership
- camera
- status
- care log
- quality/batch
- AI companion

Schema nên sâu vừa đủ cho 6 trải nghiệm đó.

## 9.5. Tách customer-visible vs internal-only ngay từ đầu
Nhiều care/quality records có thể có phần nội bộ.
Nên có cờ như:
- `visibility`
- `customer_summary`
- `internal_notes`

Để sau này tránh rò rỉ dữ liệu vận hành quá kỹ thuật.

---

## 10) MVP schema tối thiểu nếu cần chốt thật gọn

Nếu cần owner phê duyệt bản tối giản nhất để vào build, em đề xuất tối thiểu 14 bảng custom đầu tiên:

1. `wp_gdn_households`
2. `wp_gdn_household_members`
3. `wp_gdn_gardens`
4. `wp_gdn_plan_catalog`
5. `wp_gdn_subscriptions`
6. `wp_gdn_garden_zones`
7. `wp_gdn_crop_catalog`
8. `wp_gdn_crop_cycles`
9. `wp_gdn_cameras`
10. `wp_gdn_devices`
11. `wp_gdn_sensor_readings`
12. `wp_gdn_care_events`
13. `wp_gdn_harvest_batches`
14. `wp_gdn_alerts`

14 bảng này đã đủ để dựng một portal MVP có hồn.
Các bảng AI / timelapse / traceability có thể nối tiếp ngay sau đó.

---

## 11) Chốt định hướng owner-level

### Nên chốt như sau
- **WordPress** là lớp website + account foundation.
- **WooCommerce** là lớp bán gói + thanh toán.
- **Custom plugin + custom MySQL tables** là lõi sản phẩm thật.
- **AI agents** là lớp đọc/giải thích/nhắc việc trên graph dữ liệu của khu vườn.

### Hình dung một câu ngắn
Không xây “website có vài custom field”, mà đang xây:
**một ownership data platform cho khu vườn số của từng gia đình.**

### Nếu owner hỏi đâu là 3 bảng quan trọng nhất
Em sẽ trả lời:
1. `wp_gdn_gardens`
2. `wp_gdn_crop_cycles`
3. `wp_gdn_care_events`

Vì 3 bảng này tạo ra cảm giác:
- có một khu vườn thật,
- có một mùa vụ thật,
- có quá trình chăm sóc thật.

Camera, sensor, batch, AI đều xoay quanh trục đó.

---

## 12) Bước tiếp theo hợp lý sau tài liệu này

Nếu đi tiếp vòng sau, thứ tự hợp lý là:
1. chốt **MVP table list cuối cùng**,
2. chốt **REST/API surface** cho portal plugin,
3. chốt **event model** cho IoT + AI ingestion,
4. rồi mới viết migration/DDL thật.

Nếu cần, vòng kế tiếp em có thể làm luôn:
- sơ đồ ERD rút gọn,
- danh sách API endpoints cho portal,
- hoặc mapping field-by-field từ từng page HTML prototype sang từng bảng dữ liệu.
