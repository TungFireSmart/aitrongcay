<?php

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

function aitrongcay_blynk_shared_token_markers(): array
{
    return ['__shared__', 'shared', 'shared-token', 'same-as-shared'];
}

function aitrongcay_blynk_is_shared_token_marker(string $token): bool
{
    return in_array(strtolower(trim($token)), aitrongcay_blynk_shared_token_markers(), true);
}

function aitrongcay_blynk_device_schema(): array
{
    return [
        'base' => ['label' => 'Blynk base URL', 'type' => 'url', 'default' => 'https://blynk.cloud/external/api'],
        'token' => ['label' => 'Blynk token chung (sensor + pump fallback)', 'type' => 'text', 'default' => ''],
        'vpins' => [
            'temp' => ['label' => 'VPin nhiệt độ', 'default' => 'V0'],
            'hum' => ['label' => 'VPin độ ẩm không khí', 'default' => 'V1'],
            'soil' => ['label' => 'VPin độ ẩm đất', 'default' => 'V11'],
            'pump' => ['label' => 'VPin bơm', 'default' => 'V2'],
            'light1' => ['label' => 'VPin đèn 1', 'default' => 'V5'],
            'light2' => ['label' => 'VPin đèn 2', 'default' => 'V6'],
            'light3' => ['label' => 'VPin đèn 3', 'default' => 'V7'],
            'light4' => ['label' => 'VPin đèn 4', 'default' => 'V8'],
        ],
        'devices' => [
            'pump' => ['label' => 'Khóa thiết bị bơm', 'default' => 'pump'],
            'light1' => ['label' => 'Khóa thiết bị đèn 1', 'default' => 'light1'],
            'light2' => ['label' => 'Khóa thiết bị đèn 2', 'default' => 'light2'],
            'light3' => ['label' => 'Khóa thiết bị đèn 3', 'default' => 'light3'],
            'light4' => ['label' => 'Khóa thiết bị đèn 4', 'default' => 'light4'],
        ],
        'pots' => [
            'P-001' => ['label' => 'Khay/Pot P-001', 'default' => 'light1'],
            'P-002' => ['label' => 'Khay/Pot P-002', 'default' => 'light2'],
            'P-003' => ['label' => 'Khay/Pot P-003', 'default' => 'light3'],
            'P-004' => ['label' => 'Khay/Pot P-004', 'default' => 'light4'],
        ],
        'pot_tokens' => [
            'P-001' => ['label' => 'Token khay P-001', 'default' => '__shared__'],
            'P-002' => ['label' => 'Token khay P-002', 'default' => '__shared__'],
            'P-003' => ['label' => 'Token khay P-003', 'default' => '__shared__'],
            'P-004' => ['label' => 'Token khay P-004', 'default' => '__shared__'],
        ],
    ];
}

function aitrongcay_blynk_config_option_name(): string
{
    return 'aitrongcay_garden_device_configs';
}

function aitrongcay_blynk_default_config(): array
{
    $schema = aitrongcay_blynk_device_schema();

    $config = [
        'base' => (string) ($schema['base']['default'] ?? ''),
        'token' => (string) ($schema['token']['default'] ?? ''),
        'vpins' => [],
        'devices' => [],
        'pots' => [],
        'pot_tokens' => [],
    ];

    foreach ((array) ($schema['vpins'] ?? []) as $key => $field) {
        $config['vpins'][$key] = (string) ($field['default'] ?? '');
    }
    foreach ((array) ($schema['devices'] ?? []) as $key => $field) {
        $config['devices'][$key] = (string) ($field['default'] ?? '');
    }
    foreach ((array) ($schema['pots'] ?? []) as $key => $field) {
        $config['pots'][$key] = (string) ($field['default'] ?? '');
    }
    foreach ((array) ($schema['pot_tokens'] ?? []) as $key => $field) {
        $config['pot_tokens'][$key] = trim((string) ($field['default'] ?? ''));
    }

    return $config;
}

function aitrongcay_blynk_builtin_configs(): array
{
    $default = aitrongcay_blynk_default_config();
    $primary = $default;
    $primary['token'] = '8lriQRJG5nyKCEUyBM6fiUPKdaAtx9iX';
    $primary['vpins'] = [
        'temp' => 'V0',
        'hum' => 'V1',
        'soil' => 'V11',
        'pump' => 'V2',
        'light1' => 'V5',
        'light2' => 'V6',
        'light3' => 'V7',
        'light4' => 'V8',
    ];
    $primary['pots'] = [
        'P-001' => 'light1',
        'P-002' => 'light2',
        'P-003' => 'light3',
        'P-004' => 'light4',
    ];
    $primary['pot_tokens'] = [
        'P-001' => '__shared__',
        'P-002' => '__shared__',
        'P-003' => '__shared__',
        'P-004' => '__shared__',
    ];

    return [
        'primary-live' => $primary,
    ];
}

function aitrongcay_seed_real_blynk_configs(): void
{
    $saved = aitrongcay_get_saved_blynk_configs();
    $builtins = aitrongcay_blynk_builtin_configs();
    $seed_aliases = [
        'primary-live' => 'primary-live',
    ];

    $dirty = false;
    foreach ($seed_aliases as $target_key => $source_key) {
        if (! isset($builtins[$source_key])) {
            continue;
        }

        $existing = isset($saved[$target_key]) && is_array($saved[$target_key])
            ? aitrongcay_normalize_blynk_config($saved[$target_key])
            : [];
        $token = trim((string) ($existing['token'] ?? ''));
        $needs_seed = $existing === [] || $token === '' || $token === 'shared-token-placeholder';
        if (! $needs_seed) {
            continue;
        }

        $saved[$target_key] = aitrongcay_normalize_blynk_config(array_replace_recursive(
            aitrongcay_blynk_default_config(),
            $builtins[$source_key],
            $existing
        ));
        $saved[$target_key]['token'] = (string) ($builtins[$source_key]['token'] ?? '');
        $dirty = true;
    }

    if ($dirty) {
        aitrongcay_save_blynk_configs($saved);
    }
}
add_action('init', 'aitrongcay_seed_real_blynk_configs', 35);

