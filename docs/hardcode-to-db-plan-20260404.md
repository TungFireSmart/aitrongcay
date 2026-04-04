# Aitrongcay hardcode → DB plan (2026-04-04)

## 1) Priority map

| Area | Current source | Problem | Target DB/source | Priority |
|---|---|---|---|---|
| Garden profile (`garden_name`, `garden_code`, `summary`, `status`) | `inc/portal-garden-data.php` hardcode by email | Wrong garden can inherit demo text | `wp_aitr_gardens` | P1 |
| Pots list + per-pot display fields | hardcode dataset + usermeta custom pots | Mixed real/demo state | `wp_aitr_garden_pots` | P1 |
| Pot journal/note | localStorage → now `wp_aitr_garden_notes` | fixed | `wp_aitr_garden_notes` | done |
| Tool shelf | hardcode dataset | Shows fake inventory | `wp_aitr_garden_tools` | P1 |
| AI summary/tips/prompts | hardcode dataset | Feels real but is fake | `wp_aitr_garden_ai_cache` or generated from DB | P2 |
| Device mapping defaults | hardcoded P-001..P-004 defaults | Not scalable for real gardens | keep option map, but source pot list from DB | P2 |
| Market sample/profile data | hardcode demo arrays in `page.php` | Demo leaks into live UI | WP posts + garden DB | P2 |
| Homepage demo portal section | hardcoded Minh Anh demo | cosmetic demo leak | featured demo option / public garden | P3 |
| Copy mentioning `khu vườn mẫu` | static copy | old architecture wording | content cleanup | P3 |

## 2) Tables

### `wp_aitr_gardens`
- `garden_key` unique
- `owner_user_id`
- `garden_code`
- `garden_name`
- `summary`
- `status_line`
- `created_at`
- `updated_at`

### `wp_aitr_garden_pots`
- `garden_key`
- `pot_code`
- `pot_name`
- `status`
- `status_summary`
- `ph`
- `temperature`
- `humidity`
- `light_label`
- `light_device`
- `pump_label`
- `irrigation`
- `video_url`
- `image_url`
- `ai_note`
- `harvest_eta`
- `sort_order`
- `created_at`
- `updated_at`

### `wp_aitr_garden_tools`
- `garden_key`
- `tool_key`
- `name`
- `type`
- `description`
- `owned`
- `qty`
- `image`
- `sort_order`
- `created_at`
- `updated_at`

### Existing
- `wp_aitr_garden_notes` ✅
- `wp_aitr_garden_members` ✅

## 3) Refactor order
1. Add DB tables + CRUD helpers.
2. Make `portal-garden-data.php` read DB first by `garden_key`.
3. Keep old hardcoded dataset only as final fallback for legacy/dev.
4. Move create-first-pot / rename-pot toward DB writes.
5. Move tool shelf to DB reads.
6. Remove demo-by-email coupling.

## 4) Immediate coding target
- scaffold tables: gardens, garden_pots, garden_tools
- DB read helpers
- portal reads DB first
- first-pot AJAX writes new pots into DB table
