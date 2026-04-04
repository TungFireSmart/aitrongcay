<?php

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

require_once get_template_directory() . '/inc/site-config.php';
require_once get_template_directory() . '/inc/virtual-pages.php';
require_once get_template_directory() . '/inc/homepage-settings.php';
require_once get_template_directory() . '/inc/page-content-settings.php';
require_once get_template_directory() . '/inc/portal-garden-data.php';
require_once get_template_directory() . '/inc/portal-device-mapping-admin.php';

function aitrongcay_theme_setup(): void
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('custom-logo');
    add_theme_support('editor-styles');
    add_theme_support('responsive-embeds');

    register_nav_menus([
        'primary' => __('Primary Menu', 'aitrongcay'),
        'footer_public' => __('Footer Public Menu', 'aitrongcay'),
        'footer_trust' => __('Footer Trust Menu', 'aitrongcay'),
        'footer_start' => __('Footer Start Menu', 'aitrongcay'),
    ]);
}
add_action('after_setup_theme', 'aitrongcay_theme_setup');

function aitrongcay_register_post_types(): void
{
    register_post_type('aitr_consultation', [
        'labels' => [
            'name' => __('Đăng ký tư vấn', 'aitrongcay'),
            'singular_name' => __('Đăng ký tư vấn', 'aitrongcay'),
            'menu_name' => __('Leads tư vấn', 'aitrongcay'),
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-sprout',
        'supports' => ['title', 'editor', 'custom-fields'],
    ]);

    register_post_type('aitr_market_post', [
        'labels' => [
            'name' => __('Tin Chợ quê', 'aitrongcay'),
            'singular_name' => __('Tin Chợ quê', 'aitrongcay'),
            'menu_name' => __('Tin Chợ quê', 'aitrongcay'),
        ],
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-store',
        'supports' => ['title', 'editor', 'thumbnail', 'author', 'custom-fields'],
        'has_archive' => false,
        'rewrite' => false,
        'query_var' => true,
        'show_in_rest' => true,
    ]);
}
add_action('init', 'aitrongcay_register_post_types');

function aitrongcay_enqueue_assets(): void
{
    $theme = wp_get_theme();
    $css_rel_path = '/assets/css/styles.css';
    $js_rel_path = '/assets/js/main.js';
    $css_abs_path = get_template_directory() . $css_rel_path;
    $js_abs_path = get_template_directory() . $js_rel_path;
    $css_version = file_exists($css_abs_path) ? (string) filemtime($css_abs_path) : $theme->get('Version');
    $js_version = file_exists($js_abs_path) ? (string) filemtime($js_abs_path) : $theme->get('Version');

    wp_enqueue_style(
        'aitrongcay-fonts',
        'https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap',
        [],
        null
    );

    wp_enqueue_style(
        'aitrongcay-theme',
        get_template_directory_uri() . $css_rel_path,
        ['aitrongcay-fonts'],
        $css_version
    );

    wp_enqueue_script(
        'aitrongcay-theme',
        get_template_directory_uri() . $js_rel_path,
        [],
        $js_version,
        true
    );

    wp_localize_script('aitrongcay-theme', 'aitrongcayTheme', [
        'rootUrl' => home_url('/'),
        'portalUrl' => home_url('/portal/'),
        'authUrl' => home_url('/dang-nhap/'),
        'signupUrl' => home_url('/dang-ky-tu-van/'),
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'ajaxNonce' => wp_create_nonce('aitrongcay_portal_actions'),
        'gardenAssistantEnabled' => true,
        'gardenAssistantMode' => 'adapter-ready',
        'nav' => array_map(
            static fn(array $item): array => [
                'label' => $item['label'],
                'url' => $item['url'],
            ],
            aitrongcay_primary_nav_items()
        ),
    ]);
}
add_action('wp_enqueue_scripts', 'aitrongcay_enqueue_assets');

function aitrongcay_body_classes(array $classes): array
{
    if (is_page_template('templates/page-portal.php')) {
        $classes[] = 'page-portal';
    }

    return $classes;
}
add_filter('body_class', 'aitrongcay_body_classes');

function aitrongcay_consultation_action_url(): string
{
    return esc_url(admin_url('admin-post.php'));
}

function aitrongcay_consultation_notice(): ?array
{
    $status = isset($_GET['consultation_status']) ? sanitize_key((string) $_GET['consultation_status']) : '';
    if ($status === '') {
        return null;
    }

    return match ($status) {
        'success' => [
            'class' => 'notice success',
            'title' => 'Đã ghi nhận đăng ký tư vấn.',
            'body' => 'Bước tiếp theo là đội ngũ liên hệ ngắn để hiểu nhu cầu thật và chốt nhịp bắt đầu phù hợp.',
        ],
        'invalid' => [
            'class' => 'notice error',
            'title' => 'Thiếu thông tin cần thiết.',
            'body' => 'Anh/chị vui lòng để lại ít nhất họ tên và số điện thoại để bên em liên hệ tư vấn.',
        ],
        default => null,
    };
}

function aitrongcay_render_consultation_notice(): void
{
    $notice = aitrongcay_consultation_notice();
    if (! is_array($notice)) {
        return;
    }

    printf(
        '<div class="%1$s" style="margin-bottom:16px"><strong style="display:block;margin-bottom:6px">%2$s</strong><span>%3$s</span></div>',
        esc_attr($notice['class']),
        esc_html($notice['title']),
        esc_html($notice['body'])
    );
}

function aitrongcay_consultation_notification_email(): string
{
    return 'tung@pccc.vn';
}

function aitrongcay_send_consultation_notification(array $payload, int $post_id = 0): void
{
    $admin_email = aitrongcay_consultation_notification_email();
    if (! is_email($admin_email)) {
        return;
    }

    $subject = sprintf('[Ai trồng cây] Lead tư vấn mới: %s — %s', $payload['full_name'], $payload['phone']);
    $lines = [
        'Có một đăng ký tư vấn mới từ website Ai trồng cây.',
        '',
        'Họ tên: ' . $payload['full_name'],
        'Số điện thoại: ' . $payload['phone'],
        'Email: ' . ($payload['email'] !== '' ? $payload['email'] : 'Không có'),
        'Mục tiêu đầu tiên: ' . ($payload['goal'] !== '' ? $payload['goal'] : 'Chưa ghi rõ'),
        'Mốc bắt đầu mong muốn: ' . ($payload['start_window'] !== '' ? $payload['start_window'] : 'Chưa ghi rõ'),
        'Ghi chú thêm: ' . ($payload['focus'] !== '' ? $payload['focus'] : 'Không có'),
        'Funnel stage: ' . $payload['funnel_stage'],
        'Funnel source: ' . $payload['funnel_source'],
        'Thời gian (UTC): ' . gmdate('c'),
    ];

    if ($post_id > 0) {
        $lines[] = 'Lead admin: ' . admin_url('post.php?post=' . $post_id . '&action=edit');
    }

    wp_mail($admin_email, $subject, implode("\n", $lines));
}

function aitrongcay_handle_consultation_submission(): void
{
    if (! isset($_POST['aitrongcay_consultation_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['aitrongcay_consultation_nonce'])), 'aitrongcay_consultation_submit')) {
        wp_safe_redirect(add_query_arg('consultation_status', 'invalid', wp_get_referer() ?: home_url('/dang-ky-tu-van/')));
        exit;
    }

    $full_name = sanitize_text_field(wp_unslash($_POST['fullName'] ?? ''));
    $phone = sanitize_text_field(wp_unslash($_POST['phone'] ?? ''));
    $email = sanitize_email(wp_unslash($_POST['email'] ?? ''));
    $goal = sanitize_text_field(wp_unslash($_POST['goal'] ?? ''));
    $start_window = sanitize_text_field(wp_unslash($_POST['startWindow'] ?? ''));
    $focus = sanitize_textarea_field(wp_unslash($_POST['focus'] ?? ''));
    $funnel_stage = sanitize_text_field(wp_unslash($_POST['funnelStage'] ?? 'consultation'));
    $funnel_source = sanitize_text_field(wp_unslash($_POST['funnelSource'] ?? 'website'));
    $redirect_to = esc_url_raw(wp_unslash($_POST['redirect_to'] ?? home_url('/dang-ky-tu-van/')));

    if ($full_name === '' || $phone === '') {
        wp_safe_redirect(add_query_arg('consultation_status', 'invalid', $redirect_to));
        exit;
    }

    $payload = [
        'full_name' => $full_name,
        'phone' => $phone,
        'email' => $email,
        'goal' => $goal,
        'start_window' => $start_window,
        'focus' => $focus,
        'funnel_stage' => $funnel_stage,
        'funnel_source' => $funnel_source,
    ];

    $post_id = wp_insert_post([
        'post_type' => 'aitr_consultation',
        'post_status' => 'publish',
        'post_title' => sprintf('%s — %s', $full_name, $phone),
        'post_content' => implode("\n\n", array_filter([
            'Mục tiêu đầu tiên: ' . ($goal !== '' ? $goal : 'Chưa ghi rõ'),
            'Mốc bắt đầu mong muốn: ' . ($start_window !== '' ? $start_window : 'Chưa ghi rõ'),
            'Ghi chú thêm: ' . ($focus !== '' ? $focus : 'Không có'),
            'Funnel stage: ' . $funnel_stage,
            'Funnel source: ' . $funnel_source,
        ])),
    ], true);

    if (! is_wp_error($post_id) && is_int($post_id)) {
        update_post_meta($post_id, 'full_name', $full_name);
        update_post_meta($post_id, 'phone', $phone);
        update_post_meta($post_id, 'email', $email);
        update_post_meta($post_id, 'goal', $goal);
        update_post_meta($post_id, 'start_window', $start_window);
        update_post_meta($post_id, 'focus', $focus);
        update_post_meta($post_id, 'funnel_stage', $funnel_stage);
        update_post_meta($post_id, 'funnel_source', $funnel_source);
        update_post_meta($post_id, 'submitted_at_gmt', gmdate('c'));
        aitrongcay_send_consultation_notification($payload, $post_id);
    }

    wp_safe_redirect(add_query_arg('consultation_status', 'success', $redirect_to));
    exit;
}
add_action('admin_post_nopriv_aitrongcay_consultation_submit', 'aitrongcay_handle_consultation_submission');
add_action('admin_post_aitrongcay_consultation_submit', 'aitrongcay_handle_consultation_submission');

function aitrongcay_auth_notice(): ?array
{
    $status = isset($_GET['auth_status']) ? sanitize_key((string) $_GET['auth_status']) : '';
    if ($status === '') {
        return null;
    }

    return match ($status) {
        'login-required' => [
            'class' => 'notice error',
            'title' => 'Anh/chị vui lòng đăng nhập trước.',
            'body' => 'Khu vực này dành cho thành viên đã có tài khoản.',
        ],
        'login-error' => [
            'class' => 'notice error',
            'title' => 'Thông tin đăng nhập chưa đúng.',
            'body' => 'Anh/chị kiểm tra lại email và mật khẩu giúp em.',
        ],
        'register-invalid' => [
            'class' => 'notice error',
            'title' => 'Thiếu thông tin cần thiết.',
            'body' => 'Anh/chị vui lòng điền họ tên, email và mật khẩu để tạo tài khoản.',
        ],
        'register-exists' => [
            'class' => 'notice error',
            'title' => 'Email này đã có tài khoản.',
            'body' => 'Anh/chị có thể đăng nhập luôn bằng email đó.',
        ],
        'register-success' => [
            'class' => 'notice success',
            'title' => 'Tài khoản đã được tạo.',
            'body' => 'Anh/chị đã được đăng nhập ngay sau khi tạo tài khoản và có thể vào khu vườn của mình bây giờ.',
        ],
        'logged-out' => [
            'class' => 'notice success',
            'title' => 'Anh/chị đã đăng xuất.',
            'body' => 'Khi cần, anh/chị chỉ cần đăng nhập lại để vào khu vườn của mình.',
        ],
        default => null,
    };
}

function aitrongcay_render_auth_notice(): void
{
    $notice = aitrongcay_auth_notice();
    if (! is_array($notice)) {
        return;
    }

    printf(
        '<div class="%1$s" style="margin-bottom:16px"><strong style="display:block;margin-bottom:6px">%2$s</strong><span>%3$s</span></div>',
        esc_attr($notice['class']),
        esc_html($notice['title']),
        esc_html($notice['body'])
    );
}

function aitrongcay_login_action_url(): string
{
    return esc_url(admin_url('admin-post.php'));
}

function aitrongcay_account_notice(): ?array
{
    $status = isset($_GET['account_status']) ? sanitize_key((string) $_GET['account_status']) : '';
    if ($status === '') {
        return null;
    }

    return match ($status) {
        'updated' => [
            'class' => 'notice success',
            'title' => 'Đã cập nhật tài khoản.',
            'body' => 'Thông tin cơ bản của anh/chị đã được lưu.',
        ],
        'password-updated' => [
            'class' => 'notice success',
            'title' => 'Đã đổi mật khẩu.',
            'body' => 'Mật khẩu mới đã có hiệu lực cho lần đăng nhập tiếp theo.',
        ],
        'avatar-updated' => [
            'class' => 'notice success',
            'title' => 'Đã cập nhật ảnh đại diện.',
            'body' => 'Ảnh đại diện mới của anh/chị đã được lưu.',
        ],
        'password-mismatch' => [
            'class' => 'notice error',
            'title' => 'Mật khẩu xác nhận chưa khớp.',
            'body' => 'Anh/chị nhập lại giúp em để tránh sai sót.',
        ],
        'invalid-email' => [
            'class' => 'notice error',
            'title' => 'Email chưa hợp lệ.',
            'body' => 'Anh/chị kiểm tra lại email trước khi lưu.',
        ],
        'email-pending' => [
            'class' => 'notice success',
            'title' => 'Đã ghi nhận yêu cầu đổi email.',
            'body' => 'Anh/chị kiểm tra email mới và bấm link xác nhận để hoàn tất.',
        ],
        'email-confirmed' => [
            'class' => 'notice success',
            'title' => 'Email đã được xác nhận.',
            'body' => 'Địa chỉ email mới đã có hiệu lực cho tài khoản này.',
        ],
        'reset-sent' => [
            'class' => 'notice success',
            'title' => 'Đã gửi email đặt lại mật khẩu.',
            'body' => 'Anh/chị kiểm tra hộp thư để tiếp tục.',
        ],
        'avatar-removed' => [
            'class' => 'notice success',
            'title' => 'Đã xóa ảnh đại diện.',
            'body' => 'Tài khoản đã quay về avatar mặc định.',
        ],
        default => null,
    };
}

function aitrongcay_render_account_notice(): void
{
    $notice = aitrongcay_account_notice();
    if (! is_array($notice)) {
        return;
    }

    printf(
        '<div class="%1$s" style="margin-bottom:16px"><strong style="display:block;margin-bottom:6px">%2$s</strong><span>%3$s</span></div>',
        esc_attr($notice['class']),
        esc_html($notice['title']),
        esc_html($notice['body'])
    );
}

function aitrongcay_admin_display_page_title($title, $post_id = 0)
{
    if (! is_admin()) {
        return $title;
    }

    if ((int) $post_id > 0) {
        $post = get_post($post_id);
        if ($post instanceof WP_Post && $post->post_type === 'page' && $post->post_name === 'cach-hoat-dong') {
            return 'Giới thiệu';
        }
    }

    return $title;
}
add_filter('the_title', 'aitrongcay_admin_display_page_title', 10, 2);

function aitrongcay_generate_username_from_email(string $email): string
{
    $base = sanitize_user((string) strstr($email, '@', true), true);
    if ($base === '') {
        $base = 'vuon';
    }

    $username = $base;
    $i = 1;
    while (username_exists($username)) {
        $username = $base . $i;
        $i++;
    }

    return $username;
}

function aitrongcay_resolve_login_identity(string $identity): string
{
    if (is_email($identity)) {
        $user = get_user_by('email', $identity);
        if ($user instanceof WP_User) {
            return $user->user_login;
        }
    }

    return $identity;
}

function aitrongcay_handle_register_submission(): void
{
    $redirect_to = esc_url_raw(wp_unslash($_POST['redirect_to'] ?? home_url('/onboarding/')));

    if (! isset($_POST['aitrongcay_register_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['aitrongcay_register_nonce'])), 'aitrongcay_register_submit')) {
        wp_safe_redirect(add_query_arg('auth_status', 'register-invalid', $redirect_to));
        exit;
    }

    $full_name = sanitize_text_field(wp_unslash($_POST['full_name'] ?? ''));
    $email = sanitize_email(wp_unslash($_POST['email'] ?? ''));
    $password = (string) wp_unslash($_POST['password'] ?? '');

    if ($full_name === '' || ! is_email($email) || $password === '') {
        wp_safe_redirect(add_query_arg('auth_status', 'register-invalid', $redirect_to));
        exit;
    }

    if (email_exists($email)) {
        wp_safe_redirect(add_query_arg('auth_status', 'register-exists', home_url('/dang-nhap/')));
        exit;
    }

    $username = aitrongcay_generate_username_from_email($email);
    $user_id = wp_create_user($username, $password, $email);
    if (is_wp_error($user_id)) {
        wp_safe_redirect(add_query_arg('auth_status', 'register-invalid', $redirect_to));
        exit;
    }

    wp_update_user([
        'ID' => $user_id,
        'display_name' => $full_name,
        'first_name' => $full_name,
    ]);

    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id, true);

    wp_safe_redirect(add_query_arg('auth_status', 'register-success', home_url('/portal/dashboard/')));
    exit;
}
add_action('admin_post_nopriv_aitrongcay_register_submit', 'aitrongcay_handle_register_submission');
add_action('admin_post_aitrongcay_register_submit', 'aitrongcay_handle_register_submission');

function aitrongcay_handle_login_submission(): void
{
    $redirect_to = esc_url_raw(wp_unslash($_POST['redirect_to'] ?? home_url('/portal/dashboard/')));

    if (! isset($_POST['aitrongcay_login_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['aitrongcay_login_nonce'])), 'aitrongcay_login_submit')) {
        wp_safe_redirect(add_query_arg('auth_status', 'login-error', home_url('/dang-nhap/')));
        exit;
    }

    $identity = sanitize_text_field(wp_unslash($_POST['identity'] ?? ''));
    $password = (string) wp_unslash($_POST['password'] ?? '');
    $remember = isset($_POST['remember']) && wp_unslash($_POST['remember']) === '1';

    if ($identity === '' || $password === '') {
        wp_safe_redirect(add_query_arg('auth_status', 'login-error', home_url('/dang-nhap/')));
        exit;
    }

    $user = wp_signon([
        'user_login' => aitrongcay_resolve_login_identity($identity),
        'user_password' => $password,
        'remember' => $remember,
    ], is_ssl());

    if (is_wp_error($user)) {
        wp_safe_redirect(add_query_arg('auth_status', 'login-error', home_url('/dang-nhap/')));
        exit;
    }

    wp_safe_redirect($redirect_to);
    exit;
}
add_action('admin_post_nopriv_aitrongcay_login_submit', 'aitrongcay_handle_login_submission');
add_action('admin_post_aitrongcay_login_submit', 'aitrongcay_handle_login_submission');

function aitrongcay_is_backend_admin_user($user = null): bool
{
    if ($user instanceof WP_User) {
        return $user->has_cap('edit_theme_options') || $user->has_cap('manage_options');
    }

    return current_user_can('edit_theme_options') || current_user_can('manage_options');
}

function aitrongcay_login_redirect(string $redirect_to, string $requested_redirect_to, $user): string
{
    if (! ($user instanceof WP_User)) {
        return $redirect_to;
    }

    if (aitrongcay_is_backend_admin_user($user)) {
        return $redirect_to;
    }

    $requested = trim($requested_redirect_to);
    if ($requested !== '') {
        $requested_path = wp_parse_url($requested, PHP_URL_PATH);
        if (is_string($requested_path) && $requested_path !== '' && strpos($requested_path, '/wp-admin') !== 0) {
            return $requested;
        }
    }

    return home_url('/portal/dashboard/');
}
add_filter('login_redirect', 'aitrongcay_login_redirect', 10, 3);

function aitrongcay_block_wp_admin_for_frontend_users(): void
{
    if (! is_user_logged_in() || aitrongcay_is_backend_admin_user()) {
        return;
    }

    if (wp_doing_ajax()) {
        return;
    }

    $script = basename((string) ($_SERVER['PHP_SELF'] ?? ''));
    if (in_array($script, ['admin-post.php', 'async-upload.php'], true)) {
        return;
    }

    if (is_admin()) {
        wp_safe_redirect(home_url('/tai-khoan/'));
        exit;
    }
}
add_action('admin_init', 'aitrongcay_block_wp_admin_for_frontend_users');

function aitrongcay_show_admin_bar_for_backend_users(bool $show): bool
{
    if (aitrongcay_is_backend_admin_user()) {
        return $show;
    }

    return false;
}
add_filter('show_admin_bar', 'aitrongcay_show_admin_bar_for_backend_users');

function aitrongcay_handle_account_update(): void
{
    if (! is_user_logged_in()) {
        wp_safe_redirect(home_url('/dang-nhap/?auth_status=login-required'));
        exit;
    }

    check_admin_referer('aitrongcay_account_update_submit', 'aitrongcay_account_update_nonce');

    $user_id = get_current_user_id();
    $display_name = sanitize_text_field(wp_unslash($_POST['display_name'] ?? ''));
    $email = sanitize_email(wp_unslash($_POST['email'] ?? ''));
    $phone = sanitize_text_field(wp_unslash($_POST['phone'] ?? ''));
    $city = sanitize_text_field(wp_unslash($_POST['city'] ?? ''));
    $household = sanitize_text_field(wp_unslash($_POST['household'] ?? ''));
    $address_line = sanitize_text_field(wp_unslash($_POST['address_line'] ?? ''));
    $ward = sanitize_text_field(wp_unslash($_POST['ward'] ?? ''));
    $district = sanitize_text_field(wp_unslash($_POST['district'] ?? ''));
    $note = sanitize_textarea_field(wp_unslash($_POST['account_note'] ?? ''));
    $notify_email = isset($_POST['notify_email']) ? '1' : '0';
    $notify_sms = isset($_POST['notify_sms']) ? '1' : '0';
    $notify_harvest = isset($_POST['notify_harvest']) ? '1' : '0';

    if ($email !== '' && ! is_email($email)) {
        wp_safe_redirect(add_query_arg('account_status', 'invalid-email', home_url('/tai-khoan/')));
        exit;
    }

    $current = wp_get_current_user();
    wp_update_user([
        'ID' => $user_id,
        'display_name' => $display_name !== '' ? $display_name : $current->display_name,
        'first_name' => $display_name !== '' ? $display_name : $current->first_name,
    ]);

    if ($email !== '' && $email !== $current->user_email) {
        $token = wp_generate_password(32, false, false);
        update_user_meta($user_id, 'aitrongcay_pending_email', $email);
        update_user_meta($user_id, 'aitrongcay_pending_email_token', $token);
        $confirm_url = add_query_arg([
            'ait_confirm_email' => '1',
            'uid' => $user_id,
            'token' => $token,
        ], home_url('/tai-khoan/'));
        wp_mail($email, '[Ai trồng cây] Xác nhận email mới', "Anh/chị hãy bấm link sau để xác nhận email mới cho tài khoản:\n\n" . $confirm_url);
    }

    update_user_meta($user_id, 'aitrongcay_phone', $phone);
    update_user_meta($user_id, 'aitrongcay_city', $city);
    update_user_meta($user_id, 'aitrongcay_household', $household);
    update_user_meta($user_id, 'aitrongcay_address_line', $address_line);
    update_user_meta($user_id, 'aitrongcay_ward', $ward);
    update_user_meta($user_id, 'aitrongcay_district', $district);
    update_user_meta($user_id, 'aitrongcay_account_note', $note);
    update_user_meta($user_id, 'aitrongcay_notify_email', $notify_email);
    update_user_meta($user_id, 'aitrongcay_notify_sms', $notify_sms);
    update_user_meta($user_id, 'aitrongcay_notify_harvest', $notify_harvest);

    $status = ($email !== '' && $email !== $current->user_email) ? 'email-pending' : 'updated';
    wp_safe_redirect(add_query_arg('account_status', $status, home_url('/tai-khoan/')));
    exit;
}
add_action('admin_post_aitrongcay_account_update', 'aitrongcay_handle_account_update');

function aitrongcay_handle_email_confirmation(): void
{
    if (! isset($_GET['ait_confirm_email'], $_GET['uid'], $_GET['token'])) {
        return;
    }
    $user_id = absint($_GET['uid']);
    $token = sanitize_text_field((string) $_GET['token']);
    $saved_token = (string) get_user_meta($user_id, 'aitrongcay_pending_email_token', true);
    $pending_email = (string) get_user_meta($user_id, 'aitrongcay_pending_email', true);
    if ($user_id > 0 && $token !== '' && hash_equals($saved_token, $token) && is_email($pending_email)) {
        wp_update_user(['ID' => $user_id, 'user_email' => $pending_email]);
        delete_user_meta($user_id, 'aitrongcay_pending_email');
        delete_user_meta($user_id, 'aitrongcay_pending_email_token');
        wp_safe_redirect(add_query_arg('account_status', 'email-confirmed', home_url('/tai-khoan/')));
        exit;
    }
}
add_action('template_redirect', 'aitrongcay_handle_email_confirmation');

function aitrongcay_handle_account_password_update(): void
{
    if (! is_user_logged_in()) {
        wp_safe_redirect(home_url('/dang-nhap/?auth_status=login-required'));
        exit;
    }

    check_admin_referer('aitrongcay_account_password_submit', 'aitrongcay_account_password_nonce');

    $password = (string) wp_unslash($_POST['new_password'] ?? '');
    $confirm = (string) wp_unslash($_POST['confirm_password'] ?? '');

    if ($password === '' || $password !== $confirm) {
        wp_safe_redirect(add_query_arg('account_status', 'password-mismatch', home_url('/tai-khoan/#doi-mat-khau')));
        exit;
    }

    wp_set_password($password, get_current_user_id());
    wp_set_auth_cookie(get_current_user_id(), true);
    wp_safe_redirect(add_query_arg('account_status', 'password-updated', home_url('/tai-khoan/#doi-mat-khau')));
    exit;
}
add_action('admin_post_aitrongcay_account_password_update', 'aitrongcay_handle_account_password_update');

function aitrongcay_handle_account_avatar_update(): void
{
    if (! is_user_logged_in()) {
        wp_safe_redirect(home_url('/dang-nhap/?auth_status=login-required'));
        exit;
    }

    check_admin_referer('aitrongcay_account_avatar_submit', 'aitrongcay_account_avatar_nonce');

    if (empty($_FILES['avatar']) || ! is_array($_FILES['avatar']) || (int) ($_FILES['avatar']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        wp_safe_redirect(add_query_arg('account_status', 'updated', home_url('/tai-khoan/')));
        exit;
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $attachment_id = media_handle_upload('avatar', 0);
    if (! is_wp_error($attachment_id) && $attachment_id) {
        update_user_meta(get_current_user_id(), 'aitrongcay_avatar_id', (int) $attachment_id);
        update_post_meta($attachment_id, '_aitrongcay_avatar_owner', get_current_user_id());
        wp_safe_redirect(add_query_arg('account_status', 'avatar-updated', home_url('/tai-khoan/')));
        exit;
    }

    wp_safe_redirect(add_query_arg('account_status', 'updated', home_url('/tai-khoan/')));
    exit;
}
add_action('admin_post_aitrongcay_account_avatar_update', 'aitrongcay_handle_account_avatar_update');

function aitrongcay_handle_account_avatar_remove(): void
{
    if (! is_user_logged_in()) {
        wp_safe_redirect(home_url('/dang-nhap/?auth_status=login-required'));
        exit;
    }
    check_admin_referer('aitrongcay_account_avatar_remove_submit', 'aitrongcay_account_avatar_remove_nonce');
    delete_user_meta(get_current_user_id(), 'aitrongcay_avatar_id');
    wp_safe_redirect(add_query_arg('account_status', 'avatar-removed', home_url('/tai-khoan/')));
    exit;
}
add_action('admin_post_aitrongcay_account_avatar_remove', 'aitrongcay_handle_account_avatar_remove');

function aitrongcay_handle_password_reset_request(): void
{
    $email = sanitize_email(wp_unslash($_POST['email'] ?? ''));
    if ($email !== '' && is_email($email)) {
        $user = get_user_by('email', $email);
        if ($user instanceof WP_User) {
            retrieve_password($user->user_login);
        }
    }
    wp_safe_redirect(add_query_arg('account_status', 'reset-sent', home_url('/dang-nhap/')));
    exit;
}
add_action('admin_post_nopriv_aitrongcay_password_reset_request', 'aitrongcay_handle_password_reset_request');
add_action('admin_post_aitrongcay_password_reset_request', 'aitrongcay_handle_password_reset_request');

function aitrongcay_consultation_form_shortcode(): string
{
    ob_start();
    include get_template_directory() . '/template-parts/virtual/dang-ky-tu-van.php';
    return (string) ob_get_clean();
}
add_shortcode('aitrongcay_consultation_form', 'aitrongcay_consultation_form_shortcode');

function aitrongcay_login_form_shortcode(): string
{
    ob_start();
    include get_template_directory() . '/template-parts/virtual/dang-nhap.php';
    return (string) ob_get_clean();
}
add_shortcode('aitrongcay_login_form', 'aitrongcay_login_form_shortcode');

function aitrongcay_register_form_shortcode(): string
{
    ob_start();
    include get_template_directory() . '/template-parts/virtual/onboarding.php';
    return (string) ob_get_clean();
}
add_shortcode('aitrongcay_register_form', 'aitrongcay_register_form_shortcode');

function aitrongcay_require_portal_nonce(): void
{
    check_ajax_referer('aitrongcay_portal_actions', 'nonce');
    if (! is_user_logged_in()) {
        wp_send_json_error(['message' => 'Anh/chị cần đăng nhập trước.'], 401);
    }
}

function aitrongcay_friendships_table(): string
{
    global $wpdb;
    return $wpdb->prefix . 'aitr_friendships';
}

function aitrongcay_garden_members_table(): string
{
    global $wpdb;
    return $wpdb->prefix . 'aitr_garden_members';
}

function aitrongcay_garden_notes_table(): string
{
    global $wpdb;
    return $wpdb->prefix . 'aitr_garden_notes';
}

function aitrongcay_gardens_table(): string
{
    global $wpdb;
    return $wpdb->prefix . 'aitr_gardens';
}

function aitrongcay_garden_pots_table(): string
{
    global $wpdb;
    return $wpdb->prefix . 'aitr_garden_pots';
}

function aitrongcay_garden_tools_table(): string
{
    global $wpdb;
    return $wpdb->prefix . 'aitr_garden_tools';
}

function aitrongcay_install_social_tables(): void
{
    global $wpdb;
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $charset = $wpdb->get_charset_collate();
    $friendships = aitrongcay_friendships_table();
    $members = aitrongcay_garden_members_table();
    $notes = aitrongcay_garden_notes_table();
    $gardens = aitrongcay_gardens_table();
    $pots = aitrongcay_garden_pots_table();
    $tools = aitrongcay_garden_tools_table();

    dbDelta("CREATE TABLE {$friendships} (
        id bigint unsigned NOT NULL AUTO_INCREMENT,
        requester_user_id bigint unsigned NOT NULL,
        addressee_user_id bigint unsigned NOT NULL,
        unique_pair_key varchar(64) NOT NULL,
        status varchar(20) NOT NULL DEFAULT 'pending',
        created_at datetime NOT NULL,
        responded_at datetime NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY unique_pair_key (unique_pair_key),
        KEY requester_user_id (requester_user_id),
        KEY addressee_user_id (addressee_user_id),
        KEY status (status)
    ) {$charset};");

    dbDelta("CREATE TABLE {$members} (
        id bigint unsigned NOT NULL AUTO_INCREMENT,
        garden_key varchar(64) NOT NULL,
        user_id bigint unsigned NOT NULL,
        role varchar(20) NOT NULL DEFAULT 'viewer',
        status varchar(20) NOT NULL DEFAULT 'invited',
        invited_by_user_id bigint unsigned NULL,
        created_at datetime NOT NULL,
        updated_at datetime NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY garden_user (garden_key, user_id),
        KEY garden_key (garden_key),
        KEY user_id (user_id),
        KEY status (status)
    ) {$charset};");

    dbDelta("CREATE TABLE {$notes} (
        id bigint unsigned NOT NULL AUTO_INCREMENT,
        garden_key varchar(64) NOT NULL,
        pot_code varchar(64) NOT NULL,
        note_text longtext NOT NULL,
        updated_by_user_id bigint unsigned NULL,
        created_at datetime NOT NULL,
        updated_at datetime NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY garden_pot (garden_key, pot_code),
        KEY garden_key (garden_key),
        KEY updated_at (updated_at)
    ) {$charset};");

    dbDelta("CREATE TABLE {$gardens} (
        id bigint unsigned NOT NULL AUTO_INCREMENT,
        garden_key varchar(64) NOT NULL,
        owner_user_id bigint unsigned NOT NULL,
        garden_code varchar(64) NOT NULL DEFAULT '',
        garden_name varchar(255) NOT NULL DEFAULT '',
        summary text NULL,
        status_line varchar(255) NOT NULL DEFAULT '',
        created_at datetime NOT NULL,
        updated_at datetime NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY garden_key (garden_key),
        KEY owner_user_id (owner_user_id)
    ) {$charset};");

    dbDelta("CREATE TABLE {$pots} (
        id bigint unsigned NOT NULL AUTO_INCREMENT,
        garden_key varchar(64) NOT NULL,
        pot_code varchar(64) NOT NULL,
        pot_name varchar(255) NOT NULL DEFAULT '',
        status varchar(255) NOT NULL DEFAULT '',
        status_summary text NULL,
        ph varchar(64) NOT NULL DEFAULT '',
        temperature varchar(64) NOT NULL DEFAULT '',
        humidity varchar(64) NOT NULL DEFAULT '',
        light_label varchar(128) NOT NULL DEFAULT '',
        light_device varchar(64) NOT NULL DEFAULT '',
        pump_label varchar(128) NOT NULL DEFAULT '',
        irrigation varchar(255) NOT NULL DEFAULT '',
        video_url text NULL,
        image_url text NULL,
        ai_note text NULL,
        harvest_eta varchar(255) NOT NULL DEFAULT '',
        sort_order int NOT NULL DEFAULT 0,
        created_at datetime NOT NULL,
        updated_at datetime NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY garden_pot (garden_key, pot_code),
        KEY garden_key (garden_key),
        KEY sort_order (sort_order)
    ) {$charset};");

    dbDelta("CREATE TABLE {$tools} (
        id bigint unsigned NOT NULL AUTO_INCREMENT,
        garden_key varchar(64) NOT NULL,
        tool_key varchar(64) NOT NULL,
        name varchar(255) NOT NULL DEFAULT '',
        type varchar(128) NOT NULL DEFAULT '',
        description text NULL,
        owned int NOT NULL DEFAULT 0,
        qty int NOT NULL DEFAULT 0,
        image varchar(255) NOT NULL DEFAULT '',
        sort_order int NOT NULL DEFAULT 0,
        created_at datetime NOT NULL,
        updated_at datetime NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY garden_tool (garden_key, tool_key),
        KEY garden_key (garden_key),
        KEY sort_order (sort_order)
    ) {$charset};");

    update_option('aitrongcay_social_schema_version', '3', false);
}
add_action('init', 'aitrongcay_install_social_tables', 30);

function aitrongcay_seed_db_from_legacy_datasets(): void
{
    if ((string) get_option('aitrongcay_db_seed_version', '') === '1') {
        return;
    }
    if (! function_exists('aitrongcay_portal_dataset_library')) {
        return;
    }

    $library = aitrongcay_portal_dataset_library();
    foreach ($library as $dataset) {
        $emails = array_values(array_filter((array) ($dataset['match_emails'] ?? [])));
        if (! $emails) {
            continue;
        }
        $owner = get_user_by('email', (string) $emails[0]);
        if (! $owner instanceof WP_User) {
            continue;
        }
        $garden_key = aitrongcay_current_garden_key($owner);
        aitrongcay_upsert_garden_record($garden_key, (int) $owner->ID, [
            'garden_code' => (string) (($dataset['garden_code'] ?? '')),
            'garden_name' => (string) (($dataset['garden_name'] ?? '')),
            'summary' => (string) (($dataset['ai']['summary'] ?? ($dataset['summary'] ?? ''))),
            'status_line' => (string) (($dataset['status'] ?? '')),
        ]);

        foreach ((array) ($dataset['pots'] ?? []) as $index => $pot) {
            if (! is_array($pot)) continue;
            $pot['sort_order'] = $index + 1;
            aitrongcay_upsert_db_pot($garden_key, $pot);
        }

        foreach ((array) ($dataset['tool_shelf'] ?? []) as $index => $tool) {
            if (! is_array($tool)) continue;
            global $wpdb;
            $table = aitrongcay_garden_tools_table();
            $tool_key = sanitize_key((string) ($tool['name'] ?? ('tool_' . ($index + 1))));
            $now = current_time('mysql');
            $existing_id = (int) $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE garden_key = %s AND tool_key = %s LIMIT 1", $garden_key, $tool_key));
            $payload = [
                'name' => (string) ($tool['name'] ?? ''),
                'type' => (string) ($tool['type'] ?? ''),
                'description' => (string) ($tool['description'] ?? ''),
                'owned' => (int) ($tool['owned'] ?? 0),
                'qty' => (int) ($tool['qty'] ?? 0),
                'image' => (string) ($tool['image'] ?? ''),
                'sort_order' => $index + 1,
                'updated_at' => $now,
            ];
            if ($existing_id > 0) {
                $wpdb->update($table, $payload, ['id' => $existing_id], ['%s','%s','%s','%d','%d','%s','%d','%s'], ['%d']);
            } else {
                $wpdb->insert($table, array_merge(['garden_key' => $garden_key, 'tool_key' => $tool_key], $payload, ['created_at' => $now]), ['%s','%s','%s','%s','%s','%d','%d','%s','%d','%s','%s']);
            }
        }
    }

    update_option('aitrongcay_db_seed_version', '1', false);
}
add_action('init', 'aitrongcay_seed_db_from_legacy_datasets', 35);

function aitrongcay_current_garden_key(?WP_User $user = null): string
{
    $user = $user instanceof WP_User ? $user : wp_get_current_user();
    $email = strtolower(trim((string) ($user->user_email ?? 'guest@example.com')));
    return 'garden:' . md5($email);
}

function aitrongcay_selected_garden_user_meta_key(): string
{
    return '_aitrongcay_selected_garden_key';
}

function aitrongcay_garden_name_overrides_meta_key(): string
{
    return '_aitrongcay_garden_name_overrides';
}

function aitrongcay_get_garden_name_override(string $garden_key, int $user_id): string
{
    if ($garden_key === '' || $user_id <= 0) {
        return '';
    }

    $bucket = get_user_meta($user_id, aitrongcay_garden_name_overrides_meta_key(), true);
    if (! is_array($bucket)) {
        return '';
    }

    return trim((string) ($bucket[$garden_key] ?? ''));
}

function aitrongcay_store_garden_name_override(string $garden_key, int $user_id, string $garden_name): void
{
    if ($garden_key === '' || $user_id <= 0) {
        return;
    }

    $bucket = get_user_meta($user_id, aitrongcay_garden_name_overrides_meta_key(), true);
    if (! is_array($bucket)) {
        $bucket = [];
    }

    $garden_name = trim(preg_replace('/\s+/u', ' ', $garden_name));
    if ($garden_name === '') {
        unset($bucket[$garden_key]);
    } else {
        $bucket[$garden_key] = $garden_name;
    }

    update_user_meta($user_id, aitrongcay_garden_name_overrides_meta_key(), $bucket);
}

function aitrongcay_format_person_name_list(array $names): string
{
    $names = array_values(array_filter(array_map(static function ($name): string {
        return trim((string) $name);
    }, $names)));

    $count = count($names);
    if ($count === 0) {
        return '';
    }
    if ($count === 1) {
        return $names[0];
    }
    if ($count === 2) {
        return $names[0] . ' và ' . $names[1];
    }

    $last = array_pop($names);
    return implode(', ', $names) . ' và ' . $last;
}

function aitrongcay_build_default_garden_name(string $garden_key, ?WP_User $viewer = null): string
{
    $garden_key = trim($garden_key);
    $members = $garden_key !== '' ? aitrongcay_get_garden_members($garden_key) : [];
    $owner_names = [];

    foreach ($members as $member) {
        $role = (string) ($member['role'] ?? 'viewer');
        $status = (string) ($member['status'] ?? '');
        if ($status !== 'active' || ! in_array($role, ['owner', 'co_owner'], true)) {
            continue;
        }

        $member_user = get_user_by('id', (int) ($member['user_id'] ?? 0));
        if (! $member_user instanceof WP_User) {
            continue;
        }

        $display_name = trim((string) ($member_user->display_name ?: $member_user->first_name ?: $member_user->user_login));
        if ($display_name !== '' && ! in_array($display_name, $owner_names, true)) {
            $owner_names[] = $display_name;
        }
    }

    if ($owner_names !== []) {
        return 'Vườn của ' . aitrongcay_format_person_name_list($owner_names);
    }

    $profile = aitrongcay_portal_profile_for_user($viewer instanceof WP_User ? $viewer : wp_get_current_user());
    return trim((string) ($profile['garden_name'] ?? 'Khu vườn của bạn'));
}

function aitrongcay_get_garden_display_name(string $garden_key, ?WP_User $viewer = null): string
{
    $record = function_exists('aitrongcay_get_garden_record') ? aitrongcay_get_garden_record($garden_key) : null;
    $db_name = trim((string) ($record['garden_name'] ?? ''));
    if ($db_name !== '') {
        return $db_name;
    }

    $owner = aitrongcay_get_garden_owner_user($garden_key);
    $owner_id = (int) ($owner->ID ?? 0);
    $override = $owner_id > 0 ? aitrongcay_get_garden_name_override($garden_key, $owner_id) : '';
    if ($override !== '') {
        return $override;
    }

    $viewer = $viewer instanceof WP_User ? $viewer : wp_get_current_user();
    $viewer_id = (int) ($viewer->ID ?? 0);
    $viewer_override = $viewer_id > 0 ? aitrongcay_get_garden_name_override($garden_key, $viewer_id) : '';
    if ($viewer_override !== '') {
        return $viewer_override;
    }

    return aitrongcay_build_default_garden_name($garden_key, $viewer);
}

function aitrongcay_remember_selected_garden_key(int $user_id, string $garden_key): void
{
    $garden_key = trim($garden_key);
    if ($user_id <= 0 || $garden_key === '') {
        return;
    }

    update_user_meta($user_id, aitrongcay_selected_garden_user_meta_key(), $garden_key);
}

function aitrongcay_get_remembered_garden_key(int $user_id): string
{
    if ($user_id <= 0) {
        return '';
    }

    return trim((string) get_user_meta($user_id, aitrongcay_selected_garden_user_meta_key(), true));
}

function aitrongcay_resolve_garden_owner_id(string $garden_key, ?WP_User $viewer = null): int
{
    $owner = aitrongcay_get_garden_owner_user($garden_key);
    if ($owner instanceof WP_User) {
        return (int) $owner->ID;
    }

    $viewer = $viewer instanceof WP_User ? $viewer : wp_get_current_user();
    return $viewer instanceof WP_User ? (int) $viewer->ID : 0;
}

function aitrongcay_friend_pair_key(int $user_a, int $user_b): string
{
    $pair = [$user_a, $user_b];
    sort($pair, SORT_NUMERIC);
    return $pair[0] . ':' . $pair[1];
}

function aitrongcay_get_user_friends(int $user_id): array
{
    global $wpdb;
    $table = aitrongcay_friendships_table();
    return $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$table} WHERE (requester_user_id = %d OR addressee_user_id = %d) AND status = 'accepted' ORDER BY id DESC",
        $user_id,
        $user_id
    ), ARRAY_A) ?: [];
}

function aitrongcay_get_friend_ids(int $user_id): array
{
    $friends = aitrongcay_get_user_friends($user_id);
    if ($friends === []) {
        return [];
    }

    $friend_ids = [];
    foreach ($friends as $friendship) {
        $requester_id = (int) ($friendship['requester_user_id'] ?? 0);
        $addressee_id = (int) ($friendship['addressee_user_id'] ?? 0);
        $friend_id = $requester_id === $user_id ? $addressee_id : $requester_id;
        if ($friend_id > 0) {
            $friend_ids[] = $friend_id;
        }
    }

    return array_values(array_unique($friend_ids));
}

function aitrongcay_is_friend_with_garden_owner(string $garden_key, int $user_id): bool
{
    if ($garden_key === '' || $user_id <= 0) {
        return false;
    }

    $owner = aitrongcay_get_garden_owner_user($garden_key);
    if (! $owner instanceof WP_User) {
        return false;
    }

    if ((int) $owner->ID === $user_id) {
        return true;
    }

    return in_array((int) $owner->ID, aitrongcay_get_friend_ids($user_id), true);
}

function aitrongcay_get_friend_invites_received(int $user_id): array
{
    global $wpdb;
    $table = aitrongcay_friendships_table();
    return $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$table} WHERE addressee_user_id = %d AND status = 'pending' ORDER BY id DESC",
        $user_id
    ), ARRAY_A) ?: [];
}

function aitrongcay_get_active_garden_owners(int $viewer_user_id, string $search = ''): array
{
    global $wpdb;

    $members_table = aitrongcay_garden_members_table();
    $friendships_table = aitrongcay_friendships_table();
    $search = trim($search);

    $sql = "SELECT m.user_id, m.garden_key, u.display_name, u.user_login
        FROM {$members_table} m
        INNER JOIN {$wpdb->users} u ON u.ID = m.user_id
        WHERE m.role = 'owner'
          AND m.status = 'active'
          AND m.user_id <> %d";
    $params = [$viewer_user_id];

    if ($search !== '') {
        $like = '%' . $wpdb->esc_like($search) . '%';
        $sql .= " AND (u.display_name LIKE %s OR u.user_login LIKE %s)";
        $params[] = $like;
        $params[] = $like;
    }

    $sql .= " ORDER BY CASE WHEN u.display_name = '' OR u.display_name IS NULL THEN u.user_login ELSE u.display_name END ASC, m.id ASC";

    $rows = $wpdb->get_results($wpdb->prepare($sql, ...$params), ARRAY_A) ?: [];
    if ($rows === []) {
        return [];
    }

    $owner_map = [];
    foreach ($rows as $row) {
        $owner_id = (int) ($row['user_id'] ?? 0);
        if ($owner_id <= 0 || isset($owner_map[$owner_id])) {
            continue;
        }
        $owner_map[$owner_id] = $row;
    }

    $owner_ids = array_keys($owner_map);
    if ($owner_ids === []) {
        return [];
    }

    $friendship_status_map = [];
    $placeholders = implode(',', array_fill(0, count($owner_ids), '%d'));
    $friendship_rows = $wpdb->get_results($wpdb->prepare(
        "SELECT requester_user_id, addressee_user_id, status
         FROM {$friendships_table}
         WHERE ((requester_user_id = %d AND addressee_user_id IN ({$placeholders}))
             OR (addressee_user_id = %d AND requester_user_id IN ({$placeholders})))",
        ...array_merge([$viewer_user_id], $owner_ids, [$viewer_user_id], $owner_ids)
    ), ARRAY_A) ?: [];

    foreach ($friendship_rows as $row) {
        $requester_id = (int) ($row['requester_user_id'] ?? 0);
        $addressee_id = (int) ($row['addressee_user_id'] ?? 0);
        $other_id = $requester_id === $viewer_user_id ? $addressee_id : $requester_id;
        if ($other_id <= 0) {
            continue;
        }

        $status = (string) ($row['status'] ?? '');
        $friendship_status_map[$other_id] = [
            'status' => $status,
            'direction' => $status === 'pending'
                ? ($requester_id === $viewer_user_id ? 'outgoing' : 'incoming')
                : 'none',
        ];
    }

    $results = [];
    foreach ($owner_map as $owner_id => $row) {
        $owner = get_user_by('id', $owner_id);
        if (! $owner instanceof WP_User) {
            continue;
        }

        $garden_key = (string) ($row['garden_key'] ?? '');
        $profile = $garden_key !== '' ? aitrongcay_portal_profile_for_garden_context($garden_key, $owner) : aitrongcay_portal_profile_for_user($owner);
        $friendship = $friendship_status_map[$owner_id] ?? ['status' => 'none', 'direction' => 'none'];

        $results[] = [
            'user_id' => $owner_id,
            'garden_key' => $garden_key,
            'display_name' => trim((string) ($owner->display_name ?: $owner->first_name ?: $owner->user_login)),
            'user_login' => (string) $owner->user_login,
            'friendship_status' => (string) ($friendship['status'] ?? 'none'),
            'friendship_direction' => (string) ($friendship['direction'] ?? 'none'),
            'profile' => is_array($profile) ? $profile : null,
        ];
    }

    return $results;
}

function aitrongcay_get_garden_members(string $garden_key): array
{
    global $wpdb;
    $table = aitrongcay_garden_members_table();
    return $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$table} WHERE garden_key = %s AND status IN ('active','invited') ORDER BY FIELD(role,'owner','co_owner','viewer'), id ASC",
        $garden_key
    ), ARRAY_A) ?: [];
}