function aitrongcay_normalize_blynk_config(array $config): array
{
    $default = aitrongcay_blynk_default_config();

    $normalized = [
        'base' => esc_url_raw((string) ($config['base'] ?? $default['base'])),
        'token' => trim((string) ($config['token'] ?? $default['token'])),
        'vpins' => [],
        'devices' => [],
        'pots' => [],
        'pot_tokens' => [],
    ];

    foreach ($default['vpins'] as $key => $value) {
        $normalized['vpins'][$key] = strtoupper(trim((string) (($config['vpins'][$key] ?? $value))));
    }
    foreach ($default['devices'] as $key => $value) {
        $normalized['devices'][$key] = sanitize_key((string) (($config['devices'][$key] ?? $value)));
    }
    foreach ($default['pots'] as $key => $value) {
        $normalized['pots'][$key] = sanitize_key((string) (($config['pots'][$key] ?? $value)));
    }
    foreach ($default['pot_tokens'] as $key => $value) {
        $normalized['pot_tokens'][$key] = trim((string) (($config['pot_tokens'][$key] ?? $value)));
    }

    if ($normalized['base'] === '') {
        $normalized['base'] = $default['base'];
    }

    return $normalized;
}

function aitrongcay_get_saved_blynk_configs(): array
{
    $saved = get_option(aitrongcay_blynk_config_option_name(), []);
    if (! is_array($saved)) {
        return [];
    }

    $normalized = [];
    foreach ($saved as $garden_key => $config) {
        $garden_key = sanitize_text_field((string) $garden_key);
        if ($garden_key === '' || ! is_array($config)) {
            continue;
        }
        $normalized[$garden_key] = aitrongcay_normalize_blynk_config($config);
    }

    return $normalized;
}

function aitrongcay_save_blynk_configs(array $configs): void
{
    $normalized = [];
    foreach ($configs as $garden_key => $config) {
        $garden_key = sanitize_text_field((string) $garden_key);
        if ($garden_key === '' || ! is_array($config)) {
            continue;
        }
        $normalized[$garden_key] = aitrongcay_normalize_blynk_config($config);
    }

    update_option(aitrongcay_blynk_config_option_name(), $normalized, false);
}

function aitrongcay_resolve_blynk_aliases_for_garden(string $garden_key = ''): array
{
    $aliases = [];
    $garden_key = trim($garden_key);
    if ($garden_key !== '') {
        $aliases[] = $garden_key;
    }

    $owner = function_exists('aitrongcay_get_garden_owner_user') ? aitrongcay_get_garden_owner_user($garden_key) : null;
    if ($owner instanceof WP_User) {
        $aliases[] = strtolower(trim((string) $owner->user_email));
    }

    if (function_exists('aitrongcay_portal_dataset_library')) {
        foreach ((array) aitrongcay_portal_dataset_library() as $dataset_key => $dataset) {
            $matched = false;
            foreach ((array) ($dataset['match_emails'] ?? []) as $email) {
                $email = strtolower(trim((string) $email));
                if ($email !== '' && in_array($email, $aliases, true)) {
                    $matched = true;
                    break;
                }
            }
            if ($matched) {
                $aliases[] = (string) $dataset_key;
            }
        }
    }

    return array_values(array_unique(array_filter($aliases)));
}

function aitrongcay_blynk_config(string $garden_key = ''): array
{
    $default = aitrongcay_blynk_default_config();
    $builtins = aitrongcay_blynk_builtin_configs();
    $saved = aitrongcay_get_saved_blynk_configs();

    foreach (aitrongcay_resolve_blynk_aliases_for_garden($garden_key) as $alias) {
        if (isset($saved[$alias]) && is_array($saved[$alias])) {
            return aitrongcay_normalize_blynk_config(array_replace_recursive($default, $saved[$alias]));
        }
        if (isset($builtins[$alias]) && is_array($builtins[$alias])) {
            return aitrongcay_normalize_blynk_config(array_replace_recursive($default, $builtins[$alias]));
        }
    }

    return $default;
}

function aitrongcay_blynk_effective_token(string $garden_key, string $raw_token = ''): string
{
    $config = aitrongcay_blynk_config($garden_key);
    $shared_token = trim((string) ($config['token'] ?? ''));
    $raw_token = trim($raw_token);

    if ($raw_token === '' || aitrongcay_blynk_is_shared_token_marker($raw_token)) {
        return $shared_token;
    }

    return $raw_token;
}

function aitrongcay_blynk_pot_token_for_code(string $garden_key, string $pot_code): string
{
    $config = aitrongcay_blynk_config($garden_key);
    $raw_token = trim((string) (($config['pot_tokens'][$pot_code] ?? '')));
    return aitrongcay_blynk_effective_token($garden_key, $raw_token);
}

function aitrongcay_blynk_pot_token_for_device(string $garden_key, string $device): string
{
    $device = sanitize_key($device);
    if ($device === '' || $device === 'pump') {
        return '';
    }

    $config = aitrongcay_blynk_config($garden_key);
    foreach ((array) ($config['pots'] ?? []) as $pot_code => $mapped_device) {
        if (sanitize_key((string) $mapped_device) !== $device) {
            continue;
        }

        return aitrongcay_blynk_pot_token_for_code($garden_key, (string) $pot_code);
    }

    return '';
}

function aitrongcay_blynk_remote_get(array $query_args, string $base, string $endpoint = '/get')
{
    $url = add_query_arg($query_args, untrailingslashit($base) . $endpoint);
    return wp_remote_get($url, ['timeout' => 10]);
}

function aitrongcay_blynk_read_values(string $token, array $vpins, string $base): array
{
    $token = trim($token);
    if ($token === '' || $vpins === []) {
        return [];
    }

    $request_args = ['token' => $token];
    foreach ($vpins as $vpin) {
        $vpin = strtoupper(trim((string) $vpin));
        if ($vpin === '') {
            continue;
        }
        $request_args[$vpin] = '';
    }

    $response = aitrongcay_blynk_remote_get($request_args, $base, '/get');
    if (is_wp_error($response)) {
        return [];
    }

    $body = (string) wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    return is_array($data) ? $data : [];
}

