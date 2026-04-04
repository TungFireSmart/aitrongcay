<?php

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

function aitrongcay_portal_sample_profiles(): array
{
    return [];
}

function aitrongcay_portal_profile_for_user(?WP_User $user = null): array
{
    $user = $user instanceof WP_User ? $user : wp_get_current_user();
    $display_name = trim((string) ($user->display_name ?: $user->first_name ?: $user->user_login));
    $display_name = $display_name !== '' ? $display_name : 'Anh/chị';
    $garden_key = function_exists('aitrongcay_current_garden_key') ? aitrongcay_current_garden_key($user) : '';
    $garden_name = function_exists('aitrongcay_build_default_garden_name') ? aitrongcay_build_default_garden_name($garden_key, $user) : ('Khu vườn của ' . $display_name);

    return [
        'name' => $display_name,
        'garden_code' => $garden_key !== '' ? strtoupper(substr(md5($garden_key), 0, 6)) : 'GARDEN',
        'garden_name' => $garden_name,
        'summary' => 'Khu vườn này sẽ dần hiện rõ hơn khi có khay, ảnh, dữ liệu cảm biến và nhật ký chăm sóc được cập nhật đúng.',
        'status' => 'Sẵn sàng theo dõi',
        'market_title' => $display_name . ' • Chia sẻ khu vườn của mình',
        'market_body' => 'Khu vườn đang được theo dõi theo từng khay để việc chăm sóc, quan sát và đối chiếu trở nên rõ ràng hơn mỗi ngày.',
        'market_meta' => ['Hình thức: chia sẻ từ khu vườn', 'Có thể gắn ảnh, video và ghi chú theo khay'],
        'facebook_caption' => 'Hôm nay em mở khu vườn của mình và tiếp tục cập nhật các mốc chăm sóc để mọi thứ rõ ràng hơn từng ngày.',
    ];
}

function aitrongcay_portal_dataset_library(): array
{
    return [];
}

function aitrongcay_portal_default_dataset(): array
{
    return aitrongcay_portal_empty_dataset(wp_get_current_user());
}

function aitrongcay_portal_empty_dataset(?WP_User $user = null): array
{
    $profile = aitrongcay_portal_profile_for_user($user);
    $name = is_array($profile) ? (string) ($profile['name'] ?? 'Anh/chị') : 'Anh/chị';

    return [
        'match_emails' => [],
        'ai' => [
            'name' => 'AI Agent của ' . $name,
            'summary' => 'Khu vườn này còn đang chờ những khay đầu tiên và các dữ liệu thực tế đầu tiên được đưa vào. Khi bắt đầu có dữ liệu, mọi thứ ở đây sẽ hiện dần theo đúng nhịp chăm sóc của khu vườn.',
            'tips' => [
                'Bắt đầu bằng một khay trồng cây đầu tiên để khu vườn có mốc theo dõi rõ ràng.',
                'Khi đã có khay, ảnh, chỉ số và ghi chú chăm sóc sẽ dần hiện ra ngay trong khu vườn này.',
                'Nếu đã có khay ngoài thực tế mà ở đây chưa thấy, chỉ cần gắn khay đó đúng vào khu vườn là được.',
            ],
            'starter_prompts' => [
                'Hướng dẫn em thêm khay đầu tiên.',
                'Khu vườn này cần chuẩn bị gì để bắt đầu có dữ liệu thật?',
                'Khi nào dashboard bắt đầu hiện dữ liệu theo từng khay?',
            ],
        ],
        'pots' => [],
        'tool_shelf' => [],
    ];
}