function aitrongcay_get_user_garden_memberships(int $user_id, array $statuses = ['active', 'invited']): array
{
    global $wpdb;
    $table = aitrongcay_garden_members_table();
    $statuses = array_values(array_filter(array_map('sanitize_key', $statuses)));
    if ($statuses === []) {
        $statuses = ['active', 'invited'];
    }
    $placeholders = implode(',', array_fill(0, count($statuses), '%s'));
    $params = array_merge([$user_id], $statuses);
    return $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$table} WHERE user_id = %d AND status IN ({$placeholders}) ORDER BY updated_at DESC, id DESC",
        ...$params
    ), ARRAY_A) ?: [];
}

function aitrongcay_get_garden_invites_received(int $user_id): array
{
    return aitrongcay_get_user_garden_memberships($user_id, ['invited']);
}

function aitrongcay_get_garden_owner_user(string $garden_key): ?WP_User
{
    global $wpdb;
    $table = aitrongcay_garden_members_table();
    $owner_id = $wpdb->get_var($wpdb->prepare(
        "SELECT user_id FROM {$table} WHERE garden_key = %s AND role = 'owner' AND status = 'active' ORDER BY id ASC LIMIT 1",
        $garden_key
    ));
    return $owner_id ? get_user_by('id', (int) $owner_id) : null;
}