function aitrongcay_portal_apply_garden_device_mapping(array $pots, string $garden_key = ''): array
{
    $config = aitrongcay_blynk_config($garden_key);
    $pot_map = (array) ($config['pots'] ?? []);
    $device_map = (array) ($config['devices'] ?? []);
    $pump_device = (string) ($device_map['pump'] ?? 'pump');

    foreach ($pots as $index => $pot) {
        if (! is_array($pot)) {
            continue;
        }

        $pot_code = (string) ($pot['code'] ?? '');
        $mapped_device = (string) ($pot_map[$pot_code] ?? ($pot['light_device'] ?? ''));
        if ($mapped_device !== '') {
            $pots[$index]['light_device'] = $mapped_device;
        }

        $raw_pot_token = trim((string) ($config['pot_tokens'][$pot_code] ?? ''));
        $effective_pot_token = aitrongcay_blynk_effective_token($garden_key, $raw_pot_token);
        $pots[$index]['blynk_token'] = $effective_pot_token;
        $pots[$index]['blynk_token_raw'] = $raw_pot_token;
        $pots[$index]['uses_shared_blynk_token'] = $raw_pot_token === '' || aitrongcay_blynk_is_shared_token_marker($raw_pot_token);
        $pots[$index]['has_device'] = $effective_pot_token !== '';

        if (! empty($pots[$index]['pump'])) {
            $pots[$index]['pump_device'] = $pump_device;
        }
    }

    return $pots;
}

function aitrongcay_known_gardens_for_device_admin(): array
{
    $gardens = [];

    if (function_exists('aitrongcay_portal_dataset_library')) {
        foreach ((array) aitrongcay_portal_dataset_library() as $dataset_key => $dataset) {
            $email = strtolower(trim((string) (($dataset['match_emails'][0] ?? ''))));
            $user = $email !== '' ? get_user_by('email', $email) : null;
            $garden_key = $user instanceof WP_User && function_exists('aitrongcay_current_garden_key')
                ? aitrongcay_current_garden_key($user)
                : (string) $dataset_key;
            $profile = $user instanceof WP_User && function_exists('aitrongcay_portal_profile_for_user')
                ? aitrongcay_portal_profile_for_user($user)
                : null;
            $gardens[$garden_key] = [
                'garden_key' => $garden_key,
                'label' => (string) (($profile['garden_name'] ?? $dataset_key)),
                'owner_email' => $email,
                'dataset_key' => (string) $dataset_key,
            ];
        }
    }

    global $wpdb;
    if (function_exists('aitrongcay_garden_members_table')) {
        $table = aitrongcay_garden_members_table();
        $rows = $wpdb->get_results("SELECT DISTINCT garden_key FROM {$table} ORDER BY garden_key ASC", ARRAY_A) ?: [];
        foreach ($rows as $row) {
            $garden_key = sanitize_text_field((string) ($row['garden_key'] ?? ''));
            if ($garden_key === '') {
                continue;
            }
            if (! isset($gardens[$garden_key])) {
                $owner = function_exists('aitrongcay_get_garden_owner_user') ? aitrongcay_get_garden_owner_user($garden_key) : null;
                $profile = function_exists('aitrongcay_portal_profile_for_garden_context') ? aitrongcay_portal_profile_for_garden_context($garden_key, $owner instanceof WP_User ? $owner : null) : null;
                $gardens[$garden_key] = [
                    'garden_key' => $garden_key,
                    'label' => (string) (($profile['garden_name'] ?? $garden_key)),
                    'owner_email' => strtolower(trim((string) ($owner->user_email ?? ''))),
                    'dataset_key' => '',
                ];
            }
        }
    }

    foreach (array_keys(aitrongcay_get_saved_blynk_configs()) as $saved_key) {
        if (! isset($gardens[$saved_key])) {
            $gardens[$saved_key] = [
                'garden_key' => $saved_key,
                'label' => $saved_key,
                'owner_email' => '',
                'dataset_key' => '',
            ];
        }
    }

    return array_values($gardens);
}

function aitrongcay_device_mapping_admin_menu(): void
{
    add_theme_page(
        'Mapping thiết bị khu vườn',
        'Mapping thiết bị khu vườn',
        'edit_theme_options',
        'aitrongcay-garden-device-mapping',
        'aitrongcay_render_device_mapping_admin_page'
    );

    add_theme_page(
        'Hồ sơ & vật tư khu vườn',
        'Hồ sơ & vật tư khu vườn',
        'edit_theme_options',
        'aitrongcay-garden-profile-tools',
        'aitrongcay_render_garden_profile_tools_admin_page'
    );
}
add_action('admin_menu', 'aitrongcay_device_mapping_admin_menu');

function aitrongcay_handle_device_mapping_admin_save(): void
{
    if (! is_admin() || ! current_user_can('edit_theme_options')) {
        return;
    }

    if (($_POST['action'] ?? '') !== 'aitrongcay_save_device_mapping') {
        return;
    }

    check_admin_referer('aitrongcay_save_device_mapping');

    $submitted = $_POST['garden_configs'] ?? [];
    $submitted = is_array($submitted) ? $submitted : [];

    $configs = [];
    foreach ($submitted as $garden_key => $config) {
        $garden_key = sanitize_text_field((string) $garden_key);
        if ($garden_key === '' || ! is_array($config)) {
            continue;
        }
        $configs[$garden_key] = aitrongcay_normalize_blynk_config($config);
    }

    aitrongcay_save_blynk_configs($configs);

    $redirect = add_query_arg([
        'page' => 'aitrongcay-garden-device-mapping',
        'updated' => 'true',
    ], admin_url('themes.php'));
    wp_safe_redirect($redirect);
    exit;
}
add_action('admin_init', 'aitrongcay_handle_device_mapping_admin_save');