function aitrongcay_portal_dataset_for_garden(string $garden_key = '', ?WP_User $viewer = null): array
{
    $owner = function_exists('aitrongcay_get_garden_owner_user') ? aitrongcay_get_garden_owner_user($garden_key) : null;
    $context_user = $owner instanceof WP_User ? $owner : ($viewer instanceof WP_User ? $viewer : wp_get_current_user());

    $record = function_exists('aitrongcay_get_garden_record') ? aitrongcay_get_garden_record($garden_key) : null;
    $db_pots = function_exists('aitrongcay_get_db_pots') ? aitrongcay_get_db_pots($garden_key) : [];
    $db_tools = function_exists('aitrongcay_get_db_tools') ? aitrongcay_get_db_tools($garden_key) : [];

    if (is_array($record) || ! empty($db_pots) || ! empty($db_tools)) {
        $profile = aitrongcay_portal_profile_for_user($context_user instanceof WP_User ? $context_user : null);
        $garden_name = trim((string) ($record['garden_name'] ?? ($profile['garden_name'] ?? '')));
        $garden_code = trim((string) ($record['garden_code'] ?? ($profile['garden_code'] ?? '')));
        $summary = trim((string) ($record['summary'] ?? ''));
        $status_line = trim((string) ($record['status_line'] ?? ''));

        $pots = array_map(static function (array $pot): array {
            return [
                'code' => (string) ($pot['pot_code'] ?? ''),
                'name' => (string) ($pot['pot_name'] ?? ''),
                'status' => (string) ($pot['status'] ?? ''),
                'ph' => (string) ($pot['ph'] ?? ''),
                'temperature' => (string) ($pot['temperature'] ?? ''),
                'humidity' => (string) ($pot['humidity'] ?? ''),
                'light' => (string) ($pot['light_label'] ?? ''),
                'light_device' => (string) ($pot['light_device'] ?? ''),
                'pump' => (string) ($pot['pump_label'] ?? ''),
                'irrigation' => (string) ($pot['irrigation'] ?? ''),
                'video' => (string) ($pot['video_url'] ?? ''),
                'image' => (string) ($pot['image_url'] ?? ''),
                'ai_note' => (string) ($pot['ai_note'] ?? ''),
                'status_summary' => (string) ($pot['status_summary'] ?? ''),
                'harvest_eta' => (string) ($pot['harvest_eta'] ?? ''),
                'trays' => [(string) ($pot['pot_name'] ?? '')],
            ];
        }, $db_pots);

        $tools = array_map(static function (array $item): array {
            return [
                'name' => (string) ($item['name'] ?? ''),
                'type' => (string) ($item['type'] ?? ''),
                'description' => (string) ($item['description'] ?? ''),
                'owned' => (int) ($item['owned'] ?? 0),
                'qty' => (int) ($item['qty'] ?? 0),
                'image' => (string) ($item['image'] ?? ''),
            ];
        }, $db_tools);

        if ($status_line === '' && ! empty($pots)) {
            $status_line = count($pots) . ' khay • đang theo dõi';
        }
        if ($garden_name === '' && $context_user instanceof WP_User) {
            $garden_name = 'Khu vườn của ' . trim((string) ($context_user->display_name ?: $context_user->user_login));
        }

        return [
            'match_emails' => [],
            'ai' => [
                'name' => 'AI Agent của ' . ($garden_name !== '' ? $garden_name : (($profile['name'] ?? 'khu vườn này'))),
                'summary' => $summary !== '' ? $summary : 'Khu vườn này đang dùng dữ liệu thật theo đúng garden_key hiện tại.',
                'tips' => ! empty($pots) ? ['Em đang ưu tiên đọc đúng dữ liệu của từng khay trong vườn này.', 'Nếu cần, anh/chị có thể cập nhật ghi chú để lịch sử canh tác phản ánh đúng thực tế.', 'Các thông tin khay sẽ dần chuyển hết sang nguồn DB thật thay cho dữ liệu mẫu.'] : ['Vườn này chưa có nhiều dữ liệu thực tế. Khi thêm khay và cập nhật nhật ký, dashboard sẽ đầy dần.'],
                'starter_prompts' => ['Hôm nay khay nào cần chú ý nhất?', 'Tóm tắt nhanh tình trạng vườn này giúp em.', 'Gợi ý việc nên làm tiếp theo cho từng khay.'],
            ],
            'pots' => $pots,
            'tool_shelf' => $tools,
            'garden_meta' => [
                'garden_name' => $garden_name,
                'garden_code' => $garden_code,
                'status' => $status_line,
            ],
        ];
    }

    return aitrongcay_portal_empty_dataset($context_user instanceof WP_User ? $context_user : null);
}

function aitrongcay_portal_garden_ai(string $garden_key = '', ?WP_User $viewer = null): array
{
    return (array) (aitrongcay_portal_dataset_for_garden($garden_key, $viewer)['ai'] ?? []);
}

function aitrongcay_portal_pots(string $garden_key = '', ?WP_User $viewer = null): array
{
    $viewer = $viewer instanceof WP_User ? $viewer : wp_get_current_user();
    $pots = (array) (aitrongcay_portal_dataset_for_garden($garden_key, $viewer)['pots'] ?? []);

    if (function_exists('aitrongcay_get_custom_pots')) {
        $owner = function_exists('aitrongcay_get_garden_owner_user') ? aitrongcay_get_garden_owner_user($garden_key) : null;
        $target_user = $owner instanceof WP_User ? $owner : $viewer;
        $target_user_id = (int) ($target_user->ID ?? 0);
        if ($target_user_id > 0) {
            $db_or_custom_pots = aitrongcay_get_custom_pots($garden_key, $target_user_id);
            if (! empty($db_or_custom_pots)) {
                $pots = $db_or_custom_pots;
            }
        }
    }

    if (function_exists('aitrongcay_portal_apply_garden_device_mapping')) {
        $pots = aitrongcay_portal_apply_garden_device_mapping($pots, $garden_key);
    }

    if (function_exists('aitrongcay_get_pot_name_overrides')) {
        $owner = function_exists('aitrongcay_get_garden_owner_user') ? aitrongcay_get_garden_owner_user($garden_key) : null;
        $target_user = $owner instanceof WP_User ? $owner : $viewer;
        $target_user_id = (int) ($target_user->ID ?? 0);
        $name_overrides = $target_user_id > 0 ? aitrongcay_get_pot_name_overrides($garden_key, $target_user_id) : [];
        if (is_array($name_overrides) && ! empty($name_overrides)) {
            foreach ($pots as &$pot) {
                $pot_code = (string) ($pot['code'] ?? '');
                if ($pot_code !== '' && isset($name_overrides[$pot_code]) && trim((string) $name_overrides[$pot_code]) !== '') {
                    $pot['name'] = trim((string) $name_overrides[$pot_code]);
                }
            }
            unset($pot);
        }
    }

    return $pots;
}

function aitrongcay_portal_tool_shelf(string $garden_key = '', ?WP_User $viewer = null): array
{
    return (array) (aitrongcay_portal_dataset_for_garden($garden_key, $viewer)['tool_shelf'] ?? []);
}