function aitrongcay_resolve_active_garden_key(?WP_User $user = null): string
{
    $user = $user instanceof WP_User ? $user : wp_get_current_user();
    $default_garden_key = aitrongcay_current_garden_key($user);
    if (! $user instanceof WP_User || ! $user->exists()) {
        return $default_garden_key;
    }

    $user_id = (int) $user->ID;
    $requested_garden_key = isset($_GET['garden']) ? sanitize_text_field((string) wp_unslash($_GET['garden'])) : '';
    if ($requested_garden_key !== '' && aitrongcay_user_can_view_garden($requested_garden_key, $user_id)) {
        aitrongcay_remember_selected_garden_key($user_id, $requested_garden_key);
        return $requested_garden_key;
    }

    $remembered_garden_key = aitrongcay_get_remembered_garden_key($user_id);
    if ($remembered_garden_key !== '' && aitrongcay_user_can_view_garden($remembered_garden_key, $user_id)) {
        return $remembered_garden_key;
    }

    $active_memberships = aitrongcay_get_user_garden_memberships($user_id, ['active']);
    foreach ($active_memberships as $membership) {
        if (($membership['role'] ?? '') === 'owner' && ! empty($membership['garden_key'])) {
            $garden_key = (string) $membership['garden_key'];
            aitrongcay_remember_selected_garden_key($user_id, $garden_key);
            return $garden_key;
        }
    }

    foreach ($active_memberships as $membership) {
        if (! empty($membership['garden_key'])) {
            $garden_key = (string) $membership['garden_key'];
            aitrongcay_remember_selected_garden_key($user_id, $garden_key);
            return $garden_key;
        }
    }

    aitrongcay_remember_selected_garden_key($user_id, $default_garden_key);
    return $default_garden_key;
}