function aitrongcay_handle_garden_profile_tools_admin_save(): void
{
    if (! is_admin() || ! current_user_can('edit_theme_options')) {
        return;
    }
    if (($_POST['action'] ?? '') !== 'aitrongcay_save_garden_profile_tools') {
        return;
    }

    check_admin_referer('aitrongcay_save_garden_profile_tools');

    $garden_key = sanitize_text_field((string) wp_unslash($_POST['garden_key'] ?? ''));
    if ($garden_key === '') {
        return;
    }

    $record = function_exists('aitrongcay_get_garden_record') ? (aitrongcay_get_garden_record($garden_key) ?: []) : [];
    $owner = function_exists('aitrongcay_get_garden_owner_user') ? aitrongcay_get_garden_owner_user($garden_key) : null;
    $owner_user_id = (int) ($record['owner_user_id'] ?? ($owner instanceof WP_User ? $owner->ID : 0));
    if ($owner_user_id <= 0 && $owner instanceof WP_User) {
        $owner_user_id = (int) $owner->ID;
    }
    if ($owner_user_id <= 0) {
        $owner_user_id = get_current_user_id();
    }

    if (function_exists('aitrongcay_upsert_garden_record')) {
        aitrongcay_upsert_garden_record($garden_key, $owner_user_id, [
            'garden_code' => sanitize_text_field((string) wp_unslash($_POST['garden_code'] ?? '')),
            'garden_name' => sanitize_text_field((string) wp_unslash($_POST['garden_name'] ?? '')),
            'summary' => sanitize_textarea_field((string) wp_unslash($_POST['summary'] ?? '')),
            'status_line' => sanitize_text_field((string) wp_unslash($_POST['status_line'] ?? '')),
        ]);
    }

    $submitted_tools = $_POST['tools'] ?? [];
    $submitted_tools = is_array($submitted_tools) ? $submitted_tools : [];
    $tools = [];
    foreach ($submitted_tools as $tool) {
        if (! is_array($tool)) {
            continue;
        }
        $name = sanitize_text_field((string) wp_unslash($tool['name'] ?? ''));
        if ($name === '') {
            continue;
        }
        $tools[] = [
            'tool_key' => sanitize_key((string) wp_unslash($tool['tool_key'] ?? $name)),
            'name' => $name,
            'type' => sanitize_text_field((string) wp_unslash($tool['type'] ?? '')),
            'description' => sanitize_textarea_field((string) wp_unslash($tool['description'] ?? '')),
            'owned' => max(0, (int) ($tool['owned'] ?? 0)),
            'qty' => max(0, (int) ($tool['qty'] ?? 0)),
            'image' => sanitize_text_field((string) wp_unslash($tool['image'] ?? '')),
        ];
    }
    if (function_exists('aitrongcay_replace_garden_tools')) {
        aitrongcay_replace_garden_tools($garden_key, $tools);
    }

    $submitted_pots = $_POST['pots'] ?? [];
    $submitted_pots = is_array($submitted_pots) ? $submitted_pots : [];
    $pots = [];
    foreach ($submitted_pots as $pot) {
        if (! is_array($pot)) {
            continue;
        }
        $pot_code = sanitize_text_field((string) wp_unslash($pot['pot_code'] ?? ''));
        $pot_name = sanitize_text_field((string) wp_unslash($pot['pot_name'] ?? ''));
        if ($pot_code === '' || $pot_name === '') {
            continue;
        }
        $pots[] = [
            'pot_code' => $pot_code,
            'pot_name' => $pot_name,
            'status' => sanitize_text_field((string) wp_unslash($pot['status'] ?? '')),
            'status_summary' => sanitize_textarea_field((string) wp_unslash($pot['status_summary'] ?? '')),
            'ph' => sanitize_text_field((string) wp_unslash($pot['ph'] ?? '')),
            'temperature' => sanitize_text_field((string) wp_unslash($pot['temperature'] ?? '')),
            'humidity' => sanitize_text_field((string) wp_unslash($pot['humidity'] ?? '')),
            'light_label' => sanitize_text_field((string) wp_unslash($pot['light_label'] ?? '')),
            'light_device' => sanitize_key((string) wp_unslash($pot['light_device'] ?? '')),
            'pump_label' => sanitize_text_field((string) wp_unslash($pot['pump_label'] ?? '')),
            'irrigation' => sanitize_text_field((string) wp_unslash($pot['irrigation'] ?? '')),
            'image_url' => esc_url_raw((string) wp_unslash($pot['image_url'] ?? '')),
            'video_url' => esc_url_raw((string) wp_unslash($pot['video_url'] ?? '')),
            'ai_note' => sanitize_textarea_field((string) wp_unslash($pot['ai_note'] ?? '')),
            'harvest_eta' => sanitize_text_field((string) wp_unslash($pot['harvest_eta'] ?? '')),
        ];
    }
    if (function_exists('aitrongcay_replace_garden_pots')) {
        aitrongcay_replace_garden_pots($garden_key, $pots);
    }

    $redirect = add_query_arg([
        'page' => 'aitrongcay-garden-profile-tools',
        'garden_key' => rawurlencode($garden_key),
        'updated' => 'true',
    ], admin_url('themes.php'));
    wp_safe_redirect($redirect);
    exit;
}
add_action('admin_init', 'aitrongcay_handle_garden_profile_tools_admin_save');