function aitrongcay_get_role_label(string $role): string
{
    return match ($role) {
        'owner' => 'Chủ vườn',
        'co_owner' => 'Đồng sở hữu',
        'viewer' => 'Chỉ xem',
        default => $role,
    };
}

function aitrongcay_get_role_badge_class(string $role): string
{
    return match ($role) {
        'owner' => 'is-forest',
        'co_owner' => 'is-gold',
        'viewer' => 'is-sky',
        default => 'is-sand',
    };
}

function aitrongcay_get_member_status_label(string $status): string
{
    return match ($status) {
        'active' => 'Đang tham gia',
        'invited' => 'Đang chờ phản hồi',
        'declined' => 'Đã từ chối',
        'removed' => 'Đã gỡ',
        default => $status,
    };
}

function aitrongcay_get_viewable_gardens_for_user(?WP_User $user = null): array
{
    $user = $user instanceof WP_User ? $user : wp_get_current_user();
    if (! $user instanceof WP_User || ! $user->exists()) {
        return [];
    }

    $user_id = (int) $user->ID;
    $memberships = aitrongcay_get_user_garden_memberships($user_id, ['active']);
    $gardens = [];
    $append_garden = static function (string $garden_key, string $role) use (&$gardens, $user): void {
        if ($garden_key === '' || isset($gardens[$garden_key])) {
            return;
        }

        $profile = aitrongcay_portal_profile_for_garden_context($garden_key, $user);
        if (! is_array($profile)) {
            return;
        }

        $owner = aitrongcay_get_garden_owner_user($garden_key);
        $gardens[$garden_key] = [
            'garden_key' => $garden_key,
            'role' => $role,
            'profile' => $profile,
            'owner' => $owner,
            'member_count' => count(array_filter(
                aitrongcay_get_garden_members($garden_key),
                static fn(array $member): bool => ($member['status'] ?? '') === 'active'
            )),
        ];
    };

    foreach ($memberships as $membership) {
        $append_garden((string) ($membership['garden_key'] ?? ''), (string) ($membership['role'] ?? 'viewer'));
    }

    foreach (aitrongcay_get_friend_ids($user_id) as $friend_id) {
        $friend = get_user_by('id', $friend_id);
        if (! $friend instanceof WP_User) {
            continue;
        }

        $friend_garden_key = aitrongcay_preferred_garden_key_for_user($friend);
        $append_garden($friend_garden_key, 'viewer');
    }

    return array_values($gardens);
}

function aitrongcay_portal_profile_for_garden_context(string $garden_key, ?WP_User $viewer = null): ?array
{
    $owner = aitrongcay_get_garden_owner_user($garden_key);
    $base_user = $owner instanceof WP_User ? $owner : ($viewer instanceof WP_User ? $viewer : wp_get_current_user());
    $viewer = $viewer instanceof WP_User ? $viewer : wp_get_current_user();

    if ($garden_key !== '' && function_exists('aitrongcay_get_garden_record') && function_exists('aitrongcay_upsert_garden_record')) {
        $record_probe = aitrongcay_get_garden_record($garden_key);
        if (! is_array($record_probe) || trim((string) ($record_probe['garden_name'] ?? '')) === '') {
            $owner_id = (int) ($owner->ID ?? 0);
            $viewer_id = (int) ($viewer->ID ?? 0);
            $override_name = $owner_id > 0 ? aitrongcay_get_garden_name_override($garden_key, $owner_id) : '';
            if ($override_name === '' && $viewer_id > 0) {
                $override_name = aitrongcay_get_garden_name_override($garden_key, $viewer_id);
            }
            if ($override_name !== '') {
                $sync_owner_id = $owner_id > 0 ? $owner_id : max(0, $viewer_id);
                if ($sync_owner_id > 0) {
                    aitrongcay_upsert_garden_record($garden_key, $sync_owner_id, [
                        'garden_name' => $override_name,
                        'garden_code' => (string) ($record_probe['garden_code'] ?? strtoupper(substr(md5($garden_key), 0, 6))),
                        'summary' => (string) ($record_probe['summary'] ?? ''),
                        'status_line' => (string) ($record_probe['status_line'] ?? ''),
                    ]);
                }
            }
        }
    }

    $profile = aitrongcay_portal_profile_for_user($base_user instanceof WP_User ? $base_user : null);

    $record = function_exists('aitrongcay_get_garden_record') ? aitrongcay_get_garden_record($garden_key) : null;
    if (is_array($record)) {
        $db_name = trim((string) ($record['garden_name'] ?? ''));
        $db_code = trim((string) ($record['garden_code'] ?? ''));
        $db_summary = trim((string) ($record['summary'] ?? ''));
        $db_status = trim((string) ($record['status_line'] ?? ''));
        if ($db_name !== '') {
            $profile['garden_name'] = $db_name;
        }
        if ($db_code !== '') {
            $profile['garden_code'] = $db_code;
        }
        if ($db_summary !== '') {
            $profile['summary'] = $db_summary;
        }
        if ($db_status !== '') {
            $profile['status'] = $db_status;
        }
    }

    if (! empty($garden_key)) {
        $profile['garden_name'] = aitrongcay_get_garden_display_name($garden_key, $base_user instanceof WP_User ? $base_user : null);
    }

    return $profile;
}

function aitrongcay_user_garden_role(string $garden_key, int $user_id): ?string
{
    global $wpdb;
    $table = aitrongcay_garden_members_table();
    $role = $wpdb->get_var($wpdb->prepare(
        "SELECT role FROM {$table} WHERE garden_key = %s AND user_id = %d AND status = 'active' LIMIT 1",
        $garden_key,
        $user_id
    ));

    if (is_string($role) && $role !== '') {
        return $role;
    }

    if (aitrongcay_is_friend_with_garden_owner($garden_key, $user_id)) {
        return 'viewer';
    }

    return null;
}

function aitrongcay_user_can_control_garden(string $garden_key, int $user_id): bool
{
    return in_array(aitrongcay_user_garden_role($garden_key, $user_id), ['owner', 'co_owner'], true);
}

function aitrongcay_user_can_view_garden(string $garden_key, int $user_id): bool
{
    return in_array(aitrongcay_user_garden_role($garden_key, $user_id), ['owner', 'co_owner', 'viewer'], true);
}

function aitrongcay_get_garden_record(string $garden_key): ?array
{
    global $wpdb;
    if ($garden_key === '') {
        return null;
    }
    $table = aitrongcay_gardens_table();
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE garden_key = %s LIMIT 1", $garden_key), ARRAY_A);
    return is_array($row) ? $row : null;
}

function aitrongcay_upsert_garden_record(string $garden_key, int $owner_user_id, array $payload = []): bool
{
    global $wpdb;
    if ($garden_key === '' || $owner_user_id <= 0) {
        return false;
    }
    $table = aitrongcay_gardens_table();
    $now = current_time('mysql');
    $existing = aitrongcay_get_garden_record($garden_key);
    $data = [
        'owner_user_id' => $owner_user_id,
        'garden_code' => (string) ($payload['garden_code'] ?? ''),
        'garden_name' => (string) ($payload['garden_name'] ?? ''),
        'summary' => (string) ($payload['summary'] ?? ''),
        'status_line' => (string) ($payload['status_line'] ?? ''),
        'updated_at' => $now,
    ];
    if ($existing) {
        return false !== $wpdb->update($table, $data, ['garden_key' => $garden_key], ['%d','%s','%s','%s','%s','%s'], ['%s']);
    }
    return false !== $wpdb->insert(
        $table,
        array_merge(['garden_key' => $garden_key], $data, ['created_at' => $now]),
        ['%s','%d','%s','%s','%s','%s','%s','%s']
    );
}

function aitrongcay_get_db_pots(string $garden_key): array
{
    global $wpdb;
    if ($garden_key === '') {
        return [];
    }
    $table = aitrongcay_garden_pots_table();
    return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table} WHERE garden_key = %s ORDER BY sort_order ASC, id ASC", $garden_key), ARRAY_A) ?: [];
}

function aitrongcay_upsert_db_pot(string $garden_key, array $pot): bool
{
    global $wpdb;
    $garden_key = trim($garden_key);
    $pot_code = trim((string) ($pot['pot_code'] ?? $pot['code'] ?? ''));
    if ($garden_key === '' || $pot_code === '') {
        return false;
    }
    $table = aitrongcay_garden_pots_table();
    $now = current_time('mysql');
    $existing_id = (int) $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE garden_key = %s AND pot_code = %s LIMIT 1", $garden_key, $pot_code));
    $data = [
        'pot_name' => (string) ($pot['pot_name'] ?? $pot['name'] ?? ''),
        'status' => (string) ($pot['status'] ?? ''),
        'status_summary' => (string) ($pot['status_summary'] ?? ''),
        'ph' => (string) ($pot['ph'] ?? ''),
        'temperature' => (string) ($pot['temperature'] ?? ''),
        'humidity' => (string) ($pot['humidity'] ?? ''),
        'light_label' => (string) ($pot['light_label'] ?? $pot['light'] ?? ''),
        'light_device' => (string) ($pot['light_device'] ?? ''),
        'pump_label' => (string) ($pot['pump_label'] ?? $pot['pump'] ?? ''),
        'irrigation' => (string) ($pot['irrigation'] ?? ''),
        'video_url' => (string) ($pot['video_url'] ?? $pot['video'] ?? ''),
        'image_url' => (string) ($pot['image_url'] ?? $pot['image'] ?? ''),
        'ai_note' => (string) ($pot['ai_note'] ?? ''),
        'harvest_eta' => (string) ($pot['harvest_eta'] ?? ''),
        'sort_order' => (int) ($pot['sort_order'] ?? 0),
        'updated_at' => $now,
    ];
    if ($existing_id > 0) {
        return false !== $wpdb->update($table, $data, ['id' => $existing_id], ['%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%d','%s'], ['%d']);
    }
    return false !== $wpdb->insert($table, array_merge(['garden_key' => $garden_key, 'pot_code' => $pot_code], $data, ['created_at' => $now]), ['%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%d','%s','%s']);
}

function aitrongcay_replace_garden_pots(string $garden_key, array $pots): bool
{
    global $wpdb;
    if ($garden_key === '') {
        return false;
    }
    $table = aitrongcay_garden_pots_table();
    $wpdb->delete($table, ['garden_key' => $garden_key], ['%s']);
    foreach (array_values($pots) as $index => $pot) {
        if (! is_array($pot)) {
            continue;
        }
        $pot_code = trim((string) ($pot['pot_code'] ?? $pot['code'] ?? ''));
        $pot_name = trim((string) ($pot['pot_name'] ?? $pot['name'] ?? ''));
        if ($pot_code === '' || $pot_name === '') {
            continue;
        }
        $pot['sort_order'] = $index + 1;
        aitrongcay_upsert_db_pot($garden_key, $pot);
    }
    return true;
}

function aitrongcay_get_db_tools(string $garden_key): array
{
    global $wpdb;
    if ($garden_key === '') {
        return [];
    }
    $table = aitrongcay_garden_tools_table();
    return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table} WHERE garden_key = %s ORDER BY sort_order ASC, id ASC", $garden_key), ARRAY_A) ?: [];
}

function aitrongcay_replace_garden_tools(string $garden_key, array $tools): bool
{
    global $wpdb;
    if ($garden_key === '') {
        return false;
    }
    $table = aitrongcay_garden_tools_table();
    $wpdb->delete($table, ['garden_key' => $garden_key], ['%s']);
    $now = current_time('mysql');
    foreach (array_values($tools) as $index => $tool) {
        if (! is_array($tool)) {
            continue;
        }
        $name = trim((string) ($tool['name'] ?? ''));
        if ($name === '') {
            continue;
        }
        $tool_key = sanitize_key((string) ($tool['tool_key'] ?? $name ?: ('tool_' . ($index + 1))));
        $wpdb->insert($table, [
            'garden_key' => $garden_key,
            'tool_key' => $tool_key !== '' ? $tool_key : ('tool_' . ($index + 1)),
            'name' => $name,
            'type' => trim((string) ($tool['type'] ?? '')),
            'description' => trim((string) ($tool['description'] ?? '')),
            'owned' => (int) ($tool['owned'] ?? 0),
            'qty' => (int) ($tool['qty'] ?? 0),
            'image' => trim((string) ($tool['image'] ?? '')),
            'sort_order' => $index + 1,
            'created_at' => $now,
            'updated_at' => $now,
        ], ['%s','%s','%s','%s','%s','%d','%d','%s','%d','%s','%s']);
    }
    return true;
}

function aitrongcay_get_garden_pot_notes(string $garden_key): array
{
    global $wpdb;
    if ($garden_key === '') {
        return [];
    }

    $table = aitrongcay_garden_notes_table();
    $rows = $wpdb->get_results($wpdb->prepare(
        "SELECT pot_code, note_text, updated_at, updated_by_user_id FROM {$table} WHERE garden_key = %s ORDER BY updated_at DESC, id DESC",
        $garden_key
    ), ARRAY_A) ?: [];

    $notes = [];
    foreach ($rows as $row) {
        $pot_code = sanitize_text_field((string) ($row['pot_code'] ?? ''));
        if ($pot_code === '' || isset($notes[$pot_code])) {
            continue;
        }
        $notes[$pot_code] = [
            'pot_code' => $pot_code,
            'note_text' => trim((string) ($row['note_text'] ?? '')),
            'updated_at' => (string) ($row['updated_at'] ?? ''),
            'updated_by_user_id' => (int) ($row['updated_by_user_id'] ?? 0),
        ];
    }

    return $notes;
}

function aitrongcay_save_garden_pot_note(string $garden_key, string $pot_code, string $note_text, int $user_id = 0): bool
{
    global $wpdb;
    if ($garden_key === '' || $pot_code === '') {
        return false;
    }

    $table = aitrongcay_garden_notes_table();
    $now = current_time('mysql');
    $existing_id = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM {$table} WHERE garden_key = %s AND pot_code = %s LIMIT 1",
        $garden_key,
        $pot_code
    ));

    $payload = [
        'note_text' => $note_text,
        'updated_by_user_id' => max(0, $user_id),
        'updated_at' => $now,
    ];

    if ($existing_id > 0) {
        return false !== $wpdb->update(
            $table,
            $payload,
            ['id' => $existing_id],
            ['%s', '%d', '%s'],
            ['%d']
        );
    }

    return false !== $wpdb->insert($table, [
        'garden_key' => $garden_key,
        'pot_code' => $pot_code,
        'note_text' => $note_text,
        'updated_by_user_id' => max(0, $user_id),
        'created_at' => $now,
        'updated_at' => $now,
    ], ['%s', '%s', '%s', '%d', '%s', '%s']);
}

function aitrongcay_preferred_garden_key_for_user(?WP_User $user = null): string
{
    $user = $user instanceof WP_User ? $user : wp_get_current_user();
    return aitrongcay_current_garden_key($user);
}

function aitrongcay_seed_owner_membership(): void
{
    if (! is_user_logged_in()) {
        return;
    }

    global $wpdb;
    $user = wp_get_current_user();
    $user_id = (int) ($user->ID ?? 0);
    if ($user_id <= 0) {
        return;
    }

    $garden_key = aitrongcay_preferred_garden_key_for_user($user);
    $table = aitrongcay_garden_members_table();
    $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE garden_key = %s AND user_id = %d LIMIT 1", $garden_key, $user_id));
    if (! $exists) {
        $wpdb->insert($table, [
            'garden_key' => $garden_key,
            'user_id' => $user_id,
            'role' => 'owner',
            'status' => 'active',
            'invited_by_user_id' => null,
            'created_at' => current_time('mysql', true),
            'updated_at' => current_time('mysql', true),
        ]);
    } else {
        $wpdb->update(
            $table,
            [
                'role' => 'owner',
                'status' => 'active',
                'updated_at' => current_time('mysql', true),
            ],
            [
                'id' => (int) $exists,
            ],
            ['%s', '%s', '%s'],
            ['%d']
        );
    }

    aitrongcay_remember_selected_garden_key($user_id, $garden_key);
}
add_action('wp', 'aitrongcay_seed_owner_membership');

function aitrongcay_market_owner_garden_key_from_post(WP_Post $post): string
{
    $garden_key = trim((string) get_post_meta($post->ID, '_aitrongcay_market_garden_key', true));
    if ($garden_key !== '') {
        return $garden_key;
    }

    $author = get_user_by('id', (int) $post->post_author);
    return aitrongcay_current_garden_key($author instanceof WP_User ? $author : null);
}

function aitrongcay_market_context_garden_key(): string
{
    if (! is_user_logged_in()) {
        return '';
    }

    return aitrongcay_resolve_active_garden_key(wp_get_current_user());
}

function aitrongcay_migrate_attachment_garden_key(int $attachment_id): string
{
    $garden_key = trim((string) get_post_meta($attachment_id, '_aitrongcay_photo_garden_key', true));
    if ($garden_key !== '') {
        return $garden_key;
    }

    $attachment = get_post($attachment_id);
    if (! $attachment || $attachment->post_type !== 'attachment') {
        return '';
    }

    $owner_id = (int) get_post_meta($attachment_id, '_aitrongcay_photo_owner', true);
    if ($owner_id <= 0) {
        $owner_id = (int) $attachment->post_author;
    }

    $garden_key = '';
    if ($owner_id > 0) {
        $owner = get_user_by('id', $owner_id);
        $garden_key = aitrongcay_current_garden_key($owner instanceof WP_User ? $owner : null);
    }

    if ($garden_key === '') {
        $parent_id = (int) $attachment->post_parent;
        if ($parent_id > 0) {
            $parent_post = get_post($parent_id);
            if ($parent_post instanceof WP_Post) {
                $garden_key = aitrongcay_market_owner_garden_key_from_post($parent_post);
            }
        }
    }

    if ($garden_key !== '') {
        update_post_meta($attachment_id, '_aitrongcay_photo_garden_key', $garden_key);
    }

    return $garden_key;
}

function aitrongcay_migrate_market_post_garden_key(int $post_id): string
{
    $post = get_post($post_id);
    if (! $post || $post->post_type !== 'aitr_market_post') {
        return '';
    }

    $garden_key = trim((string) get_post_meta($post_id, '_aitrongcay_market_garden_key', true));
    if ($garden_key === '') {
        $garden_key = aitrongcay_current_garden_key(get_user_by('id', (int) $post->post_author) ?: null);
        if ($garden_key !== '') {
            update_post_meta($post_id, '_aitrongcay_market_garden_key', $garden_key);
        }
    }

    $gallery = array_map('absint', (array) get_post_meta($post_id, '_aitrongcay_market_gallery', true));
    foreach ($gallery as $attachment_id) {
        $attachment_garden = aitrongcay_migrate_attachment_garden_key($attachment_id);
        if ($garden_key === '' && $attachment_garden !== '') {
            $garden_key = $attachment_garden;
            update_post_meta($post_id, '_aitrongcay_market_garden_key', $garden_key);
        }
    }

    $thumb_id = (int) get_post_thumbnail_id($post_id);
    if ($thumb_id > 0) {
        $thumb_garden = aitrongcay_migrate_attachment_garden_key($thumb_id);
        if ($garden_key === '' && $thumb_garden !== '') {
            $garden_key = $thumb_garden;
            update_post_meta($post_id, '_aitrongcay_market_garden_key', $garden_key);
        }
    }

    return $garden_key;
}

function aitrongcay_migrate_legacy_garden_media(int $limit = 80): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $attachments = get_posts([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'posts_per_page' => $limit,
        'orderby' => 'date',
        'order' => 'DESC',
        'fields' => 'ids',
        'meta_query' => [
            [
                'key' => '_aitrongcay_photo_garden_key',
                'compare' => 'NOT EXISTS',
            ],
        ],
    ]);
    foreach ($attachments as $attachment_id) {
        aitrongcay_migrate_attachment_garden_key((int) $attachment_id);
    }

    $market_posts = get_posts([
        'post_type' => 'aitr_market_post',
        'post_status' => 'publish',
        'posts_per_page' => $limit,
        'orderby' => 'date',
        'order' => 'DESC',
        'fields' => 'ids',
        'meta_query' => [
            [
                'key' => '_aitrongcay_market_garden_key',
                'compare' => 'NOT EXISTS',
            ],
        ],
    ]);
    foreach ($market_posts as $market_post_id) {
        aitrongcay_migrate_market_post_garden_key((int) $market_post_id);
    }
}
add_action('wp', static function (): void {
    aitrongcay_migrate_legacy_garden_media();
}, 15);

function aitrongcay_market_posts_query_args(string $garden_key = '', int $posts_per_page = 24): array
{
    $args = [
        'post_type' => 'aitr_market_post',
        'post_status' => 'publish',
        'posts_per_page' => $posts_per_page,
        'orderby' => 'date',
        'order' => 'DESC',
    ];

    $meta_query = [];
    if ($garden_key !== '') {
        $meta_query[] = [
            'key' => '_aitrongcay_market_garden_key',
            'value' => $garden_key,
        ];
    }

    $category = sanitize_text_field((string) ($_GET['market_category'] ?? ''));
    if ($category !== '') {
        $meta_query[] = [
            'key' => '_aitrongcay_market_category',
            'value' => $category,
        ];
    }

    $offerType = sanitize_text_field((string) ($_GET['market_offer_type'] ?? ''));
    if ($offerType !== '') {
        $meta_query[] = [
            'key' => '_aitrongcay_market_offer_type',
            'value' => $offerType,
        ];
    }

    if ($meta_query) {
        $args['meta_query'] = $meta_query;
    }

    $sort = sanitize_key((string) ($_GET['market_sort'] ?? 'newest'));
    if ($sort === 'popular') {
        $args['meta_key'] = '_aitrongcay_market_share_count';
        $args['orderby'] = 'meta_value_num date';
        $args['order'] = 'DESC';
    }

    return $args;
}

function aitrongcay_blynk_get_status_ajax(): void
{
    aitrongcay_require_portal_nonce();

    $garden_key = sanitize_text_field((string) wp_unslash($_POST['garden_key'] ?? aitrongcay_market_context_garden_key()));
    if ($garden_key !== '' && ! aitrongcay_user_can_view_garden($garden_key, get_current_user_id())) {
        wp_send_json_error(['message' => 'Không có quyền xem trạng thái khu vườn này.'], 403);
    }

    $cfg = aitrongcay_blynk_config($garden_key);
    $base = (string) ($cfg['base'] ?? 'https://blynk.cloud/external/api');
    $vpins = (array) ($cfg['vpins'] ?? []);

    $payload = ['garden_key' => $garden_key];
    $shared_vpins = [];
    foreach (['temp', 'hum', 'soil', 'pump'] as $device) {
        if (! empty($vpins[$device])) {
            $shared_vpins[$device] = (string) $vpins[$device];
        }
    }

    $shared_data = aitrongcay_blynk_read_values((string) ($cfg['token'] ?? ''), array_values($shared_vpins), $base);
    foreach ($shared_vpins as $device => $vpin) {
        $value = $shared_data[$vpin] ?? null;
        if (in_array($device, ['temp', 'hum', 'soil'], true)) {
            $payload[$device] = $value !== null ? (float) $value : null;
        } else {
            $payload[$device] = $value !== null ? (int) $value : null;
        }
    }

    $light_devices = ['light1', 'light2', 'light3', 'light4'];
    $has_any_light = false;
    $light_requests = [];
    foreach ($light_devices as $device) {
        $vpin = (string) ($vpins[$device] ?? '');
        if ($vpin === '') {
            $payload[$device] = null;
            continue;
        }

        $token = aitrongcay_blynk_pot_token_for_device($garden_key, $device);
        if ($token === '') {
            $token = trim((string) ($cfg['token'] ?? ''));
        }
        if ($token === '') {
            $payload[$device] = null;
            continue;
        }

        if (! isset($light_requests[$token])) {
            $light_requests[$token] = [
                'vpins' => [],
                'devices' => [],
            ];
        }
        $light_requests[$token]['vpins'][] = $vpin;
        $light_requests[$token]['devices'][$device] = $vpin;
    }

    foreach ($light_requests as $token => $request) {
        $light_data = aitrongcay_blynk_read_values((string) $token, array_values(array_unique((array) ($request['vpins'] ?? []))), $base);
        foreach ((array) ($request['devices'] ?? []) as $device => $vpin) {
            $value = $light_data[$vpin] ?? null;
            $payload[$device] = $value !== null ? (int) $value : null;
            if ($value !== null) {
                $has_any_light = true;
            }
        }
    }

    if ($shared_data === [] && ! $has_any_light) {
        wp_send_json_error(['message' => 'Không đọc được dữ liệu Blynk.'], 502);
    }

    wp_send_json_success($payload);
}
add_action('wp_ajax_aitrongcay_blynk_get_status', 'aitrongcay_blynk_get_status_ajax');

function aitrongcay_blynk_send_control(string $device, int $state, string $garden_key = '')
{
    if (! in_array($state, [0, 1], true)) {
        return new WP_Error('invalid_command', 'Lệnh điều khiển không hợp lệ.');
    }

    $cfg = aitrongcay_blynk_config($garden_key);
    $vpin = (string) ($cfg['vpins'][$device] ?? '');
    if ($vpin === '') {
        return new WP_Error('invalid_command', 'Thiết bị này chưa được map cho khu vườn đang chọn.');
    }

    $token = $device === 'pump'
        ? trim((string) ($cfg['token'] ?? ''))
        : aitrongcay_blynk_pot_token_for_device($garden_key, $device);
    if ($token === '') {
        $token = trim((string) ($cfg['token'] ?? ''));
    }
    if ($token === '') {
        return new WP_Error('missing_token', 'Thiết bị này chưa có Blynk token thật để gửi lệnh.');
    }

    $url = add_query_arg([
        'token' => $token,
        $vpin => $state,
    ], untrailingslashit((string) ($cfg['base'] ?? 'https://blynk.cloud/external/api')) . '/update');

    $response = wp_remote_get($url, ['timeout' => 10]);
    if (is_wp_error($response)) {
        return $response;
    }

    $code = (int) wp_remote_retrieve_response_code($response);
    if ($code < 200 || $code >= 300) {
        return new WP_Error('blynk_http_error', 'Blynk phản hồi HTTP ' . $code);
    }

    return true;
}

function aitrongcay_blynk_control_ajax(): void
{
    aitrongcay_require_portal_nonce();

    $garden_key = sanitize_text_field((string) ($_POST['garden_key'] ?? aitrongcay_current_garden_key()));
    if (! aitrongcay_user_can_control_garden($garden_key, get_current_user_id())) {
        wp_send_json_error(['message' => 'Anh/chị chỉ có quyền xem khu vườn này.'], 403);
    }

    $device = sanitize_key((string) ($_POST['device'] ?? ''));
    $state = isset($_POST['state']) ? (int) $_POST['state'] : -1;

    $result = aitrongcay_blynk_send_control($device, $state, $garden_key);
    if (is_wp_error($result)) {
        wp_send_json_error(['message' => $result->get_error_message()], 400);
    }

    wp_send_json_success(['device' => $device, 'state' => $state]);
}
add_action('wp_ajax_aitrongcay_blynk_control', 'aitrongcay_blynk_control_ajax');

function aitrongcay_blynk_control_direct_url(string $device, int $state): string
{
    $url = add_query_arg([
        'action' => 'aitrongcay_blynk_control_direct',
        'device' => $device,
        'state' => $state,
        'redirect_to' => home_url('/portal/dashboard/'),
    ], admin_url('admin-post.php'));

    return wp_nonce_url($url, 'aitrongcay_blynk_control_direct');
}

function aitrongcay_blynk_control_direct_submit(): void
{
    if (! is_user_logged_in()) {
        wp_safe_redirect(home_url('/dang-nhap/?auth_status=login-required'));
        exit;
    }

    check_admin_referer('aitrongcay_blynk_control_direct');

    $device = sanitize_key((string) ($_GET['device'] ?? $_POST['device'] ?? ''));
    $garden_key = sanitize_text_field((string) ($_GET['garden_key'] ?? $_POST['garden_key'] ?? aitrongcay_resolve_active_garden_key(wp_get_current_user())));
    $state = isset($_GET['state']) ? (int) $_GET['state'] : (isset($_POST['state']) ? (int) $_POST['state'] : -1);

    $result = aitrongcay_blynk_send_control($device, $state, $garden_key);

    $redirect = $garden_key !== '' ? add_query_arg('garden', rawurlencode($garden_key), home_url('/portal/dashboard/')) : home_url('/portal/dashboard/');
    if (is_wp_error($result)) {
        $redirect = add_query_arg('blynk_ctrl', rawurlencode($result->get_error_message()), $redirect);
    } else {
        $redirect = add_query_arg('blynk_ctrl', rawurlencode('ok:' . $device . ':' . $state), $redirect);
    }

    wp_safe_redirect($redirect);
    exit;
}
add_action('admin_post_aitrongcay_blynk_control_direct', 'aitrongcay_blynk_control_direct_submit');