function aitrongcay_render_garden_profile_tools_admin_page(): void
{
    if (! current_user_can('edit_theme_options')) {
        wp_die('Không đủ quyền.');
    }

    $gardens = aitrongcay_known_gardens_for_device_admin();
    $selected_key = sanitize_text_field((string) ($_GET['garden_key'] ?? ''));
    if ($selected_key === '' && ! empty($gardens[0]['garden_key'])) {
        $selected_key = (string) $gardens[0]['garden_key'];
    }

    $record = $selected_key !== '' && function_exists('aitrongcay_get_garden_record') ? (aitrongcay_get_garden_record($selected_key) ?: []) : [];
    $tools = $selected_key !== '' && function_exists('aitrongcay_get_db_tools') ? aitrongcay_get_db_tools($selected_key) : [];
    $pots = $selected_key !== '' && function_exists('aitrongcay_get_db_pots') ? aitrongcay_get_db_pots($selected_key) : [];
    $display_name = $selected_key !== '' && function_exists('aitrongcay_get_garden_display_name') ? aitrongcay_get_garden_display_name($selected_key) : '';
    if ($record === []) {
        $record = [
            'garden_code' => '',
            'garden_name' => $display_name,
            'summary' => '',
            'status_line' => '',
        ];
    }
    while (count($tools) < 6) {
        $tools[] = [
            'tool_key' => '',
            'name' => '',
            'type' => '',
            'description' => '',
            'owned' => 0,
            'qty' => 0,
            'image' => '',
        ];
    }
    while (count($pots) < 8) {
        $pots[] = [
            'pot_code' => '',
            'pot_name' => '',
            'status' => '',
            'status_summary' => '',
            'ph' => '',
            'temperature' => '',
            'humidity' => '',
            'light_label' => '',
            'light_device' => '',
            'pump_label' => '',
            'irrigation' => '',
            'image_url' => '',
            'video_url' => '',
            'ai_note' => '',
            'harvest_eta' => '',
        ];
    }
    ?>
    <div class="wrap">
        <h1>Hồ sơ & vật tư khu vườn</h1>
        <p>Trang này giúp nhập nhanh dữ liệu thật cho từng khu vườn: tên vườn, mã vườn, mô tả ngắn, trạng thái hiển thị và danh sách vật tư đang dùng.</p>
        <?php if (isset($_GET['updated'])) : ?>
            <div class="notice notice-success is-dismissible"><p>Đã lưu hồ sơ và vật tư khu vườn.</p></div>
        <?php endif; ?>

        <form method="get" style="margin:16px 0 24px;display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
            <input type="hidden" name="page" value="aitrongcay-garden-profile-tools">
            <label for="garden_key_picker"><strong>Chọn khu vườn</strong></label>
            <select id="garden_key_picker" name="garden_key" style="min-width:360px;">
                <?php foreach ($gardens as $garden) : $garden_key = (string) ($garden['garden_key'] ?? ''); ?>
                    <option value="<?php echo esc_attr($garden_key); ?>" <?php selected($selected_key, $garden_key); ?>>
                        <?php echo esc_html((string) ($garden['label'] ?? $garden_key)); ?> — <?php echo esc_html($garden_key); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button class="button button-secondary" type="submit">Mở khu vườn</button>
        </form>

        <?php if ($selected_key === '') : ?>
            <div class="notice notice-warning"><p>Chưa có khu vườn nào để chỉnh.</p></div>
        <?php else : ?>
            <form method="post">
                <?php wp_nonce_field('aitrongcay_save_garden_profile_tools'); ?>
                <input type="hidden" name="action" value="aitrongcay_save_garden_profile_tools">
                <input type="hidden" name="garden_key" value="<?php echo esc_attr($selected_key); ?>">

                <div style="background:#fff;border:1px solid #dcdcde;border-radius:12px;padding:20px;margin:0 0 20px;max-width:1100px;">
                    <h2 style="margin-top:0">Hồ sơ khu vườn</h2>
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row"><label for="garden_code">Mã vườn</label></th>
                                <td><input class="regular-text" id="garden_code" name="garden_code" value="<?php echo esc_attr((string) ($record['garden_code'] ?? '')); ?>"></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="garden_name">Tên vườn</label></th>
                                <td><input class="regular-text" id="garden_name" name="garden_name" value="<?php echo esc_attr((string) ($record['garden_name'] ?? '')); ?>"></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="status_line">Dòng trạng thái</label></th>
                                <td><input class="regular-text" id="status_line" name="status_line" value="<?php echo esc_attr((string) ($record['status_line'] ?? '')); ?>"><p class="description">Ví dụ: 4 khay • đang theo dõi</p></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="summary">Mô tả ngắn</label></th>
                                <td><textarea id="summary" name="summary" rows="4" class="large-text"><?php echo esc_textarea((string) ($record['summary'] ?? '')); ?></textarea></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div style="background:#fff;border:1px solid #dcdcde;border-radius:12px;padding:20px;margin:0 0 20px;max-width:1100px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
                        <div>
                            <h2 style="margin:0">Vật tư khu vườn</h2>
                            <p style="margin:8px 0 0">Điền mỗi dòng là một vật tư. Bỏ trống tên thì hệ thống sẽ bỏ qua dòng đó khi lưu.</p>
                        </div>
                        <button class="button button-secondary" type="button" data-add-tool-row>+ Thêm dòng vật tư</button>
                    </div>
                    <table class="widefat striped" data-tools-table>
                        <thead>
                            <tr>
                                <th style="width:120px">Mã</th>
                                <th style="width:180px">Tên vật tư</th>
                                <th style="width:140px">Loại</th>
                                <th>Mô tả</th>
                                <th style="width:90px">Sở hữu</th>
                                <th style="width:90px">SL</th>
                                <th style="width:140px">Ảnh</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tools as $index => $tool) : ?>
                                <tr data-tool-row>
                                    <td><input class="regular-text code" name="tools[<?php echo esc_attr((string) $index); ?>][tool_key]" value="<?php echo esc_attr((string) ($tool['tool_key'] ?? '')); ?>"></td>
                                    <td><input class="regular-text" name="tools[<?php echo esc_attr((string) $index); ?>][name]" value="<?php echo esc_attr((string) ($tool['name'] ?? '')); ?>"></td>
                                    <td><input class="regular-text" name="tools[<?php echo esc_attr((string) $index); ?>][type]" value="<?php echo esc_attr((string) ($tool['type'] ?? '')); ?>"></td>
                                    <td><textarea name="tools[<?php echo esc_attr((string) $index); ?>][description]" rows="2" class="large-text"><?php echo esc_textarea((string) ($tool['description'] ?? '')); ?></textarea></td>
                                    <td><input type="number" min="0" step="1" name="tools[<?php echo esc_attr((string) $index); ?>][owned]" value="<?php echo esc_attr((string) ((int) ($tool['owned'] ?? 0))); ?>"></td>
                                    <td><input type="number" min="0" step="1" name="tools[<?php echo esc_attr((string) $index); ?>][qty]" value="<?php echo esc_attr((string) ((int) ($tool['qty'] ?? 0))); ?>"></td>
                                    <td><div style="display:flex;gap:8px;align-items:flex-start"><input class="regular-text" name="tools[<?php echo esc_attr((string) $index); ?>][image]" value="<?php echo esc_attr((string) ($tool['image'] ?? '')); ?>"><button class="button-link-delete" type="button" data-remove-row>Xóa</button></div></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div style="background:#fff;border:1px solid #dcdcde;border-radius:12px;padding:20px;margin:0 0 20px;max-width:1280px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
                        <div>
                            <h2 style="margin:0">Khay / Pots của khu vườn</h2>
                            <p style="margin:8px 0 0">Điền mỗi dòng là một khay thật. Bỏ trống mã khay hoặc tên khay thì dòng đó sẽ bị bỏ qua khi lưu.</p>
                        </div>
                        <button class="button button-secondary" type="button" data-add-pot-row>+ Thêm dòng khay</button>
                    </div>
                    <table class="widefat striped" data-pots-table>
                        <thead>
                            <tr>
                                <th style="width:90px">Mã khay</th>
                                <th style="width:140px">Tên khay</th>
                                <th style="width:120px">Trạng thái</th>
                                <th>Mô tả ngắn</th>
                                <th style="width:70px">pH</th>
                                <th style="width:90px">Nhiệt độ</th>
                                <th style="width:80px">Độ ẩm</th>
                                <th style="width:120px">Đèn</th>
                                <th style="width:90px">light_device</th>
                                <th style="width:120px">Bơm</th>
                                <th style="width:120px">Tưới</th>
                                <th style="width:130px">Harvest ETA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pots as $index => $pot) : ?>
                                <tr data-pot-row-main>
                                    <td><input class="regular-text code" name="pots[<?php echo esc_attr((string) $index); ?>][pot_code]" value="<?php echo esc_attr((string) ($pot['pot_code'] ?? '')); ?>"></td>
                                    <td><input class="regular-text" name="pots[<?php echo esc_attr((string) $index); ?>][pot_name]" value="<?php echo esc_attr((string) ($pot['pot_name'] ?? '')); ?>"></td>
                                    <td><input class="regular-text" name="pots[<?php echo esc_attr((string) $index); ?>][status]" value="<?php echo esc_attr((string) ($pot['status'] ?? '')); ?>"></td>
                                    <td><textarea name="pots[<?php echo esc_attr((string) $index); ?>][status_summary]" rows="2" class="large-text"><?php echo esc_textarea((string) ($pot['status_summary'] ?? '')); ?></textarea></td>
                                    <td><input class="small-text" name="pots[<?php echo esc_attr((string) $index); ?>][ph]" value="<?php echo esc_attr((string) ($pot['ph'] ?? '')); ?>"></td>
                                    <td><input class="regular-text" name="pots[<?php echo esc_attr((string) $index); ?>][temperature]" value="<?php echo esc_attr((string) ($pot['temperature'] ?? '')); ?>"></td>
                                    <td><input class="regular-text" name="pots[<?php echo esc_attr((string) $index); ?>][humidity]" value="<?php echo esc_attr((string) ($pot['humidity'] ?? '')); ?>"></td>
                                    <td><input class="regular-text" name="pots[<?php echo esc_attr((string) $index); ?>][light_label]" value="<?php echo esc_attr((string) ($pot['light_label'] ?? '')); ?>"></td>
                                    <td><input class="regular-text code" name="pots[<?php echo esc_attr((string) $index); ?>][light_device]" value="<?php echo esc_attr((string) ($pot['light_device'] ?? '')); ?>"></td>
                                    <td><input class="regular-text" name="pots[<?php echo esc_attr((string) $index); ?>][pump_label]" value="<?php echo esc_attr((string) ($pot['pump_label'] ?? '')); ?>"></td>
                                    <td><input class="regular-text" name="pots[<?php echo esc_attr((string) $index); ?>][irrigation]" value="<?php echo esc_attr((string) ($pot['irrigation'] ?? '')); ?>"></td>
                                    <td><div style="display:flex;gap:8px;align-items:flex-start"><input class="regular-text" name="pots[<?php echo esc_attr((string) $index); ?>][harvest_eta]" value="<?php echo esc_attr((string) ($pot['harvest_eta'] ?? '')); ?>"><button class="button-link-delete" type="button" data-remove-pot-row>Xóa</button></div></td>
                                </tr>
                                <tr data-pot-row-extra>
                                    <td colspan="6"><label><strong>AI note</strong><br><textarea name="pots[<?php echo esc_attr((string) $index); ?>][ai_note]" rows="2" class="large-text"><?php echo esc_textarea((string) ($pot['ai_note'] ?? '')); ?></textarea></label></td>
                                    <td colspan="3"><label><strong>Image URL</strong><br><input class="large-text" name="pots[<?php echo esc_attr((string) $index); ?>][image_url]" value="<?php echo esc_attr((string) ($pot['image_url'] ?? '')); ?>"></label></td>
                                    <td colspan="3"><label><strong>Video URL</strong><br><input class="large-text" name="pots[<?php echo esc_attr((string) $index); ?>][video_url]" value="<?php echo esc_attr((string) ($pot['video_url'] ?? '')); ?>"></label></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php submit_button('Lưu hồ sơ, vật tư & khay khu vườn'); ?>
            </form>

            <script>
            (function () {
              const toolsTable = document.querySelector('[data-tools-table] tbody');
              const potsTable = document.querySelector('[data-pots-table] tbody');
              const addToolBtn = document.querySelector('[data-add-tool-row]');
              const addPotBtn = document.querySelector('[data-add-pot-row]');

              function reindexTools() {
                if (!toolsTable) return;
                Array.from(toolsTable.querySelectorAll('[data-tool-row]')).forEach((row, index) => {
                  row.querySelectorAll('input, textarea').forEach((field) => {
                    field.name = field.name.replace(/tools\[\d+\]/, `tools[${index}]`);
                  });
                });
              }

              function reindexPots() {
                if (!potsTable) return;
                const mains = Array.from(potsTable.querySelectorAll('[data-pot-row-main]'));
                mains.forEach((mainRow, index) => {
                  const extraRow = mainRow.nextElementSibling;
                  [mainRow, extraRow].forEach((row) => {
                    if (!row) return;
                    row.querySelectorAll('input, textarea').forEach((field) => {
                      field.name = field.name.replace(/pots\[\d+\]/, `pots[${index}]`);
                    });
                  });
                });
              }

              if (addToolBtn && toolsTable) {
                addToolBtn.addEventListener('click', () => {
                  const index = toolsTable.querySelectorAll('[data-tool-row]').length;
                  const tr = document.createElement('tr');
                  tr.setAttribute('data-tool-row', '1');
                  tr.innerHTML = `
                    <td><input class="regular-text code" name="tools[${index}][tool_key]"></td>
                    <td><input class="regular-text" name="tools[${index}][name]"></td>
                    <td><input class="regular-text" name="tools[${index}][type]"></td>
                    <td><textarea name="tools[${index}][description]" rows="2" class="large-text"></textarea></td>
                    <td><input type="number" min="0" step="1" name="tools[${index}][owned]" value="0"></td>
                    <td><input type="number" min="0" step="1" name="tools[${index}][qty]" value="0"></td>
                    <td><div style="display:flex;gap:8px;align-items:flex-start"><input class="regular-text" name="tools[${index}][image]"><button class="button-link-delete" type="button" data-remove-row>Xóa</button></div></td>`;
                  toolsTable.appendChild(tr);
                });

                toolsTable.addEventListener('click', (event) => {
                  const btn = event.target.closest('[data-remove-row]');
                  if (!btn) return;
                  const row = btn.closest('[data-tool-row]');
                  if (row) row.remove();
                  reindexTools();
                });
              }

              if (addPotBtn && potsTable) {
                addPotBtn.addEventListener('click', () => {
                  const index = potsTable.querySelectorAll('[data-pot-row-main]').length;
                  const main = document.createElement('tr');
                  main.setAttribute('data-pot-row-main', '1');
                  main.innerHTML = `
                    <td><input class="regular-text code" name="pots[${index}][pot_code]"></td>
                    <td><input class="regular-text" name="pots[${index}][pot_name]"></td>
                    <td><input class="regular-text" name="pots[${index}][status]"></td>
                    <td><textarea name="pots[${index}][status_summary]" rows="2" class="large-text"></textarea></td>
                    <td><input class="small-text" name="pots[${index}][ph]"></td>
                    <td><input class="regular-text" name="pots[${index}][temperature]"></td>
                    <td><input class="regular-text" name="pots[${index}][humidity]"></td>
                    <td><input class="regular-text" name="pots[${index}][light_label]"></td>
                    <td><input class="regular-text code" name="pots[${index}][light_device]"></td>
                    <td><input class="regular-text" name="pots[${index}][pump_label]"></td>
                    <td><input class="regular-text" name="pots[${index}][irrigation]"></td>
                    <td><div style="display:flex;gap:8px;align-items:flex-start"><input class="regular-text" name="pots[${index}][harvest_eta]"><button class="button-link-delete" type="button" data-remove-pot-row>Xóa</button></div></td>`;
                  const extra = document.createElement('tr');
                  extra.setAttribute('data-pot-row-extra', '1');
                  extra.innerHTML = `
                    <td colspan="6"><label><strong>AI note</strong><br><textarea name="pots[${index}][ai_note]" rows="2" class="large-text"></textarea></label></td>
                    <td colspan="3"><label><strong>Image URL</strong><br><input class="large-text" name="pots[${index}][image_url]"></label></td>
                    <td colspan="3"><label><strong>Video URL</strong><br><input class="large-text" name="pots[${index}][video_url]"></label></td>`;
                  potsTable.appendChild(main);
                  potsTable.appendChild(extra);
                });

                potsTable.addEventListener('click', (event) => {
                  const btn = event.target.closest('[data-remove-pot-row]');
                  if (!btn) return;
                  const main = btn.closest('[data-pot-row-main]');
                  const extra = main ? main.nextElementSibling : null;
                  if (main) main.remove();
                  if (extra && extra.matches('[data-pot-row-extra]')) extra.remove();
                  reindexPots();
                });
              }
            })();
            </script>
        <?php endif; ?>
    </div>
    <?php
}