function aitrongcay_capture_photo_ajax(): void
{
    aitrongcay_require_portal_nonce();

    $image_data = (string) wp_unslash($_POST['image'] ?? '');
    if (! preg_match('#^data:image/(png|jpeg);base64,#', $image_data, $matches)) {
        wp_send_json_error(['message' => 'Không đọc được ảnh chụp.'], 400);
    }

    $binary = base64_decode(substr($image_data, strpos($image_data, ',') + 1), true);
    if ($binary === false) {
        wp_send_json_error(['message' => 'Dữ liệu ảnh không hợp lệ.'], 400);
    }

    $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
    $filename = 'livecam-' . get_current_user_id() . '-' . wp_generate_password(8, false, false) . '.' . $extension;
    $uploaded = wp_upload_bits($filename, null, $binary);
    if (! empty($uploaded['error'])) {
        wp_send_json_error(['message' => $uploaded['error']], 500);
    }

    $filetype = wp_check_filetype($uploaded['file'], null);
    $attachment_id = wp_insert_attachment([
        'post_mime_type' => $filetype['type'] ?: 'image/' . $extension,
        'post_title' => sanitize_file_name(pathinfo($filename, PATHINFO_FILENAME)),
        'post_status' => 'inherit',
        'post_author' => get_current_user_id(),
    ], $uploaded['file']);

    if (is_wp_error($attachment_id) || ! $attachment_id) {
        wp_send_json_error(['message' => 'Không tạo được attachment.'], 500);
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';
    $attach_data = wp_generate_attachment_metadata($attachment_id, $uploaded['file']);
    wp_update_attachment_metadata($attachment_id, $attach_data);
    update_post_meta($attachment_id, '_aitrongcay_photo_owner', get_current_user_id());
    update_post_meta($attachment_id, '_aitrongcay_photo_garden_key', aitrongcay_resolve_active_garden_key());
    update_post_meta($attachment_id, '_aitrongcay_photo_source', 'livecam');

    $image_url = wp_get_attachment_image_url($attachment_id, 'medium_large') ?: wp_get_attachment_url($attachment_id);

    wp_send_json_success([
        'id' => $attachment_id,
        'url' => set_url_scheme((string) $image_url, 'https'),
        'label' => get_the_title($attachment_id),
    ]);
}
add_action('wp_ajax_aitrongcay_capture_photo', 'aitrongcay_capture_photo_ajax');

function aitrongcay_generate_demo_snapshot_attachment(int $user_id)
{
    if (! function_exists('imagecreatetruecolor')) {
        return new WP_Error('gd_missing', 'Server chưa bật GD để tạo ảnh demo.');
    }

    $width = 1600;
    $height = 900;
    $image = imagecreatetruecolor($width, $height);
    if (! $image) {
        return new WP_Error('canvas_failed', 'Không tạo được canvas ảnh demo.');
    }

    $bg = imagecolorallocate($image, 236, 245, 238);
    $green = imagecolorallocate($image, 31, 107, 69);
    $green2 = imagecolorallocate($image, 123, 196, 127);
    $white = imagecolorallocate($image, 255, 255, 255);
    $dark = imagecolorallocate($image, 26, 43, 36);

    imagefilledrectangle($image, 0, 0, $width, $height, $bg);
    imagefilledrectangle($image, 80, 90, 1520, 810, $white);
    imagefilledrectangle($image, 80, 90, 1520, 240, $green);
    imagefilledellipse($image, 420, 470, 420, 420, $green2);
    imagefilledellipse($image, 760, 500, 300, 300, $green);
    imagefilledellipse($image, 1080, 450, 360, 360, $green2);
    imagefilledrectangle($image, 160, 620, 1440, 700, $green);
    imagestring($image, 5, 130, 130, 'Ai trong cay - Snapshot demo', $white);
    imagestring($image, 4, 130, 275, 'Vuon dang duoc theo doi trong portal', $dark);
    imagestring($image, 4, 130, 315, 'Anh nay duoc tao boi server de demo luong chup anh on dinh', $dark);
    imagestring($image, 4, 130, 355, 'Sau nay co the doi sang snapshot that khi livecam cung domain', $dark);

    ob_start();
    imagepng($image);
    $contents = ob_get_clean();
    imagedestroy($image);

    if (! is_string($contents) || $contents === '') {
        return new WP_Error('render_failed', 'Không render được ảnh demo.');
    }

    $filename = 'anh-vuon-' . $user_id . '-' . wp_generate_password(8, false, false) . '.png';
    $uploaded = wp_upload_bits($filename, null, $contents);
    if (! empty($uploaded['error'])) {
        return new WP_Error('upload_failed', (string) $uploaded['error']);
    }

    $attachment_id = wp_insert_attachment([
        'post_mime_type' => 'image/png',
        'post_title' => sanitize_file_name(pathinfo($filename, PATHINFO_FILENAME)),
        'post_status' => 'inherit',
        'post_author' => $user_id,
    ], $uploaded['file']);

    if (is_wp_error($attachment_id) || ! $attachment_id) {
        return new WP_Error('attachment_failed', 'Không tạo được attachment demo.');
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';
    $attach_data = wp_generate_attachment_metadata($attachment_id, $uploaded['file']);
    wp_update_attachment_metadata($attachment_id, $attach_data);
    update_post_meta($attachment_id, '_aitrongcay_photo_owner', $user_id);
    update_post_meta($attachment_id, '_aitrongcay_photo_garden_key', aitrongcay_current_garden_key(get_user_by('id', $user_id) ?: null));
    update_post_meta($attachment_id, '_aitrongcay_photo_source', 'livecam');

    return [
        'id' => $attachment_id,
        'url' => set_url_scheme((string) (wp_get_attachment_image_url($attachment_id, 'medium_large') ?: wp_get_attachment_url($attachment_id)), 'https'),
        'label' => get_the_title($attachment_id),
    ];
}

function aitrongcay_capture_demo_photo_ajax(): void
{
    aitrongcay_require_portal_nonce();
    $result = aitrongcay_generate_demo_snapshot_attachment(get_current_user_id());
    if (is_wp_error($result)) {
        wp_send_json_error(['message' => $result->get_error_message()], 500);
    }
    wp_send_json_success($result);
}
add_action('wp_ajax_aitrongcay_capture_demo_photo', 'aitrongcay_capture_demo_photo_ajax');

function aitrongcay_capture_demo_photo_submit(): void
{
    if (! is_user_logged_in()) {
        wp_safe_redirect(home_url('/dang-nhap/?auth_status=login-required'));
        exit;
    }

    check_admin_referer('aitrongcay_capture_demo_photo_submit', 'aitrongcay_capture_demo_nonce');
    $result = aitrongcay_generate_demo_snapshot_attachment(get_current_user_id());
    $redirect = home_url('/portal/dashboard/#photo-library');
    if (! is_wp_error($result) && ! empty($result['id'])) {
        $redirect = add_query_arg('photo_added', (string) $result['id'], home_url('/portal/dashboard/')) . '#photo-library';
    }
    wp_safe_redirect($redirect);
    exit;
}
add_action('admin_post_aitrongcay_capture_demo_photo_submit', 'aitrongcay_capture_demo_photo_submit');

function aitrongcay_create_public_livecam_ajax(): void
{
    aitrongcay_require_portal_nonce();

    $token = wp_generate_password(24, false, false);
    update_user_meta(get_current_user_id(), '_aitrongcay_livecam_public_token', $token);

    wp_send_json_success([
        'token' => $token,
        'url' => add_query_arg('share_token', rawurlencode($token), home_url('/portal/webcam/')),
    ]);
}
add_action('wp_ajax_aitrongcay_create_public_livecam', 'aitrongcay_create_public_livecam_ajax');

function aitrongcay_disable_public_livecam_ajax(): void
{
    aitrongcay_require_portal_nonce();
    delete_user_meta(get_current_user_id(), '_aitrongcay_livecam_public_token');
    wp_send_json_success(['disabled' => true]);
}
add_action('wp_ajax_aitrongcay_disable_public_livecam', 'aitrongcay_disable_public_livecam_ajax');

function aitrongcay_market_structured_fields_schema(): array
{
    return [
        'category' => ['meta_key' => '_aitrongcay_market_category', 'type' => 'text'],
        'offer_type' => ['meta_key' => '_aitrongcay_market_offer_type', 'type' => 'text'],
        'quantity' => ['meta_key' => '_aitrongcay_market_quantity', 'type' => 'text'],
        'area' => ['meta_key' => '_aitrongcay_market_area', 'type' => 'text'],
        'availability' => ['meta_key' => '_aitrongcay_market_availability', 'type' => 'text'],
        'contact_text' => ['meta_key' => '_aitrongcay_market_contact_text', 'type' => 'text'],
    ];
}

function aitrongcay_get_market_structured_data(int $post_id): array
{
    $data = [];
    foreach (aitrongcay_market_structured_fields_schema() as $field => $config) {
        $data[$field] = trim((string) get_post_meta($post_id, (string) $config['meta_key'], true));
    }
    return $data;
}

function aitrongcay_save_market_structured_data(int $post_id, array $source): void
{
    foreach (aitrongcay_market_structured_fields_schema() as $field => $config) {
        $value = sanitize_text_field((string) wp_unslash($source[$field] ?? ''));
        if ($value === '') {
            delete_post_meta($post_id, (string) $config['meta_key']);
        } else {
            update_post_meta($post_id, (string) $config['meta_key'], $value);
        }
    }
}

function aitrongcay_market_summary_line(array $data): string
{
    $parts = array_values(array_filter([
        trim((string) ($data['offer_type'] ?? '')),
        trim((string) ($data['quantity'] ?? '')),
        trim((string) ($data['area'] ?? '')),
    ]));
    return implode(' • ', $parts);
}

function aitrongcay_migrate_market_structured_meta(): void
{
    if ((string) get_option('aitrongcay_market_structured_meta_version', '') === '1') {
        return;
    }

    $posts = get_posts([
        'post_type' => 'aitr_market_post',
        'post_status' => 'publish',
        'posts_per_page' => 200,
        'orderby' => 'date',
        'order' => 'DESC',
        'fields' => 'ids',
    ]);

    foreach ($posts as $post_id) {
        $post_id = (int) $post_id;
        $content = wp_strip_all_tags((string) get_post_field('post_content', $post_id));
        $title = (string) get_post_field('post_title', $post_id);
        $existing = aitrongcay_get_market_structured_data($post_id);

        if (($existing['offer_type'] ?? '') === '') {
            foreach (['Bán', 'Trao đổi', 'Chia sẻ', 'Nhận đặt trước'] as $offer) {
                if (str_contains(mb_strtolower($title . ' ' . $content), mb_strtolower($offer))) {
                    update_post_meta($post_id, '_aitrongcay_market_offer_type', $offer);
                    break;
                }
            }
        }

        if (($existing['category'] ?? '') === '') {
            $map = [
                'Hạt giống' => ['hạt', 'hạt giống'],
                'Cây giống' => ['cây giống', 'cây con'],
                'Dinh dưỡng cho cây' => ['dinh dưỡng', 'phân', 'giá thể'],
                'Các loại rau' => ['rau', 'xà lách', 'cải', 'rau muống'],
                'Hoa' => ['hoa', 'nụ', 'cúc'],
            ];
            foreach ($map as $category => $keywords) {
                foreach ($keywords as $keyword) {
                    if (str_contains(mb_strtolower($title . ' ' . $content), mb_strtolower($keyword))) {
                        update_post_meta($post_id, '_aitrongcay_market_category', $category);
                        break 2;
                    }
                }
            }
        }
    }

    update_option('aitrongcay_market_structured_meta_version', '1', false);
}
add_action('init', 'aitrongcay_migrate_market_structured_meta', 40);

function aitrongcay_upload_market_photo_ajax(): void
{
    aitrongcay_require_portal_nonce();

    if (empty($_FILES['market_photo']) || ! is_array($_FILES['market_photo'])) {
        wp_send_json_error(['message' => 'Chưa nhận được ảnh tải lên.'], 400);
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $attachment_id = media_handle_upload('market_photo', 0);
    if (is_wp_error($attachment_id) || ! $attachment_id) {
        wp_send_json_error(['message' => 'Không tải được ảnh lên hệ thống.'], 500);
    }

    update_post_meta($attachment_id, '_aitrongcay_photo_owner', get_current_user_id());
    update_post_meta($attachment_id, '_aitrongcay_photo_garden_key', aitrongcay_resolve_active_garden_key());

    wp_send_json_success([
        'id' => $attachment_id,
        'url' => wp_get_attachment_image_url($attachment_id, 'medium_large') ?: wp_get_attachment_url($attachment_id),
        'title' => get_the_title($attachment_id),
    ]);
}
add_action('wp_ajax_aitrongcay_upload_market_photo', 'aitrongcay_upload_market_photo_ajax');

function aitrongcay_create_market_post_ajax(): void
{
    aitrongcay_require_portal_nonce();

    $title = sanitize_text_field((string) wp_unslash($_POST['title'] ?? ''));
    $content = wp_kses_post((string) wp_unslash($_POST['content'] ?? ''));
    $photo_ids = array_map('absint', (array) ($_POST['photo_ids'] ?? []));
    $photo_ids = array_values(array_filter($photo_ids));

    if ($title === '' || $content === '') {
        wp_send_json_error(['message' => 'Thiếu tiêu đề hoặc nội dung tin đăng.'], 400);
    }

    if (function_exists('mb_strlen')) {
        if (mb_strlen($title) < 12) {
            wp_send_json_error(['message' => 'Tiêu đề hơi ngắn. Anh/chị nên viết rõ hơn để người xem hiểu ngay tin đăng nói về gì.'], 400);
        }
        if (mb_strlen(wp_strip_all_tags($content)) < 24) {
            wp_send_json_error(['message' => 'Nội dung còn quá ngắn. Anh/chị nên thêm số lượng, khu vực hoặc cách liên hệ.'], 400);
        }
    }

    $post_id = wp_insert_post([
        'post_type' => 'aitr_market_post',
        'post_status' => 'publish',
        'post_title' => $title,
        'post_content' => $content,
        'post_author' => get_current_user_id(),
        'comment_status' => 'open',
    ], true);

    if (is_wp_error($post_id) || ! $post_id) {
        wp_send_json_error(['message' => 'Không tạo được tin Chợ quê.'], 500);
    }

    $market_garden_key = aitrongcay_resolve_active_garden_key();
    update_post_meta($post_id, '_aitrongcay_market_garden_key', $market_garden_key);
    aitrongcay_save_market_structured_data($post_id, $_POST);

    if ($photo_ids) {
        set_post_thumbnail($post_id, $photo_ids[0]);
        update_post_meta($post_id, '_aitrongcay_market_gallery', $photo_ids);
    }

    wp_send_json_success([
        'id' => $post_id,
        'url' => add_query_arg('created_post', (string) $post_id, home_url('/cho-que/#market-drafts')),
        'title' => get_the_title($post_id),
        'structured' => aitrongcay_get_market_structured_data($post_id),
    ]);
}
add_action('wp_ajax_aitrongcay_create_market_post', 'aitrongcay_create_market_post_ajax');

function aitrongcay_update_market_post_ajax(): void
{
    aitrongcay_require_portal_nonce();
    $post_id = absint($_POST['post_id'] ?? 0);
    $post = get_post($post_id);
    if (! $post || $post->post_type !== 'aitr_market_post' || (int) $post->post_author !== get_current_user_id()) {
        wp_send_json_error(['message' => 'Không có quyền sửa tin này.'], 403);
    }

    $title = sanitize_text_field((string) wp_unslash($_POST['title'] ?? ''));
    $content = wp_kses_post((string) wp_unslash($_POST['content'] ?? ''));
    $new_photo_ids = array_map('absint', (array) ($_POST['photo_ids'] ?? []));
    $new_photo_ids = array_values(array_filter($new_photo_ids));
    $existing_photo_ids = array_map('absint', (array) ($_POST['existing_photo_ids'] ?? []));
    $existing_photo_ids = array_values(array_filter($existing_photo_ids));

    if ($title === '' || $content === '') {
        wp_send_json_error(['message' => 'Anh/chị vui lòng nhập đủ tiêu đề và nội dung tin đăng.'], 400);
    }
    if (function_exists('mb_strlen')) {
        if (mb_strlen($title) < 12) {
            wp_send_json_error(['message' => 'Tiêu đề hơi ngắn. Anh/chị nên viết rõ hơn để người xem hiểu ngay tin đăng nói về gì.'], 400);
        }
        if (mb_strlen(wp_strip_all_tags($content)) < 24) {
            wp_send_json_error(['message' => 'Nội dung còn quá ngắn. Anh/chị nên thêm số lượng, khu vực hoặc cách liên hệ.'], 400);
        }
    }

    $final_photo_ids = array_values(array_filter(array_merge($existing_photo_ids, $new_photo_ids)));

    wp_update_post(['ID' => $post_id, 'post_title' => $title, 'post_content' => $content]);
    aitrongcay_save_market_structured_data($post_id, $_POST);

    $market_garden_key = trim((string) get_post_meta($post_id, '_aitrongcay_market_garden_key', true));
    if ($market_garden_key === '') {
        update_post_meta($post_id, '_aitrongcay_market_garden_key', aitrongcay_current_garden_key(get_user_by('id', (int) $post->post_author) ?: null));
    }

    if ($final_photo_ids) {
        set_post_thumbnail($post_id, $final_photo_ids[0]);
        update_post_meta($post_id, '_aitrongcay_market_gallery', $final_photo_ids);
    } else {
        delete_post_thumbnail($post_id);
        delete_post_meta($post_id, '_aitrongcay_market_gallery');
    }

    $gallery_items = [];
    foreach ($final_photo_ids as $attachment_id) {
        $url = (string) (wp_get_attachment_image_url($attachment_id, 'large') ?: wp_get_attachment_url($attachment_id));
        if (! $url) {
            continue;
        }
        $gallery_items[] = [
            'id' => $attachment_id,
            'url' => set_url_scheme($url, 'https'),
            'title' => get_the_title($attachment_id),
        ];
    }

    $thumb_url = has_post_thumbnail($post_id) ? (string) get_the_post_thumbnail_url($post_id, 'large') : '';
    $gallery_url = ($gallery_items && ! empty($gallery_items[0]['url'])) ? (string) $gallery_items[0]['url'] : '';
    $image_url = $thumb_url ? set_url_scheme($thumb_url, 'https') : $gallery_url;

    wp_send_json_success([
        'id' => $post_id,
        'title' => $title,
        'content' => wp_strip_all_tags($content),
        'imageUrl' => $image_url ?: '',
        'gallery' => $gallery_items,
        'structured' => aitrongcay_get_market_structured_data($post_id),
        'summaryLine' => aitrongcay_market_summary_line(aitrongcay_get_market_structured_data($post_id)),
    ]);
}
add_action('wp_ajax_aitrongcay_update_market_post', 'aitrongcay_update_market_post_ajax');

function aitrongcay_delete_market_post_ajax(): void
{
    aitrongcay_require_portal_nonce();
    $post_id = absint($_POST['post_id'] ?? 0);
    $post = get_post($post_id);
    if (! $post || $post->post_type !== 'aitr_market_post' || (int) $post->post_author !== get_current_user_id()) {
        wp_send_json_error(['message' => 'Không có quyền xóa tin này.'], 403);
    }
    wp_trash_post($post_id);
    wp_send_json_success(['id' => $post_id]);
}
add_action('wp_ajax_aitrongcay_delete_market_post', 'aitrongcay_delete_market_post_ajax');

function aitrongcay_normalize_phone_digits(string $phone): string
{
    $digits = preg_replace('/\D+/', '', $phone) ?: '';
    if ($digits === '') {
        return '';
    }
    if (str_starts_with($digits, '84')) {
        return $digits;
    }
    if (str_starts_with($digits, '0')) {
        return '84' . substr($digits, 1);
    }
    return $digits;
}

function aitrongcay_user_zalo_phone(int $user_id): string
{
    if ($user_id <= 0) {
        return '';
    }
    $phone = (string) get_user_meta($user_id, 'aitrongcay_phone', true);
    return aitrongcay_normalize_phone_digits($phone);
}

function aitrongcay_market_zalo_link_for_post(int $post_id): string
{
    $post = get_post($post_id);
    if (! $post || $post->post_type !== 'aitr_market_post') {
        return '';
    }
    $phone = aitrongcay_user_zalo_phone((int) $post->post_author);
    if ($phone === '') {
        return '';
    }
    return 'https://zalo.me/' . rawurlencode($phone);
}

function aitrongcay_market_zalo_action_url(int $post_id): string
{
    $post_id = absint($post_id);
    if ($post_id <= 0) {
        return home_url('/cho-que/');
    }
    return add_query_arg('aitrongcay_market_zalo', (string) $post_id, home_url('/cho-que/'));
}

add_action('template_redirect', static function (): void {
    $post_id = absint($_GET['aitrongcay_market_zalo'] ?? 0);
    if ($post_id <= 0) {
        return;
    }

    $url = aitrongcay_market_zalo_link_for_post($post_id);
    if ($url === '') {
        wp_safe_redirect(home_url('/cho-que/'));
        exit;
    }

    wp_redirect($url, 302, 'AiTrongCay');
    exit;
}, 1);

function aitrongcay_get_market_zalo_link_ajax(): void
{
    aitrongcay_require_portal_nonce();
    $post_id = absint($_POST['post_id'] ?? 0);
    $url = aitrongcay_market_zalo_link_for_post($post_id);
    if ($url === '') {
        wp_send_json_error(['message' => 'Tin đăng này chưa có Zalo sẵn sàng để liên hệ.'], 404);
    }
    wp_send_json_success([
        'url' => $url,
        'post_id' => $post_id,
        'mode' => 'zalo-deeplink',
    ]);
}
add_action('wp_ajax_aitrongcay_get_market_zalo_link', 'aitrongcay_get_market_zalo_link_ajax');
add_action('wp_ajax_nopriv_aitrongcay_get_market_zalo_link', 'aitrongcay_get_market_zalo_link_ajax');

function aitrongcay_custom_pots_meta_key(): string
{
    return '_aitrongcay_custom_pots_by_garden';
}

function aitrongcay_get_custom_pots(string $garden_key, int $user_id): array
{
    if ($garden_key === '') {
        return [];
    }

    $db_pots = function_exists('aitrongcay_get_db_pots') ? aitrongcay_get_db_pots($garden_key) : [];
    if ($db_pots) {
        return array_map(static function (array $pot): array {
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
    }

    if ($user_id <= 0) {
        return [];
    }

    $bucket = get_user_meta($user_id, aitrongcay_custom_pots_meta_key(), true);
    if (! is_array($bucket)) {
        return [];
    }

    $pots = $bucket[$garden_key] ?? [];
    return is_array($pots) ? array_values(array_filter($pots, 'is_array')) : [];
}

function aitrongcay_store_custom_pots(string $garden_key, int $user_id, array $pots): void
{
    if ($garden_key === '') {
        return;
    }

    if (function_exists('aitrongcay_upsert_db_pot')) {
        foreach (array_values($pots) as $index => $pot) {
            if (! is_array($pot)) continue;
            $pot['sort_order'] = $index + 1;
            aitrongcay_upsert_db_pot($garden_key, $pot);
        }
    }

    if ($user_id <= 0) {
        return;
    }

    $bucket = get_user_meta($user_id, aitrongcay_custom_pots_meta_key(), true);
    if (! is_array($bucket)) {
        $bucket = [];
    }

    $bucket[$garden_key] = array_values($pots);
    update_user_meta($user_id, aitrongcay_custom_pots_meta_key(), $bucket);
}

function aitrongcay_pot_name_overrides_meta_key(): string
{
    return '_aitrongcay_pot_name_overrides_by_garden';
}

function aitrongcay_get_pot_name_overrides(string $garden_key, int $user_id): array
{
    if ($garden_key === '' || $user_id <= 0) {
        return [];
    }

    $bucket = get_user_meta($user_id, aitrongcay_pot_name_overrides_meta_key(), true);
    if (! is_array($bucket)) {
        return [];
    }

    $overrides = $bucket[$garden_key] ?? [];
    return is_array($overrides) ? $overrides : [];
}

function aitrongcay_store_pot_name_overrides(string $garden_key, int $user_id, array $overrides): void
{
    if ($garden_key === '' || $user_id <= 0) {
        return;
    }

    $bucket = get_user_meta($user_id, aitrongcay_pot_name_overrides_meta_key(), true);
    if (! is_array($bucket)) {
        $bucket = [];
    }

    $bucket[$garden_key] = $overrides;
    update_user_meta($user_id, aitrongcay_pot_name_overrides_meta_key(), $bucket);
}

function aitrongcay_update_pot_name_for_garden(string $garden_key, int $user_id, string $pot_code, string $pot_name): bool
{
    $garden_key = trim($garden_key);
    $pot_code = trim($pot_code);
    $pot_name = trim($pot_name);

    if ($garden_key === '' || $user_id <= 0 || $pot_code === '' || $pot_name === '') {
        return false;
    }

    $custom_pots = aitrongcay_get_custom_pots($garden_key, $user_id);
    $custom_updated = false;
    foreach ($custom_pots as &$pot) {
        if ((string) ($pot['code'] ?? '') !== $pot_code) {
            continue;
        }
        $pot['name'] = $pot_name;
        if (empty($pot['trays']) || ! is_array($pot['trays'])) {
            $pot['trays'] = [$pot_name];
        } else {
            $pot['trays'][0] = $pot_name;
        }
        $custom_updated = true;
        break;
    }
    unset($pot);

    if ($custom_updated) {
        aitrongcay_store_custom_pots($garden_key, $user_id, $custom_pots);
        return true;
    }

    $dataset = function_exists('aitrongcay_portal_dataset_for_garden') ? (array) aitrongcay_portal_dataset_for_garden($garden_key, get_user_by('id', $user_id) ?: null) : [];
    $dataset_pots = (array) ($dataset['pots'] ?? []);
    $known = false;
    foreach ($dataset_pots as $pot) {
        if ((string) ($pot['code'] ?? '') === $pot_code) {
            $known = true;
            break;
        }
    }

    if (! $known) {
        return false;
    }

    $overrides = aitrongcay_get_pot_name_overrides($garden_key, $user_id);
    $overrides[$pot_code] = $pot_name;
    aitrongcay_store_pot_name_overrides($garden_key, $user_id, $overrides);

    return true;
}

function aitrongcay_next_custom_pot_code(array $existing_pots): string
{
    $max = 0;
    foreach ($existing_pots as $pot) {
        $code = (string) ($pot['code'] ?? '');
        if (preg_match('/P-(\d+)/', $code, $matches)) {
            $max = max($max, (int) $matches[1]);
        }
    }

    return 'P-' . str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
}

function aitrongcay_build_custom_pot(string $plant_name, array $existing_pots): array
{
    $plant_name = trim($plant_name);
    $code = aitrongcay_next_custom_pot_code($existing_pots);
    $pot_number = max(1, count($existing_pots) + 1);
    $light_number = (($pot_number - 1) % 4) + 1;

    return [
        'code' => $code,
        'name' => $plant_name,
        'status' => 'Mới khởi tạo',
        'ph' => '--',
        'temperature' => '-- °C',
        'humidity' => '-- %',
        'light' => 'Đèn khay ' . $pot_number,
        'light_device' => 'light' . $light_number,
        'pump' => 'Bơm chung',
        'irrigation' => 'Sẽ gợi ý sau khi xác nhận giống cây',
        'video' => '',
        'image' => get_template_directory_uri() . '/assets/images/hero-greenhouse.svg',
        'ai_note' => 'Em vừa khởi tạo khay này từ cuộc trò chuyện AI. Bước tiếp theo là theo dõi ảnh, chỉ số và nhịp chăm thực tế.',
        'status_summary' => 'Khay mới đã được tạo cho cây ' . $plant_name . '. Hiện đang ở bước khởi tạo thông tin nền và chờ anh bổ sung dữ liệu thực tế.',
        'harvest_eta' => 'Đang chờ AI gợi ý lịch chăm phù hợp',
        'trays' => [$plant_name],
        'is_custom' => true,
        'created_via' => 'ai_onboarding',
        'created_at' => gmdate('c'),
    ];
}

function aitrongcay_create_custom_pot_for_user(WP_User $user, string $garden_key, string $plant_name): array
{
    $owner = aitrongcay_get_garden_owner_user($garden_key);
    $target_user = $owner instanceof WP_User ? $owner : $user;
    $target_user_id = (int) ($target_user->ID ?? 0);
    if ($target_user_id <= 0) {
        return ['error' => 'Không xác định được chủ khu vườn để tạo khay.'];
    }

    $existing_pots = array_merge(
        aitrongcay_portal_dataset_for_garden($garden_key, $target_user)['pots'] ?? [],
        aitrongcay_get_custom_pots($garden_key, $target_user_id)
    );

    $new_pot = aitrongcay_build_custom_pot($plant_name, $existing_pots);
    $custom_pots = aitrongcay_get_custom_pots($garden_key, $target_user_id);
    $custom_pots[] = $new_pot;
    aitrongcay_store_custom_pots($garden_key, $target_user_id, $custom_pots);

    if (function_exists('aitrongcay_upsert_garden_record')) {
        $default_name = function_exists('aitrongcay_build_default_garden_name') ? aitrongcay_build_default_garden_name($garden_key, $target_user) : 'Khu vườn của bạn';
        aitrongcay_upsert_garden_record($garden_key, $target_user_id, [
            'garden_name' => $default_name,
            'garden_code' => strtoupper(substr(md5($garden_key), 0, 6)),
            'summary' => 'Khu vườn này đang bắt đầu có dữ liệu thật theo các khay đã được tạo.',
            'status_line' => count($custom_pots) . ' khay • đang theo dõi',
        ]);
    }

    return [
        'pot' => $new_pot,
        'garden_key' => $garden_key,
        'owner_user_id' => $target_user_id,
    ];
}

function aitrongcay_garden_assistant_build_reply(string $message, WP_User $user, string $garden_key = ''): array
{
    $normalized = strtolower(trim($message));
    $garden_ai = aitrongcay_portal_garden_ai($garden_key, $user);
    $pots = function_exists('aitrongcay_portal_pots') ? aitrongcay_portal_pots($garden_key, $user) : [];
    $pot_names = array_values(array_filter(array_map(static fn(array $pot): string => trim((string) ($pot['name'] ?? $pot['code'] ?? '')), $pots)));
    $sample_pot = $pot_names[0] ?? 'khay đang theo dõi';
    $reply = (string) ($garden_ai['summary'] ?? 'Em đã nhận câu hỏi của anh/chị. Hiện em đang ưu tiên đọc dữ liệu đúng theo khu vườn đang mở và gợi ý những bước an toàn, dễ làm trước.');

    if ($normalized !== '') {
        if (str_contains($normalized, 'ph')) {
            $reply = 'Nếu anh/chị đang hỏi về pH, em khuyên xem trước khay có dấu hiệu bất thường rõ nhất rồi điều chỉnh từng bước nhỏ. Nếu vườn này chưa có pH thật cho từng khay, mình nên cập nhật dần để em bám sát hơn.';
        } elseif (str_contains($normalized, 'độ ẩm') || str_contains($normalized, 'am')) {
            $reply = 'Với độ ẩm giảm nhẹ, mình nên xem lại khay nào có dấu hiệu khô trước rồi mới tăng tưới. Ưu tiên giữ ổn định thay vì thay đổi quá mạnh trong một lần.';
        } elseif (str_contains($normalized, 'đèn') || str_contains($normalized, 'den')) {
            $reply = 'Nếu cần tối ưu đèn, em khuyên tăng nhẹ theo nhịp 20–30 phút rồi theo dõi phản ứng của ' . $sample_pot . ' trong ngày kế tiếp. Ánh sáng đều thường an toàn hơn tăng mạnh một lần.';
        } elseif (str_contains($normalized, 'chợ quê') || str_contains($normalized, 'cho que')) {
            $reply = 'Nếu anh/chị muốn đăng Chợ quê, em khuyên chọn ảnh bìa rõ, tiêu đề ngắn và đưa ý chính lên đầu mô tả. Như vậy bài sẽ gọn và dễ được bấm xem hơn.';
        }
    }

    $session_label = 'garden-assistant-user-' . max(1, (int) $user->ID);

    return [
        'reply' => $reply,
        'sessionLabel' => $session_label,
        'mode' => 'adapter-ready',
        'agentStatus' => 'Chưa nối OpenClaw gateway thật — adapter đã sẵn sàng để cắm session riêng.',
    ];
}

function aitrongcay_garden_assistant_chat_ajax(): void
{
    aitrongcay_require_portal_nonce();
    if (! is_user_logged_in()) {
        wp_send_json_error(['message' => 'Anh/chị cần đăng nhập để chat với Trợ lý AI.'], 403);
    }

    $message = sanitize_textarea_field((string) wp_unslash($_POST['message'] ?? ''));
    if ($message === '') {
        wp_send_json_error(['message' => 'Anh/chị vui lòng nhập nội dung cần hỏi.'], 400);
    }

    $user = wp_get_current_user();
    $garden_key = sanitize_text_field((string) wp_unslash($_POST['garden_key'] ?? aitrongcay_resolve_active_garden_key($user)));
    if ($garden_key === '' || ! aitrongcay_user_can_view_garden($garden_key, (int) $user->ID)) {
        $garden_key = aitrongcay_resolve_active_garden_key($user);
    }
    aitrongcay_remember_selected_garden_key((int) $user->ID, $garden_key);

    $history_bucket = get_user_meta($user->ID, '_aitrongcay_garden_assistant_history_by_garden', true);
    if (! is_array($history_bucket)) {
        $history_bucket = [];
    }
    $history = $history_bucket[$garden_key] ?? [];
    if (! is_array($history)) {
        $history = [];
    }

    $history[] = [
        'role' => 'user',
        'text' => $message,
        'time' => gmdate('c'),
    ];

    $assistant = aitrongcay_garden_assistant_build_reply($message, $user, $garden_key);
    $history[] = [
        'role' => 'assistant',
        'text' => $assistant['reply'],
        'time' => gmdate('c'),
    ];

    $history = array_slice($history, -20);
    $history_bucket[$garden_key] = $history;
    update_user_meta($user->ID, '_aitrongcay_garden_assistant_history_by_garden', $history_bucket);

    wp_send_json_success([
        'messages' => $history,
        'reply' => $assistant['reply'],
        'sessionLabel' => $assistant['sessionLabel'],
        'mode' => $assistant['mode'],
        'agentStatus' => $assistant['agentStatus'],
    ]);
}
add_action('wp_ajax_aitrongcay_garden_assistant_chat', 'aitrongcay_garden_assistant_chat_ajax');

function aitrongcay_create_first_pot_ajax(): void
{
    aitrongcay_require_portal_nonce();
    if (! is_user_logged_in()) {
        wp_send_json_error(['message' => 'Anh/chị cần đăng nhập để tạo khay cây.'], 403);
    }

    $user = wp_get_current_user();
    $plant_name = sanitize_text_field((string) wp_unslash($_POST['plant_name'] ?? ''));
    if ($plant_name === '') {
        wp_send_json_error(['message' => 'Anh/chị cho em biết mình muốn trồng cây gì nhé.'], 400);
    }

    $garden_key = sanitize_text_field((string) wp_unslash($_POST['garden_key'] ?? aitrongcay_resolve_active_garden_key($user)));
    if ($garden_key === '' || ! aitrongcay_user_can_view_garden($garden_key, (int) $user->ID)) {
        $garden_key = aitrongcay_resolve_active_garden_key($user);
    }

    $created = aitrongcay_create_custom_pot_for_user($user, $garden_key, $plant_name);
    if (! empty($created['error'])) {
        wp_send_json_error(['message' => (string) $created['error']], 500);
    }

    $pot = (array) ($created['pot'] ?? []);
    $pot_name = (string) ($pot['name'] ?? $plant_name);
    wp_send_json_success([
        'message' => 'Em đã tạo khay đầu tiên cho ' . $pot_name . '. Mình quay về dashboard để theo dõi luôn nhé.',
        'pot' => $pot,
        'redirect' => add_query_arg('garden', rawurlencode($garden_key), home_url('/portal/dashboard/')),
        'garden_key' => $garden_key,
    ]);
}
add_action('wp_ajax_aitrongcay_create_first_pot', 'aitrongcay_create_first_pot_ajax');

function aitrongcay_rename_pot_ajax(): void
{
    aitrongcay_require_portal_nonce();
    if (! is_user_logged_in()) {
        wp_send_json_error(['message' => 'Anh/chị cần đăng nhập để đổi tên khay.'], 403);
    }

    $user = wp_get_current_user();
    $garden_key = sanitize_text_field((string) wp_unslash($_POST['garden_key'] ?? aitrongcay_resolve_active_garden_key($user)));
    if ($garden_key === '' || ! aitrongcay_user_can_control_garden($garden_key, (int) $user->ID)) {
        wp_send_json_error(['message' => 'Anh/chị chưa có quyền đổi tên khay này.'], 403);
    }

    $owner = aitrongcay_get_garden_owner_user($garden_key);
    $target_user_id = (int) (($owner instanceof WP_User ? $owner->ID : $user->ID) ?: 0);
    if ($target_user_id <= 0) {
        $target_user_id = (int) $user->ID;
    }
    $pot_code = sanitize_text_field((string) wp_unslash($_POST['pot_code'] ?? ''));
    $pot_name = sanitize_text_field((string) wp_unslash($_POST['pot_name'] ?? ''));
    $pot_name = trim(preg_replace('/\s+/u', ' ', $pot_name));

    if ($pot_code === '' || $pot_name === '') {
        wp_send_json_error(['message' => 'Tên khay không được để trống.'], 400);
    }

    if (function_exists('mb_strlen') && mb_strlen($pot_name) > 120) {
        $pot_name = mb_substr($pot_name, 0, 120);
    }

    if (! aitrongcay_update_pot_name_for_garden($garden_key, $target_user_id, $pot_code, $pot_name)) {
        wp_send_json_error(['message' => 'Khay này hiện chưa hỗ trợ đổi tên trực tiếp.'], 400);
    }

    wp_send_json_success([
        'pot_code' => $pot_code,
        'pot_name' => $pot_name,
        'message' => 'Đã lưu tên khay mới.',
    ]);
}
add_action('wp_ajax_aitrongcay_rename_pot', 'aitrongcay_rename_pot_ajax');

function aitrongcay_save_pot_note_ajax(): void
{
    aitrongcay_require_portal_nonce();
    if (! is_user_logged_in()) {
        wp_send_json_error(['message' => 'Anh/chị cần đăng nhập để lưu ghi chú.'], 403);
    }

    $user = wp_get_current_user();
    $garden_key = sanitize_text_field((string) wp_unslash($_POST['garden_key'] ?? aitrongcay_resolve_active_garden_key($user)));
    if ($garden_key === '' || ! aitrongcay_user_can_control_garden($garden_key, (int) $user->ID)) {
        wp_send_json_error(['message' => 'Anh/chị chưa có quyền cập nhật nhật ký của khu vườn này.'], 403);
    }

    $pot_code = sanitize_text_field((string) wp_unslash($_POST['pot_code'] ?? ''));
    $note_text = trim((string) wp_unslash($_POST['note_text'] ?? ''));
    $note_text = preg_replace('/\r\n?|\n/u', "\n", $note_text);
    $note_text = trim((string) preg_replace('/[\t ]+/u', ' ', $note_text));

    if ($pot_code === '') {
        wp_send_json_error(['message' => 'Thiếu mã khay để lưu ghi chú.'], 400);
    }

    if (function_exists('mb_strlen') && mb_strlen($note_text) > 2000) {
        $note_text = mb_substr($note_text, 0, 2000);
    }

    if (! aitrongcay_save_garden_pot_note($garden_key, $pot_code, $note_text, (int) $user->ID)) {
        wp_send_json_error(['message' => 'Chưa lưu được ghi chú cho khay này.'], 500);
    }

    wp_send_json_success([
        'garden_key' => $garden_key,
        'pot_code' => $pot_code,
        'note_text' => $note_text,
        'updated_at' => current_time('mysql'),
        'message' => $note_text === '' ? 'Đã xóa ghi chú trống.' : 'Đã lưu ghi chú canh tác.',
    ]);
}
add_action('wp_ajax_aitrongcay_save_pot_note', 'aitrongcay_save_pot_note_ajax');

function aitrongcay_rename_garden_ajax(): void
{
    aitrongcay_require_portal_nonce();
    if (! is_user_logged_in()) {
        wp_send_json_error(['message' => 'Anh/chị cần đăng nhập để đổi tên vườn.'], 403);
    }

    $user = wp_get_current_user();
    $garden_key = sanitize_text_field((string) wp_unslash($_POST['garden_key'] ?? aitrongcay_resolve_active_garden_key($user)));
    if ($garden_key === '' || ! aitrongcay_user_can_control_garden($garden_key, (int) $user->ID)) {
        wp_send_json_error(['message' => 'Anh/chị chưa có quyền đổi tên khu vườn này.'], 403);
    }

    $owner = aitrongcay_get_garden_owner_user($garden_key);
    $target_user_id = (int) (($owner instanceof WP_User ? $owner->ID : $user->ID) ?: 0);
    if ($target_user_id <= 0) {
        $target_user_id = (int) $user->ID;
    }

    $garden_name = sanitize_text_field((string) wp_unslash($_POST['garden_name'] ?? ''));
    $garden_name = trim((string) preg_replace('/\s+/u', ' ', $garden_name));
    if ($garden_name === '') {
        $garden_name = aitrongcay_build_default_garden_name($garden_key, $user);
    }
    if ($garden_name === '') {
        $garden_name = 'Khu vườn của bạn';
    }

    if (function_exists('mb_strlen') && mb_strlen($garden_name) > 160) {
        $garden_name = mb_substr($garden_name, 0, 160);
    }

    aitrongcay_store_garden_name_override($garden_key, $target_user_id, $garden_name);
    if ((int) $user->ID !== $target_user_id) {
        aitrongcay_store_garden_name_override($garden_key, (int) $user->ID, $garden_name);
    }
    aitrongcay_remember_selected_garden_key((int) $user->ID, $garden_key);

    if (function_exists('aitrongcay_upsert_garden_record')) {
        $existing = aitrongcay_get_garden_record($garden_key) ?: [];
        $sync_owner_id = $target_user_id > 0 ? $target_user_id : (int) $user->ID;
        aitrongcay_upsert_garden_record($garden_key, $sync_owner_id, [
            'garden_name' => $garden_name,
            'garden_code' => (string) ($existing['garden_code'] ?? strtoupper(substr(md5($garden_key), 0, 6))),
            'summary' => (string) ($existing['summary'] ?? ''),
            'status_line' => (string) ($existing['status_line'] ?? ''),
        ]);
    }

    wp_send_json_success([
        'garden_key' => $garden_key,
        'garden_name' => $garden_name,
        'message' => 'Đã lưu tên khu vườn mới.',
    ]);
}
add_action('wp_ajax_aitrongcay_rename_garden', 'aitrongcay_rename_garden_ajax');

function aitrongcay_delete_photo_attachment_ajax(): void
{
    aitrongcay_require_portal_nonce();
    $attachment_id = absint($_POST['attachment_id'] ?? 0);
    $attachment = get_post($attachment_id);
    if (! $attachment || $attachment->post_type !== 'attachment') {
        wp_send_json_error(['message' => 'Không tìm thấy ảnh cần xóa.'], 404);
    }
    $owner_id = (int) get_post_meta($attachment_id, '_aitrongcay_photo_owner', true);
    if ($owner_id !== get_current_user_id()) {
        wp_send_json_error(['message' => 'Không có quyền xóa ảnh này.'], 403);
    }
    wp_delete_attachment($attachment_id, true);
    wp_send_json_success(['id' => $attachment_id]);
}
add_action('wp_ajax_aitrongcay_delete_photo_attachment', 'aitrongcay_delete_photo_attachment_ajax');

function aitrongcay_rename_photo_attachment_ajax(): void
{
    aitrongcay_require_portal_nonce();
    $attachment_id = absint($_POST['attachment_id'] ?? 0);
    $title = sanitize_text_field((string) wp_unslash($_POST['title'] ?? ''));
    $attachment = get_post($attachment_id);
    if (! $attachment || $attachment->post_type !== 'attachment') {
        wp_send_json_error(['message' => 'Không tìm thấy ảnh cần đổi tên.'], 404);
    }
    $owner_id = (int) get_post_meta($attachment_id, '_aitrongcay_photo_owner', true);
    if ($owner_id !== get_current_user_id()) {
        wp_send_json_error(['message' => 'Không có quyền đổi tên ảnh này.'], 403);
    }
    if ($title === '') {
        wp_send_json_error(['message' => 'Tên ảnh không được để trống.'], 400);
    }
    wp_update_post(['ID' => $attachment_id, 'post_title' => $title]);
    wp_send_json_success(['id' => $attachment_id, 'title' => get_the_title($attachment_id)]);
}
add_action('wp_ajax_aitrongcay_rename_photo_attachment', 'aitrongcay_rename_photo_attachment_ajax');

function aitrongcay_upload_photo_attachment_ajax(): void
{
    aitrongcay_require_portal_nonce();
    if (empty($_FILES['photo'])) {
        wp_send_json_error(['message' => 'Chưa có file ảnh để upload.'], 400);
    }
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $attachment_id = media_handle_upload('photo', 0);
    if (is_wp_error($attachment_id)) {
        wp_send_json_error(['message' => $attachment_id->get_error_message()], 500);
    }
    update_post_meta($attachment_id, '_aitrongcay_photo_owner', get_current_user_id());
    update_post_meta($attachment_id, '_aitrongcay_photo_garden_key', aitrongcay_resolve_active_garden_key());
    update_post_meta($attachment_id, '_aitrongcay_photo_source', 'livecam');
    wp_update_post(['ID' => $attachment_id, 'post_title' => 'Ảnh vườn ' . gmdate('H:i')]);
    wp_send_json_success([
        'id' => $attachment_id,
        'url' => set_url_scheme((string) (wp_get_attachment_image_url($attachment_id, 'medium_large') ?: wp_get_attachment_url($attachment_id)), 'https'),
        'title' => get_the_title($attachment_id),
        'download' => set_url_scheme((string) wp_get_attachment_url($attachment_id), 'https'),
    ]);
}
add_action('wp_ajax_aitrongcay_upload_photo_attachment', 'aitrongcay_upload_photo_attachment_ajax');

function aitrongcay_toggle_market_like_ajax(): void
{
    aitrongcay_require_portal_nonce();
    $post_id = absint($_POST['post_id'] ?? 0);
    $post = get_post($post_id);
    if (! $post || $post->post_type !== 'aitr_market_post') {
        wp_send_json_error(['message' => 'Không tìm thấy tin đăng.'], 404);
    }
    $user_id = get_current_user_id();
    $likes = array_map('intval', (array) get_post_meta($post_id, '_aitrongcay_market_likes', true));
    if (in_array($user_id, $likes, true)) {
        $likes = array_values(array_diff($likes, [$user_id]));
        $liked = false;
    } else {
        $likes[] = $user_id;
        $likes = array_values(array_unique($likes));
        $liked = true;
    }
    update_post_meta($post_id, '_aitrongcay_market_likes', $likes);
    wp_send_json_success(['liked' => $liked, 'count' => count($likes)]);
}
add_action('wp_ajax_aitrongcay_toggle_market_like', 'aitrongcay_toggle_market_like_ajax');

function aitrongcay_send_friend_request_ajax(): void
{
    aitrongcay_require_portal_nonce();
    global $wpdb;
    $target = sanitize_text_field((string) ($_POST['target'] ?? ''));
    $current_user_id = get_current_user_id();
    $target_user = is_email($target) ? get_user_by('email', $target) : get_user_by('login', $target);
    if (! $target_user instanceof WP_User) {
        wp_send_json_error(['message' => 'Không tìm thấy người dùng.'], 404);
    }
    if ((int) $target_user->ID === $current_user_id) {
        wp_send_json_error(['message' => 'Không thể tự kết bạn với chính mình.'], 400);
    }
    $table = aitrongcay_friendships_table();
    $pair_key = aitrongcay_friend_pair_key($current_user_id, (int) $target_user->ID);
    $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE unique_pair_key = %s LIMIT 1", $pair_key));
    if ($exists) {
        wp_send_json_error(['message' => 'Hai người đã có hoặc đang chờ lời mời kết bạn.'], 400);
    }
    $wpdb->insert($table, [
        'requester_user_id' => $current_user_id,
        'addressee_user_id' => (int) $target_user->ID,
        'unique_pair_key' => $pair_key,
        'status' => 'pending',
        'created_at' => current_time('mysql', true),
        'responded_at' => null,
    ]);
    wp_send_json_success(['message' => 'Đã gửi lời mời kết bạn.']);
}
add_action('wp_ajax_aitrongcay_send_friend_request', 'aitrongcay_send_friend_request_ajax');

function aitrongcay_accept_friend_request_ajax(): void
{
    aitrongcay_require_portal_nonce();
    global $wpdb;
    $id = isset($_POST['friendship_id']) ? (int) $_POST['friendship_id'] : 0;
    $table = aitrongcay_friendships_table();
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d LIMIT 1", $id), ARRAY_A);
    if (! is_array($row) || (int) $row['addressee_user_id'] !== get_current_user_id() || ($row['status'] ?? '') !== 'pending') {
        wp_send_json_error(['message' => 'Không tìm thấy lời mời hợp lệ.'], 404);
    }
    $wpdb->update($table, [
        'status' => 'accepted',
        'responded_at' => current_time('mysql', true),
    ], ['id' => $id]);
    wp_send_json_success(['message' => 'Đã chấp nhận kết bạn.']);
}
add_action('wp_ajax_aitrongcay_accept_friend_request', 'aitrongcay_accept_friend_request_ajax');

function aitrongcay_reject_friend_request_ajax(): void
{
    aitrongcay_require_portal_nonce();
    global $wpdb;
    $id = isset($_POST['friendship_id']) ? (int) $_POST['friendship_id'] : 0;
    $table = aitrongcay_friendships_table();
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d LIMIT 1", $id), ARRAY_A);
    if (! is_array($row) || (int) $row['addressee_user_id'] !== get_current_user_id() || ($row['status'] ?? '') !== 'pending') {
        wp_send_json_error(['message' => 'Không tìm thấy lời mời hợp lệ.'], 404);
    }
    $wpdb->update($table, [
        'status' => 'declined',
        'responded_at' => current_time('mysql', true),
    ], ['id' => $id]);
    wp_send_json_success(['message' => 'Đã từ chối lời mời kết bạn.']);
}
add_action('wp_ajax_aitrongcay_reject_friend_request', 'aitrongcay_reject_friend_request_ajax');

function aitrongcay_invite_garden_member_ajax(): void
{
    aitrongcay_require_portal_nonce();
    global $wpdb;
    $garden_key = sanitize_text_field((string) ($_POST['garden_key'] ?? aitrongcay_current_garden_key()));
    if (aitrongcay_user_garden_role($garden_key, get_current_user_id()) !== 'owner') {
        wp_send_json_error(['message' => 'Chỉ chủ vườn mới được mời người khác.'], 403);
    }
    $user_id = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
    $role = sanitize_key((string) ($_POST['role'] ?? 'viewer'));
    if (! in_array($role, ['co_owner', 'viewer'], true)) {
        wp_send_json_error(['message' => 'Vai trò không hợp lệ.'], 400);
    }
    $table = aitrongcay_garden_members_table();
    $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE garden_key = %s AND user_id = %d LIMIT 1", $garden_key, $user_id));
    if ($exists) {
        wp_send_json_error(['message' => 'Người này đã có hoặc đang chờ quyền trong khu vườn.'], 400);
    }
    $wpdb->insert($table, [
        'garden_key' => $garden_key,
        'user_id' => $user_id,
        'role' => $role,
        'status' => 'invited',
        'invited_by_user_id' => get_current_user_id(),
        'created_at' => current_time('mysql', true),
        'updated_at' => current_time('mysql', true),
    ]);
    wp_send_json_success(['message' => 'Đã gửi lời mời vào khu vườn.']);
}
add_action('wp_ajax_aitrongcay_invite_garden_member', 'aitrongcay_invite_garden_member_ajax');

function aitrongcay_accept_garden_invite_ajax(): void
{
    aitrongcay_require_portal_nonce();
    global $wpdb;
    $id = isset($_POST['membership_id']) ? (int) $_POST['membership_id'] : 0;
    $table = aitrongcay_garden_members_table();
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d LIMIT 1", $id), ARRAY_A);
    if (! is_array($row) || (int) $row['user_id'] !== get_current_user_id() || ($row['status'] ?? '') !== 'invited') {
        wp_send_json_error(['message' => 'Không tìm thấy lời mời hợp lệ.'], 404);
    }
    $wpdb->update($table, [
        'status' => 'active',
        'updated_at' => current_time('mysql', true),
    ], ['id' => $id]);
    aitrongcay_remember_selected_garden_key(get_current_user_id(), (string) ($row['garden_key'] ?? ''));
    wp_send_json_success(['message' => 'Đã tham gia khu vườn.', 'garden_key' => $row['garden_key'] ?? '']);
}
add_action('wp_ajax_aitrongcay_accept_garden_invite', 'aitrongcay_accept_garden_invite_ajax');

function aitrongcay_decline_garden_invite_ajax(): void
{
    aitrongcay_require_portal_nonce();
    global $wpdb;
    $id = isset($_POST['membership_id']) ? (int) $_POST['membership_id'] : 0;
    $table = aitrongcay_garden_members_table();
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d LIMIT 1", $id), ARRAY_A);
    if (! is_array($row) || (int) $row['user_id'] !== get_current_user_id() || ($row['status'] ?? '') !== 'invited') {
        wp_send_json_error(['message' => 'Không tìm thấy lời mời hợp lệ.'], 404);
    }
    $wpdb->update($table, [
        'status' => 'declined',
        'updated_at' => current_time('mysql', true),
    ], ['id' => $id]);
    wp_send_json_success(['message' => 'Đã từ chối lời mời vào khu vườn.']);
}
add_action('wp_ajax_aitrongcay_decline_garden_invite', 'aitrongcay_decline_garden_invite_ajax');

function aitrongcay_update_garden_member_role_ajax(): void
{
    aitrongcay_require_portal_nonce();
    global $wpdb;
    $membership_id = isset($_POST['membership_id']) ? (int) $_POST['membership_id'] : 0;
    $role = sanitize_key((string) ($_POST['role'] ?? 'viewer'));
    if (! in_array($role, ['co_owner', 'viewer'], true)) {
        wp_send_json_error(['message' => 'Vai trò không hợp lệ.'], 400);
    }
    $table = aitrongcay_garden_members_table();
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d LIMIT 1", $membership_id), ARRAY_A);
    if (! is_array($row)) {
        wp_send_json_error(['message' => 'Không tìm thấy thành viên.'], 404);
    }
    if (aitrongcay_user_garden_role((string) $row['garden_key'], get_current_user_id()) !== 'owner') {
        wp_send_json_error(['message' => 'Chỉ chủ vườn mới được đổi quyền.'], 403);
    }
    if (($row['role'] ?? '') === 'owner') {
        wp_send_json_error(['message' => 'Không thể đổi quyền của chủ vườn.'], 400);
    }
    $wpdb->update($table, [
        'role' => $role,
        'updated_at' => current_time('mysql', true),
    ], ['id' => $membership_id]);
    wp_send_json_success(['message' => 'Đã cập nhật quyền thành viên.']);
}
add_action('wp_ajax_aitrongcay_update_garden_member_role', 'aitrongcay_update_garden_member_role_ajax');

function aitrongcay_remove_garden_member_ajax(): void
{
    aitrongcay_require_portal_nonce();
    global $wpdb;
    $membership_id = isset($_POST['membership_id']) ? (int) $_POST['membership_id'] : 0;
    $table = aitrongcay_garden_members_table();
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d LIMIT 1", $membership_id), ARRAY_A);
    if (! is_array($row)) {
        wp_send_json_error(['message' => 'Không tìm thấy thành viên.'], 404);
    }
    if (aitrongcay_user_garden_role((string) $row['garden_key'], get_current_user_id()) !== 'owner') {
        wp_send_json_error(['message' => 'Chỉ chủ vườn mới được gỡ thành viên.'], 403);
    }
    if (($row['role'] ?? '') === 'owner') {
        wp_send_json_error(['message' => 'Không thể gỡ chủ vườn.'], 400);
    }
    $wpdb->delete($table, ['id' => $membership_id], ['%d']);
    wp_send_json_success(['message' => ($row['status'] ?? '') === 'invited' ? 'Đã hủy lời mời.' : 'Đã gỡ thành viên khỏi khu vườn.']);
}
add_action('wp_ajax_aitrongcay_remove_garden_member', 'aitrongcay_remove_garden_member_ajax');