function aitrongcay_render_device_mapping_admin_page(): void
{
    if (! current_user_can('edit_theme_options')) {
        wp_die('Không đủ quyền.');
    }

    $schema = aitrongcay_blynk_device_schema();
    $saved = aitrongcay_get_saved_blynk_configs();
    $builtins = aitrongcay_blynk_builtin_configs();
    $gardens = aitrongcay_known_gardens_for_device_admin();
    ?>
    <div class="wrap">
        <h1>Mapping thiết bị theo khu vườn</h1>
        <p>Trang này dùng để khai báo token thật, VPin và mapping thiết bị theo từng <code>garden_key</code>. Mô hình thực tế hiện hỗ trợ 1 token chung cho sensor + bơm + nhiều khay cùng lúc; mỗi khay vẫn map riêng sang đèn <code>light1..light4</code> và chỉ cần nhập token riêng khi thật sự khác token chung.</p>
        <?php if (isset($_GET['updated'])) : ?>
            <div class="notice notice-success is-dismissible"><p>Đã lưu mapping thiết bị khu vườn.</p></div>
        <?php endif; ?>
        <form method="post">
            <?php wp_nonce_field('aitrongcay_save_device_mapping'); ?>
            <input type="hidden" name="action" value="aitrongcay_save_device_mapping">
            <?php foreach ($gardens as $garden) :
                $garden_key = (string) ($garden['garden_key'] ?? '');
                $dataset_key = (string) ($garden['dataset_key'] ?? '');
                $config = $saved[$garden_key] ?? ($builtins[$dataset_key] ?? aitrongcay_blynk_default_config());
                $token_mask = trim((string) ($config['token'] ?? '')) !== '' ? str_repeat('•', max(8, strlen((string) $config['token']))) : 'Chưa khai báo';
                ?>
                <div style="background:#fff;border:1px solid #dcdcde;border-radius:12px;padding:20px;margin:0 0 20px;max-width:1280px;">
                    <h2 style="margin-top:0"><?php echo esc_html((string) ($garden['label'] ?? $garden_key)); ?></h2>
                    <p style="margin-top:0;color:#50575e">
                        <strong>garden_key:</strong> <code><?php echo esc_html($garden_key); ?></code>
                        <?php if (! empty($garden['owner_email'])) : ?> · <strong>owner:</strong> <?php echo esc_html((string) $garden['owner_email']); ?><?php endif; ?>
                        <?php if ($dataset_key !== '') : ?> · <strong>dataset:</strong> <code><?php echo esc_html($dataset_key); ?></code><?php endif; ?>
                    </p>
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row"><label for="base-<?php echo esc_attr($garden_key); ?>"><?php echo esc_html((string) $schema['base']['label']); ?></label></th>
                                <td><input class="regular-text code" id="base-<?php echo esc_attr($garden_key); ?>" name="garden_configs[<?php echo esc_attr($garden_key); ?>][base]" value="<?php echo esc_attr((string) ($config['base'] ?? '')); ?>"></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="token-<?php echo esc_attr($garden_key); ?>"><?php echo esc_html((string) $schema['token']['label']); ?></label></th>
                                <td>
                                    <input class="regular-text code" id="token-<?php echo esc_attr($garden_key); ?>" name="garden_configs[<?php echo esc_attr($garden_key); ?>][token]" value="<?php echo esc_attr((string) ($config['token'] ?? '')); ?>" autocomplete="off" spellcheck="false">
                                    <p class="description">Hiện tại: <?php echo esc_html($token_mask); ?>. Token này dùng cho cảm biến chung + bơm và cũng có thể dùng luôn cho nhiều khay/đèn. Ở phần token theo khay, có thể để trống hoặc nhập <code>__shared__</code>/<code>shared-token</code> để dùng lại token chung; hệ thống không ép token phải unique giữa các khay.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px;">
                        <div>
                            <h3>VPin cảm biến & điều khiển</h3>
                            <?php foreach ((array) ($schema['vpins'] ?? []) as $field_key => $field) : ?>
                                <p>
                                    <label for="vpin-<?php echo esc_attr($garden_key . '-' . $field_key); ?>"><strong><?php echo esc_html((string) ($field['label'] ?? $field_key)); ?></strong></label><br>
                                    <input class="regular-text code" id="vpin-<?php echo esc_attr($garden_key . '-' . $field_key); ?>" name="garden_configs[<?php echo esc_attr($garden_key); ?>][vpins][<?php echo esc_attr($field_key); ?>]" value="<?php echo esc_attr((string) (($config['vpins'][$field_key] ?? ''))); ?>">
                                </p>
                            <?php endforeach; ?>
                        </div>
                        <div>
                            <h3>Khóa thiết bị logic</h3>
                            <?php foreach ((array) ($schema['devices'] ?? []) as $field_key => $field) : ?>
                                <p>
                                    <label for="device-<?php echo esc_attr($garden_key . '-' . $field_key); ?>"><strong><?php echo esc_html((string) ($field['label'] ?? $field_key)); ?></strong></label><br>
                                    <input class="regular-text code" id="device-<?php echo esc_attr($garden_key . '-' . $field_key); ?>" name="garden_configs[<?php echo esc_attr($garden_key); ?>][devices][<?php echo esc_attr($field_key); ?>]" value="<?php echo esc_attr((string) (($config['devices'][$field_key] ?? ''))); ?>">
                                </p>
                            <?php endforeach; ?>
                        </div>
                        <div>
                            <h3>Map khay → thiết bị đèn</h3>
                            <?php foreach ((array) ($schema['pots'] ?? []) as $field_key => $field) : ?>
                                <p>
                                    <label for="pot-<?php echo esc_attr($garden_key . '-' . $field_key); ?>"><strong><?php echo esc_html((string) ($field['label'] ?? $field_key)); ?></strong></label><br>
                                    <input class="regular-text code" id="pot-<?php echo esc_attr($garden_key . '-' . $field_key); ?>" name="garden_configs[<?php echo esc_attr($garden_key); ?>][pots][<?php echo esc_attr($field_key); ?>]" value="<?php echo esc_attr((string) (($config['pots'][$field_key] ?? ''))); ?>">
                                </p>
                            <?php endforeach; ?>
                        </div>
                        <div>
                            <h3>Token riêng theo khay</h3>
                            <?php foreach ((array) ($schema['pot_tokens'] ?? []) as $field_key => $field) :
                                $masked = trim((string) ($config['pot_tokens'][$field_key] ?? '')) !== '' ? str_repeat('•', max(8, strlen((string) $config['pot_tokens'][$field_key]))) : 'Chưa khai báo';
                                ?>
                                <p>
                                    <label for="pot-token-<?php echo esc_attr($garden_key . '-' . $field_key); ?>"><strong><?php echo esc_html((string) ($field['label'] ?? $field_key)); ?></strong></label><br>
                                    <input class="regular-text code" id="pot-token-<?php echo esc_attr($garden_key . '-' . $field_key); ?>" name="garden_configs[<?php echo esc_attr($garden_key); ?>][pot_tokens][<?php echo esc_attr($field_key); ?>]" value="<?php echo esc_attr((string) (($config['pot_tokens'][$field_key] ?? ''))); ?>" autocomplete="off" spellcheck="false">
                                    <span class="description" style="display:block">Hiện tại: <?php echo esc_html($masked); ?>. Để trống hoặc nhập <code>__shared__</code> nếu khay này dùng cùng token chung.</span>
                                </p>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php submit_button('Lưu mapping thiết bị'); ?>
        </form>
    </div>
    <?php
}
