<?php

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$page = aitrongcay_current_virtual_page();
$slug = $page['slug'] ?? 'portal';
$portal_nav = aitrongcay_portal_nav_items();
$current_user = wp_get_current_user();
$is_logged_in = is_user_logged_in();
$share_token = isset($_GET['share_token']) ? sanitize_text_field((string) wp_unslash($_GET['share_token'])) : '';

$garden_key = $is_logged_in ? aitrongcay_resolve_active_garden_key($current_user instanceof WP_User ? $current_user : null) : '';
$active_profile = $is_logged_in ? aitrongcay_portal_profile_for_garden_context($garden_key, $current_user instanceof WP_User ? $current_user : null) : null;
$garden_ai = aitrongcay_portal_garden_ai($garden_key, $current_user instanceof WP_User ? $current_user : null);
$pots = aitrongcay_portal_pots($garden_key, $current_user instanceof WP_User ? $current_user : null);
$tool_shelf = aitrongcay_portal_tool_shelf($garden_key, $current_user instanceof WP_User ? $current_user : null);
$pot_notes = $is_logged_in && function_exists('aitrongcay_get_garden_pot_notes') ? aitrongcay_get_garden_pot_notes($garden_key) : [];

$has_valid_public_livecam_token = false;
if ($slug === 'portal/webcam' && $share_token !== '') {
    $matched_users = get_users([
        'meta_key' => '_aitrongcay_livecam_public_token',
        'meta_value' => $share_token,
        'number' => 1,
        'count_total' => false,
    ]);
    if ($matched_users) {
        $current_user = $matched_users[0];
        $garden_key = aitrongcay_current_garden_key($current_user instanceof WP_User ? $current_user : null);
        $active_profile = aitrongcay_portal_profile_for_garden_context($garden_key, $current_user instanceof WP_User ? $current_user : null);
        $garden_ai = aitrongcay_portal_garden_ai($garden_key, $current_user instanceof WP_User ? $current_user : null);
        $pots = aitrongcay_portal_pots($garden_key, $current_user instanceof WP_User ? $current_user : null);
        $tool_shelf = aitrongcay_portal_tool_shelf($garden_key, $current_user instanceof WP_User ? $current_user : null);
        $pot_notes = function_exists('aitrongcay_get_garden_pot_notes') ? aitrongcay_get_garden_pot_notes($garden_key) : [];
        $is_logged_in = true;
        $has_valid_public_livecam_token = true;
    }
}

if ($slug !== 'portal' && ! $is_logged_in) {
    wp_safe_redirect(home_url('/dang-nhap/?auth_status=login-required'));
    exit;
}

if ($slug === 'portal/webcam' && $share_token !== '' && ! $has_valid_public_livecam_token) {
    wp_safe_redirect(home_url('/dang-nhap/?auth_status=login-required'));
    exit;
}

$current_role = $is_logged_in ? (aitrongcay_user_garden_role($garden_key, (int) $current_user->ID) ?? 'owner') : null;
$current_role_label = $is_logged_in ? aitrongcay_get_role_label((string) $current_role) : '';
$current_role_badge_class = $is_logged_in ? aitrongcay_get_role_badge_class((string) $current_role) : 'is-sand';
$can_manage_members = $current_role === 'owner';
$can_control_garden = $is_logged_in ? aitrongcay_user_can_control_garden($garden_key, (int) $current_user->ID) : false;
$friends = $is_logged_in ? aitrongcay_get_user_friends((int) $current_user->ID) : [];
$friend_invites = $is_logged_in ? aitrongcay_get_friend_invites_received((int) $current_user->ID) : [];
$friend_owner_search = $is_logged_in && $slug === 'portal/ban-be' ? sanitize_text_field((string) wp_unslash($_GET['owner_search'] ?? '')) : '';
$active_garden_owners = $is_logged_in && $slug === 'portal/ban-be' && $friend_owner_search !== '' ? aitrongcay_get_active_garden_owners((int) $current_user->ID, $friend_owner_search) : [];
$garden_invites = $is_logged_in ? aitrongcay_get_garden_invites_received((int) $current_user->ID) : [];
$garden_members = $is_logged_in ? aitrongcay_get_garden_members($garden_key) : [];
$garden_owner_user = $is_logged_in ? aitrongcay_get_garden_owner_user($garden_key) : null;
$viewable_gardens = $is_logged_in ? aitrongcay_get_viewable_gardens_for_user($current_user instanceof WP_User ? $current_user : null) : [];
$garden_display_name = $is_logged_in
    ? trim((string) (function_exists('aitrongcay_get_garden_display_name')
        ? aitrongcay_get_garden_display_name($garden_key, $current_user instanceof WP_User ? $current_user : null)
        : ((string) ($active_profile['garden_name'] ?? ''))))
    : '';
if ($garden_display_name === '' && is_array($active_profile)) {
    $garden_display_name = trim((string) ($active_profile['garden_name'] ?? ''));
}
$has_real_pots = ! empty($pots);
$active_member_count = count(array_filter($garden_members, static fn(array $member): bool => ($member['status'] ?? '') === 'active'));
$pending_member_count = count(array_filter($garden_members, static fn(array $member): bool => ($member['status'] ?? '') === 'invited'));
$owner_member = null;
$co_owner_members = [];
$viewer_members = [];
$pending_members = [];
foreach ($garden_members as $member) {
    $member_role = (string) ($member['role'] ?? 'viewer');
    $member_status = (string) ($member['status'] ?? '');
    if ($member_status === 'invited') {
        $pending_members[] = $member;
        continue;
    }
    if ($member_role === 'owner' && $owner_member === null) {
        $owner_member = $member;
        continue;
    }
    if ($member_role === 'co_owner') {
        $co_owner_members[] = $member;
        continue;
    }
    $viewer_members[] = $member;
}

$render_portal_nav = static function () use ($portal_nav, $slug, $garden_key): void {
    foreach ($portal_nav as $item) {
        $is_active = $slug === $item['slug'] || ($item['slug'] === 'portal/kho-nong-cu' && $slug === 'portal/dashboard');
        $base_href = $item['slug'] === 'portal/kho-nong-cu'
            ? home_url('/portal/dashboard/#tool-shelf')
            : home_url('/' . $item['slug'] . '/');
        $href = $garden_key !== '' ? add_query_arg('garden', rawurlencode($garden_key), $base_href) : $base_href;
        printf(
            '<a%s href="%s">%s</a>',
            $is_active ? ' class="active"' : '',
            esc_url($href),
            esc_html($item['label'])
        );
    }
};

if ($slug === 'portal') : ?>
<section class="section-hero">
    <div class="container grid-2">
        <div>
            <span class="eyebrow">Khu vườn của bạn</span>
            <h1>Sở hữu từng khay cây. Theo dõi cả khu vườn.</h1>
            <p class="lead">Mỗi khay có camera riêng, cảm biến riêng và lịch sử riêng. Mỗi khu vườn có một AI Agent đồng hành và theo dõi mọi việc cùng gia đình.</p>
            <div class="inline-list" style="margin-top:20px">
                <a class="btn btn-primary" href="<?php echo esc_url($garden_key !== '' ? add_query_arg('garden', rawurlencode($garden_key), home_url('/portal/dashboard/')) : home_url('/portal/dashboard/')); ?>">Mở khu vườn của tôi</a>
                <a class="btn btn-secondary" href="<?php echo esc_url(home_url('/dang-nhap/')); ?>">Đăng nhập</a>
            </div>
        </div>
        <div class="glass-card editorial-card">
            <div class="kpi-row">
                <div class="metric"><span class="subtle">Số khay</span><strong><?php echo esc_html((string) count($pots)); ?></strong></div>
                <div class="metric"><span class="subtle">Mỗi khay</span><strong>6 khay</strong></div>
                <div class="metric"><span class="subtle">AI Agent</span><strong>1 trợ lý riêng</strong></div>
            </div>
            <p style="margin-top:18px">Khu vườn giúp gia đình theo dõi, điều khiển và giám sát quá trình canh tác theo từng khay cây.</p>
        </div>
    </div>
</section>
<?php return; endif; ?>

<?php if (! $active_profile && $slug !== 'portal/webcam') : ?>
<section class="section-tight">
    <div class="container">
        <div class="notice error"><strong>Tài khoản này chưa được gán khu vườn nào.</strong><div style="margin-top:6px">Khi tài khoản được gắn đúng khu vườn hoặc quyền truy cập, dashboard sẽ hiện dữ liệu tương ứng.</div></div>
    </div>
</section>
<?php return; endif; ?>

<?php
if ($slug === 'portal/webcam' && $share_token !== '' && $has_valid_public_livecam_token && $active_profile) :
?>
<section class="section-hero public-livecam-shell">
    <div class="container" style="max-width:980px">
        <div class="glass-card editorial-card public-livecam-card">
            <span class="eyebrow">Livecam được chia sẻ</span>
            <h1><?php echo esc_html($active_profile['garden_code'] . ' • ' . $active_profile['garden_name']); ?></h1>
            <div class="small subtle" style="margin-top:8px" data-garden-display-name><?php echo esc_html($active_profile['garden_name']); ?></div>
            <p class="lead">Một link xem gọn, chỉ giữ đúng khung camera và nhịp hiện tại của khu vườn.</p>
            <div class="public-livecam-frame">
                <video data-livecam autoplay muted loop playsinline controls poster="<?php echo esc_url(get_template_directory_uri() . '/assets/images/hero-greenhouse.svg'); ?>" style="display:block;width:100%;height:auto;aspect-ratio:16/9;background:#0f172a"><source src="https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4" type="video/mp4">Trình duyệt này chưa phát được video demo.</video>
            </div>
            <div class="chips" style="margin-top:18px">
                <span class="chip"><?php echo esc_html($active_profile['status']); ?></span>
                <span class="chip">Chỉ xem livecam</span>
            </div>
        </div>
    </div>
</section>
<?php return; endif; ?>

<?php
$media_owner_id = $is_logged_in ? aitrongcay_resolve_garden_owner_id($garden_key, $current_user instanceof WP_User ? $current_user : null) : 0;

$library_query = new WP_Query([
    'post_type' => 'attachment',
    'post_status' => 'inherit',
    'posts_per_page' => 50,
    'orderby' => 'date',
    'order' => 'DESC',
    'meta_query' => [
        'relation' => 'AND',
        [
            'key' => '_aitrongcay_photo_source',
            'value' => 'livecam',
        ],
        [
            'key' => '_aitrongcay_photo_garden_key',
            'value' => $garden_key,
        ],
    ],
]);

$pot_photo_groups = [];
$pot_lookup = [];
foreach ($pots as $pot_item) {
    $pot_lookup[$pot_item['code']] = $pot_item;
    $pot_photo_groups[$pot_item['code']] = [];
}
if ($library_query->have_posts()) {
    foreach ($library_query->posts as $photo_post) {
        $pot_code = (string) get_post_meta($photo_post->ID, '_aitrongcay_pot_code', true);
        if ($pot_code === '' || ! isset($pot_photo_groups[$pot_code])) {
            $pot_code = 'UNGROUPED';
            if (! isset($pot_photo_groups[$pot_code])) {
                $pot_photo_groups[$pot_code] = [];
            }
        }
        $pot_photo_groups[$pot_code][] = $photo_post;
    }
}
?>

<div class="portal-shell">
    <div class="portal-layout">
        <aside class="sidebar">
            <div class="logo" style="margin-bottom:20px"><span class="logo-badge">🌿</span><span>Ai trồng cây</span></div>

            <div class="sidebar-group">
                <?php $render_portal_nav(); ?>
            </div>
            <div class="sidebar-group">
                <h4>Tài khoản</h4>
                <a href="<?php echo esc_url($garden_key !== '' ? add_query_arg('garden', rawurlencode($garden_key), home_url('/portal/dashboard/')) : home_url('/portal/dashboard/')); ?>"><?php echo esc_html($current_user->display_name ?: $current_user->user_login); ?></a>
                <a href="<?php echo esc_url(wp_logout_url(home_url('/dang-nhap/?auth_status=logged-out'))); ?>">Đăng xuất</a>
            </div>
            <div class="sidebar-group">
                <h4>Website</h4>
                <a href="<?php echo esc_url(home_url('/')); ?>">← Quay lại website</a>
            </div>
        </aside>
        <main class="portal-main">
            <?php if (isset($_GET['auth_status'])) : ?>
                <div style="margin-bottom:20px"><?php aitrongcay_render_auth_notice(); ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['photo_added'])) : ?>
                <div class="notice success" style="margin-bottom:20px"><strong>Ảnh mới đã vào kho ảnh.</strong><div style="margin-top:6px">Anh/chị có thể chọn ảnh này ngay để đăng Chợ quê.</div></div>
            <?php endif; ?>
            <?php if (isset($_GET['blynk_ctrl'])) : ?>
                <?php $blynk_ctrl_msg = sanitize_text_field((string) wp_unslash($_GET['blynk_ctrl'])); ?>
                <?php if (strpos($blynk_ctrl_msg, 'ok:') === 0) : ?>
                    <div class="notice success" style="margin-bottom:20px"><strong>Lệnh điều khiển đã được gửi thành công.</strong><div style="margin-top:6px">Thiết bị đang cập nhật trạng thái mới.</div></div>
                <?php else : ?>
                    <div class="notice error" style="margin-bottom:20px"><strong>Không gửi được lệnh điều khiển.</strong><div style="margin-top:6px">Vui lòng thử lại sau vài giây.</div></div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($slug === 'portal/dashboard') : ?>
                <div class="portal-hero"><div class="portal-garden-name-wrap"><?php if ($can_control_garden) : ?><div class="garden-inline-rename" data-garden-inline-name data-garden-name="<?php echo esc_attr($garden_display_name); ?>"><div class="garden-inline-row"><h2 class="garden-inline-name-text" data-garden-display-name><?php echo esc_html($garden_display_name); ?></h2><input type="text" class="garden-inline-input" value="<?php echo esc_attr($garden_display_name); ?>" data-garden-inline-input maxlength="160" aria-label="Tên khu vườn" hidden><button class="garden-inline-name-edit" type="button" data-garden-inline-edit aria-label="Đổi tên khu vườn">✏️</button><span class="garden-inline-save-status" data-garden-inline-status hidden>Nhấn Enter hoặc click ra ngoài để lưu</span></div></div><?php else : ?><h2 data-garden-display-name><?php echo esc_html($garden_display_name); ?></h2><?php endif; ?></div><p style="margin:14px 0 14px"><?php echo esc_html($garden_ai['summary']); ?></p><div class="cards-3"><?php foreach ($garden_ai['tips'] as $tip) : ?><div class="card soft-card" style="background:#ffffff;color:#0f172a;border:1px solid rgba(15,23,42,.12)"><p style="margin:0;color:#0f172a;font-weight:500"><?php echo esc_html($tip); ?></p></div><?php endforeach; ?></div></div>
                <div class="portal-cards">
                    <?php if ($current_role !== 'owner') : ?>
                        <section class="portal-card span-12"><div class="notice role-explainer role-explainer--<?php echo esc_attr($current_role); ?>" style="margin:0"><strong><?php echo esc_html($current_role === 'co_owner' ? 'Anh đang hỗ trợ khu vườn này với quyền đồng sở hữu.' : 'Anh đang mở khu vườn này ở chế độ chỉ xem.'); ?></strong><div style="margin-top:6px"><?php if ($garden_owner_user instanceof WP_User) : ?>Chủ vườn hiện tại là <?php echo esc_html($garden_owner_user->display_name ?: $garden_owner_user->user_login); ?>. <?php endif; ?><?php echo esc_html($current_role === 'co_owner' ? 'Anh vẫn điều khiển được đèn và bơm, nhưng chưa thể mời thêm người hay đổi quyền thành viên.' : 'Anh vẫn xem được dashboard, ảnh và trạng thái thiết bị, nhưng các nút điều khiển và quản trị chia sẻ sẽ bị khóa.'); ?></div></div></section>
                    <?php endif; ?>
                    <?php if ($has_real_pots) : ?>
                    <section class="portal-card span-12" id="blynk-live-card"><div class="cards-4"><div class="card soft-card"><div class="kicker">Nhiệt độ</div><p style="margin:4px 0 0;font-weight:700" data-blynk-temp>-- °C</p></div><div class="card soft-card"><div class="kicker">Độ ẩm không khí</div><p style="margin:4px 0 0;font-weight:700" data-blynk-hum>-- %</p></div><div class="card soft-card"><div class="kicker">Độ ẩm đất</div><p style="margin:4px 0 0;font-weight:700" data-blynk-soil>-- %</p></div><div class="card soft-card"><div class="kicker">Trạng thái</div><p style="margin:4px 0 0;font-weight:700" data-blynk-status>Đang chờ dữ liệu</p></div></div></section>
                    <section class="portal-card span-12">
    <div class="section-head" style="margin-bottom:18px">
        <span class="eyebrow">Các khay cây đang sở hữu</span>
        <div class="inline-list" style="margin-top:14px">
            <a class="btn btn-primary" href="<?php echo esc_url(add_query_arg(['compose' => '1', 'garden' => $garden_key], home_url('/cho-que/'))); ?>">Đăng tin lên Chợ quê</a>
            <button id="garden-view-mode-toggle" class="view-mode-switch is-private" type="button" data-state="private"><span class="view-mode-dot"></span><span class="view-mode-icon" aria-hidden="true">🔒</span><span class="view-mode-label">Chế độ view riêng tư</span></button>
            <a class="btn btn-secondary" href="<?php echo esc_url(home_url('/portal/nhat-ky-cham-soc/')); ?>">Kho ảnh</a>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:minmax(0,1fr);gap:16px">
        <?php foreach ($pots as $pot) : ?>
            <?php
            $pot_light_device = trim((string) ($pot['light_device'] ?? ''));
            $pot_has_device = $pot_light_device !== '' && ! empty($pot['has_device']);
            $pot_can_control_device = $can_control_garden && $pot_has_device;
            ?>
            <details class="card pot-card" style="padding:18px">
                <summary style="cursor:pointer">
                    <div class="pot-row-summary">
                        <div class="pot-row-media-wrap">
                            <div class="pot-row-media media-frame media-frame-16x9">
                                <img class="media-fit-cover" src="<?php echo esc_url($pot['image'] ?? (get_template_directory_uri() . '/assets/images/hero-greenhouse.svg')); ?>" alt="<?php echo esc_attr($pot['name']); ?>">
                            </div>
                            <div class="pot-detail-toolbar" style="margin-top:10px">
                                <a class="btn btn-secondary" href="<?php echo esc_url(add_query_arg(['compose' => '1', 'garden' => $garden_key], home_url('/cho-que/'))); ?>">Đăng Chợ quê</a>
                            </div>
                        </div>

                        <div class="pot-row-main">
                            <div class="pot-row-top">
                                <div>
                                    <div class="kicker"><?php echo esc_html($pot['code']); ?></div>
                                    <?php if ($can_control_garden) : ?>
                                        <div
                                            class="pot-inline-rename"
                                            data-pot-inline-name
                                            data-pot-code="<?php echo esc_attr($pot['code']); ?>"
                                            data-pot-name="<?php echo esc_attr($pot['name']); ?>"
                                        >
                                            <div class="pot-inline-row">
                                                <span class="pot-inline-name-text"><?php echo esc_html($pot['name']); ?></span>
                                                <input
                                                    type="text"
                                                    class="pot-inline-input"
                                                    value="<?php echo esc_attr($pot['name']); ?>"
                                                    data-pot-inline-input
                                                    maxlength="120"
                                                    aria-label="Tên khay <?php echo esc_attr($pot['code']); ?>"
                                                    hidden
                                                >
                                                <button
                                                    class="pot-inline-name-edit"
                                                    type="button"
                                                    data-pot-inline-edit
                                                    aria-label="Đổi tên khay <?php echo esc_attr($pot['code']); ?>"
                                                >✏️</button>
                                                <span class="pot-inline-save-status" data-pot-inline-status hidden>Nhấn Enter hoặc click ra ngoài để lưu</span>
                                            </div>
                                        </div>
                                    <?php else : ?>
                                        <h3><?php echo esc_html($pot['name']); ?></h3>
                                    <?php endif; ?>
                                </div>
                                <span class="chip"><?php echo esc_html($pot['status']); ?></span>
                            </div>

                            <div class="pot-control-layout pot-control-layout-flat">
                                <div class="control-environment-metrics pot-metric-grid">
                                    <span class="chip">pH: <?php echo esc_html($pot['ph']); ?></span>
                                    <span class="chip" data-pot-temp-chip>Nhiệt độ: -- °C</span>
                                    <span class="chip" data-pot-hum-chip>Độ ẩm: -- %</span>
                                    <span class="chip control-environment-status-chip"><?php echo esc_html($pot['status']); ?></span>
                                </div>

                                <div class="toggle-row control-toggle-row pot-control-buttons">
                                    <div class="pot-control-button-group">
                                        <?php if ($pot_can_control_device) : ?>
                                            <button class="blynk-light-toggle is-on" type="button" data-blynk-light-toggle="<?php echo esc_attr($pot_light_device); ?>" data-light-label="<?php echo esc_attr($pot['light']); ?>" data-action-on="Tắt đèn đi" data-action-off="Bật đèn lên" data-icon-on="💡" data-icon-off="🔅" data-state="1" onclick="return window.aitrLightToggle(this, event)">
                                                <span class="blynk-light-main">
                                                    <span class="blynk-light-icon">💡</span>
                                                    <span class="blynk-light-copy">
                                                        <span class="blynk-light-status-row"><span class="blynk-light-status-pill">Đang bật</span></span>
                                                        <span class="blynk-light-action-row"><span class="blynk-light-action-text">Tắt đèn đi</span></span>
                                                    </span>
                                                </span>
                                            </button>
                                        <?php elseif ($can_control_garden) : ?>
                                            <button class="blynk-light-toggle is-disabled" type="button" disabled aria-disabled="true" title="Khay này chưa gắn bộ device">
                                                <span class="blynk-light-main"><span class="blynk-light-icon">💡</span><span class="blynk-light-off">Chưa gắn device</span></span>
                                            </button>
                                        <?php else : ?>
                                            <button class="blynk-light-toggle is-disabled" type="button" disabled aria-disabled="true">
                                                <span class="blynk-light-main"><span class="blynk-light-icon">💡</span><span class="blynk-light-off">Chỉ xem</span></span>
                                            </button>
                                        <?php endif; ?>
                                    </div>

                                    <div class="pot-control-button-group">
                                        <?php if ($pot_can_control_device) : ?>
                                            <button class="blynk-light-toggle is-off" type="button" data-blynk-pump-toggle="pump" data-action-on="Tắt bơm đi" data-action-off="Bật bơm lên" data-icon-on="🫧" data-icon-off="💧" data-state="0">
                                                <span class="blynk-light-main">
                                                    <span class="blynk-light-icon">💧</span>
                                                    <span class="blynk-light-copy">
                                                        <span class="blynk-light-status-row"><span class="blynk-light-status-pill">Đang tắt</span><span class="blynk-light-status-text">Bơm chung hiện chưa chạy</span></span>
                                                        <span class="blynk-light-action-row"><span class="blynk-light-action-text">Bật bơm lên</span></span>
                                                    </span>
                                                </span>
                                            </button>
                                        <?php elseif ($can_control_garden) : ?>
                                            <button class="blynk-light-toggle is-disabled" type="button" disabled aria-disabled="true" title="Khay này chưa gắn bộ device">
                                                <span class="blynk-light-main"><span class="blynk-light-icon">🫧</span><span class="blynk-light-off">Chưa gắn device</span></span>
                                            </button>
                                        <?php else : ?>
                                            <button class="blynk-light-toggle is-disabled" type="button" disabled aria-disabled="true">
                                                <span class="blynk-light-main"><span class="blynk-light-icon">🫧</span><span class="blynk-light-off">Chỉ xem</span></span>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <?php if ($pot_has_device) : ?>
                                <div class="small subtle live-device-status" style="margin-top:6px" data-pot-live-status="<?php echo esc_attr($pot_light_device); ?>">Trạng thái đèn của khay đang đồng bộ...</div>
                            <?php elseif ($can_control_garden) : ?>
                                <div class="small subtle live-device-status" style="margin-top:6px">Khay này chưa gắn bộ device nên chưa có trạng thái điều khiển live.</div>
                            <?php else : ?>
                                <div class="small subtle live-device-status" style="margin-top:6px">Chế độ chỉ xem.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </summary>

                <div class="pot-detail-grid">
                    <div>
                        <aside class="pot-side-panel">
                            <div>
                                <h4 style="margin-bottom:8px">Nhận xét của AI</h4>
                                <p class="small subtle" style="margin:0 0 8px;font-weight:700;color:var(--forest-deep)"><?php echo esc_html($pot['harvest_eta']); ?></p>
                                <p class="small subtle" style="margin:0 0 8px"><?php echo esc_html($pot['status_summary']); ?></p>
                                <p class="small subtle" style="margin:0"><?php echo esc_html($pot['ai_note']); ?></p>
                            </div>
                        </aside>

                        <div style="margin-top:14px">
                            <label for="note-<?php echo esc_attr($pot['code']); ?>" class="small subtle">Ghi thêm vào nhật ký canh tác</label>
                            <textarea id="note-<?php echo esc_attr($pot['code']); ?>" data-autosave-note data-note-key="<?php echo esc_attr($pot['code']); ?>" placeholder="Ví dụ: hôm nay vừa gieo hạt, vừa thay nước, lá gốc bắt đầu vàng nhẹ..."><?php echo esc_textarea((string) ($pot_notes[(string) ($pot['code'] ?? '')]['note_text'] ?? '')); ?></textarea>
                            <div class="inline-list" style="margin-top:10px"><span class="note-status-live" data-note-status>Sẵn sàng</span></div>
                        </div>
                    </div>
                </div>
            </details>
        <?php endforeach; ?>
        <div class="pot-add-cta-wrap">
            <a class="btn btn-primary pot-add-cta" href="<?php echo esc_url($garden_key !== '' ? add_query_arg(['garden' => rawurlencode($garden_key), 'mode' => 'onboarding'], home_url('/portal/tro-ly-ai/')) : add_query_arg('mode', 'onboarding', home_url('/portal/tro-ly-ai/'))); ?>">＋ khay cây</a>
        </div>
    </div>
</section>
                    <section class="portal-card span-12"><div class="section-head" style="margin-bottom:18px"><span class="eyebrow">Lịch sử canh tác</span><h3 style="margin-bottom:0">Các mốc thực tế đang theo dõi</h3><div class="inline-list" style="margin-top:14px"><a class="btn btn-secondary" href="<?php echo esc_url(home_url('/portal/nhat-ky-cham-soc/')); ?>">Mở kho ảnh</a></div></div><?php $garden_activity_cards = []; foreach ($pots as $pot_item) : $pot_code = trim((string) ($pot_item['code'] ?? '')); $pot_name = trim((string) ($pot_item['name'] ?? '')); $pot_note = trim((string) ($pot_notes[$pot_code]['note_text'] ?? '')); $pot_summary = trim((string) ($pot_item['status_summary'] ?? '')); $pot_status = trim((string) ($pot_item['status'] ?? '')); $pot_eta = trim((string) ($pot_item['harvest_eta'] ?? '')); $activity_text = $pot_note !== '' ? $pot_note : ($pot_summary !== '' ? $pot_summary : ($pot_status !== '' ? $pot_status : '')); if ($activity_text === '' && $pot_eta !== '') { $activity_text = $pot_eta; } if ($activity_text === '') { continue; } $activity_label = $pot_code !== '' ? $pot_code : ($pot_name !== '' ? $pot_name : 'Khay cây'); $garden_activity_cards[] = ['label' => $activity_label, 'text' => $activity_text, 'is_note' => $pot_note !== '']; if (count($garden_activity_cards) >= 3) { break; } endforeach; ?><?php if ($garden_activity_cards) : ?><div class="cards-3"><?php foreach ($garden_activity_cards as $activity_item) : ?><div class="card soft-card"><div class="kicker"><?php echo esc_html($activity_item['label']); ?><?php if (! empty($activity_item['is_note'])) : ?> · Ghi chú mới<?php endif; ?></div><p style="margin:0"><?php echo esc_html($activity_item['text']); ?></p></div><?php endforeach; ?></div><?php else : ?><div class="notice">Vườn này chưa có mốc canh tác nào được ghi nhận.</div><?php endif; ?></section>
                    <section class="portal-card span-12" id="tool-shelf" style="scroll-margin-top:24px">
    <div class="section-head" style="margin-bottom:16px;display:flex;justify-content:space-between;align-items:flex-end;gap:12px;flex-wrap:wrap">
        <div><h3 style="margin-bottom:0">Vật tư đang dùng cho khu vườn này</h3></div>
        <div class="inline-list"><button class="btn btn-secondary" type="button" data-shelf-prev>←</button><button class="btn btn-secondary" type="button" data-shelf-next>→</button></div>
    </div>

    <div class="cards-4" data-tool-shelf>
        <?php foreach ($tool_shelf as $index => $item) : ?>
            <div class="store-item-card" data-shelf-item data-shelf-index="<?php echo esc_attr((string) $index); ?>">
                <div class="store-item-media-wrap">
                    <a href="#" class="store-item-open" data-tool-popup-open data-tool-name="<?php echo esc_attr($item['name']); ?>" data-tool-type="<?php echo esc_attr($item['type']); ?>" data-tool-desc="<?php echo esc_attr($item['description']); ?>" data-tool-image="<?php echo esc_attr(get_template_directory_uri() . '/assets/images/' . ($item['image'] ?? 'tools-shed.svg')); ?>">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/' . ($item['image'] ?? 'tools-shed.svg')); ?>" alt="<?php echo esc_attr($item['name']); ?>" class="store-item-media">
                    </a>
                    <span class="store-stock-badge"><?php echo esc_html((string) $item['owned']); ?></span>
                </div>
                <a href="#" class="store-item-title store-item-open" data-tool-popup-open data-tool-name="<?php echo esc_attr($item['name']); ?>" data-tool-type="<?php echo esc_attr($item['type']); ?>" data-tool-desc="<?php echo esc_attr($item['description']); ?>" data-tool-image="<?php echo esc_attr(get_template_directory_uri() . '/assets/images/' . ($item['image'] ?? 'tools-shed.svg')); ?>"><?php echo esc_html($item['name']); ?></a>
                <small class="subtle"><?php echo esc_html($item['type']); ?></small>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="notice" style="margin-top:14px"><div class="inline-list" style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap"><strong>Khu vật tư này giúp theo dõi sát từng khay đang có trong vườn. Anh cũng có thể hỏi AI để nhận gợi ý phù hợp theo từng khay cây cụ thể.</strong><a class="btn btn-secondary" href="<?php echo esc_url(home_url('/portal/tro-ly-ai/')); ?>">💬 Hỏi trợ lý AI</a></div></div>

    <div class="tool-popup" data-tool-popup hidden>
        <div class="tool-popup-backdrop" data-tool-popup-close></div>
        <div class="tool-popup-card" role="dialog" aria-modal="true" aria-label="Chi tiết vật phẩm">
            <button class="tool-popup-close" type="button" data-tool-popup-close>×</button>
            <img src="" alt="" class="tool-popup-image" data-tool-popup-image>
            <div class="kicker" data-tool-popup-type></div>
            <h3 style="margin:8px 0 10px" data-tool-popup-name></h3>
            <p style="margin:0" data-tool-popup-desc></p>
        </div>
    </div>
</section>
                    <?php else : ?>
                    <section class="portal-card span-12">
                        <div class="notice" style="margin:0;padding:24px;border-radius:24px;background:linear-gradient(180deg,#f7fff8 0%,#eefbf0 100%);border:1px solid rgba(47,123,69,.16)">
                            <div class="section-head" style="margin-bottom:14px">
                                <span class="eyebrow">Khu vườn của anh đang chờ</span>
                                <h3 style="margin-bottom:0">Chưa có khay trồng cây nào ở đây cả</h3>
                            </div>
                            <p style="margin:0 0 14px">Giống như một góc vườn mới dọn đất xong, chỗ này đang chờ anh đặt khay trồng cây đầu tiên vào để bắt đầu theo dõi mỗi ngày.</p>
                            <div class="inline-list" style="margin-top:14px">
                                <a class="btn btn-primary" href="<?php echo esc_url($garden_key !== '' ? add_query_arg(['garden' => rawurlencode($garden_key), 'mode' => 'onboarding'], home_url('/portal/tro-ly-ai/')) : add_query_arg('mode', 'onboarding', home_url('/portal/tro-ly-ai/'))); ?>">Thêm khay trồng cây đầu tiên</a>
                                <a class="btn btn-secondary" href="<?php echo esc_url(home_url('/portal/chia-se-khu-vuon/')); ?>">Mời người vào vườn</a>
                            </div>
                        </div>
                    </section>
                    <?php endif; ?>
                </div>
            <?php elseif ($slug === 'portal/nhat-ky-cham-soc') : ?>
                <div class="portal-cards">
                    <section class="portal-card span-12">
                        <div class="section-head" style="margin-bottom:18px">
                            <span class="eyebrow">Kho ảnh của vườn</span>
                            <h3 style="margin-bottom:0">Ảnh thực tế đã phân theo từng khay</h3>
                            <p class="small subtle" style="margin:8px 0 0">Các ảnh dưới đây đã được nhập vào đúng tài khoản vườn của anh và gắn theo từng khay để tiện theo dõi, đối chiếu và dùng cho trợ lý AI sau này.</p>
                        </div>
                        <div class="cards-4" style="margin-bottom:16px">
                            <?php foreach ($pots as $pot_item) : ?>
                                <?php $photo_count = count($pot_photo_groups[$pot_item['code']] ?? []); ?>
                                <div class="card soft-card">
                                    <div class="kicker"><?php echo esc_html($pot_item['code']); ?></div>
                                    <h4 style="margin:6px 0"><?php echo esc_html($pot_item['name']); ?></h4>
                                    <p class="small subtle" style="margin:0">Hiện có <?php echo esc_html((string) $photo_count); ?> ảnh trong kho · <?php echo esc_html($pot_item['light']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                    <?php foreach ($pots as $pot_item) : ?>
                        <?php $group_items = $pot_photo_groups[$pot_item['code']] ?? []; ?>
                        <section class="portal-card span-12" id="photo-<?php echo esc_attr(strtolower($pot_item['code'])); ?>">
                            <div class="section-head" style="margin-bottom:18px">
                                <span class="eyebrow"><?php echo esc_html($pot_item['code']); ?></span>
                                <h3 style="margin-bottom:0"><?php echo esc_html($pot_item['name']); ?></h3>
                                <p class="small subtle" style="margin:8px 0 0">Ảnh thuộc khay này · <?php echo esc_html($pot_item['status']); ?> · <?php echo esc_html($pot_item['light']); ?></p>
                            </div>
                            <?php if (! empty($group_items)) : ?>
                                <div class="cards-3">
                                    <?php foreach ($group_items as $photo_post) : ?>
                                        <?php
                                        $attachment_id = (int) $photo_post->ID;
                                        $image_url = set_url_scheme((string) (wp_get_attachment_image_url($attachment_id, 'large') ?: wp_get_attachment_url($attachment_id)), 'https');
                                        $download_url = set_url_scheme((string) wp_get_attachment_url($attachment_id), 'https');
                                        $title = get_the_title($attachment_id);
                                        $caption = trim((string) ($photo_post->post_content ?: 'Ảnh thực tế của ' . $pot_item['name']));
                                        ?>
                                        <article class="card soft-card" data-photo-card>
                                            <div class="card-media media-frame media-frame-16x9">
                                                <img class="media-thumb media-fit-cover" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>">
                                            </div>
                                            <div class="kicker"><?php echo esc_html($pot_item['code']); ?> · Ảnh thật</div>
                                            <h4 data-photo-title style="margin:8px 0 6px"><?php echo esc_html($title); ?></h4>
                                            <p class="small subtle" style="margin:0"><?php echo esc_html($caption); ?></p>
                                            <div class="inline-list photo-actions" style="margin-top:12px">
                                                <a class="small-link" href="<?php echo esc_url($download_url); ?>" target="_blank" rel="noopener">Mở ảnh gốc</a>
                                            </div>
                                        </article>
                                    <?php endforeach; ?>
                                </div>
                            <?php else : ?>
                                <div class="notice">Chưa có ảnh nào được gắn vào khay này.</div>
                            <?php endif; ?>
                        </section>
                    <?php endforeach; ?>
                    <?php if (! empty($pot_photo_groups['UNGROUPED'])) : ?>
                        <section class="portal-card span-12">
                            <div class="section-head" style="margin-bottom:18px">
                                <span class="eyebrow">Chưa phân loại</span>
                                <h3 style="margin-bottom:0">Ảnh cần gắn thêm vào khay cụ thể</h3>
                            </div>
                            <div class="cards-3">
                                <?php foreach ($pot_photo_groups['UNGROUPED'] as $photo_post) : ?>
                                    <?php $attachment_id = (int) $photo_post->ID; ?>
                                    <article class="card soft-card"><h4 style="margin:0 0 8px"><?php echo esc_html(get_the_title($attachment_id)); ?></h4><p class="small subtle" style="margin:0">Ảnh này đã thuộc tài khoản vườn nhưng chưa gắn mã khay.</p></article>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endif; ?>
                </div>
            <?php elseif ($slug === 'portal/ban-be') : ?>
                <?php
                $friend_count = count($friends);
                $pending_friend_count = count($friend_invites);
                $discover_count = count($active_garden_owners);
                $share_garden_url = $garden_key !== '' ? add_query_arg('garden', rawurlencode($garden_key), home_url('/portal/chia-se-khu-vuon/')) : home_url('/portal/chia-se-khu-vuon/');
                $friends_page_url = $garden_key !== '' ? add_query_arg('garden', rawurlencode($garden_key), home_url('/portal/ban-be/')) : home_url('/portal/ban-be/');
                ?>
                <div class="portal-cards portal-friends-page">
                    <section class="portal-card span-12 share-hero-card friends-hero-card">
                        <div class="friends-hero-stats">
                            <article class="friends-hero-stat">
                                <span class="friends-hero-stat__label">Bạn bè</span>
                                <span class="friends-hero-stat__value"><?php echo esc_html((string) $friend_count); ?></span>
                            </article>
                            <article class="friends-hero-stat">
                                <span class="friends-hero-stat__label">Lời mời kết bạn</span>
                                <span class="friends-hero-stat__value"><?php echo esc_html((string) $pending_friend_count); ?></span>
                            </article>
                        </div>
                    </section>

                    <section class="portal-card span-12 friends-section friends-discovery-section">

                        <div class="card soft-card active-owner-explorer friends-explorer-card">
                            <form method="get" class="active-owner-search" action="<?php echo esc_url(home_url('/portal/ban-be/')); ?>">
                                <?php if ($garden_key !== '') : ?><input type="hidden" name="garden" value="<?php echo esc_attr($garden_key); ?>"><?php endif; ?>
                                <div class="form-stack" style="margin-top:8px">
                                    <input id="owner-search" type="search" name="owner_search" value="<?php echo esc_attr($friend_owner_search); ?>" placeholder="Tìm theo tên">
                                    <button class="btn btn-secondary" type="submit">Tìm</button>
                                    <?php if ($friend_owner_search !== '') : ?><a class="btn btn-ghost" href="<?php echo esc_url($friends_page_url); ?>">Xóa lọc</a><?php endif; ?>
                                </div>
                            </form>
                            <?php if ($friend_owner_search === '') : ?>

                            <?php elseif ($active_garden_owners) : ?>
                                <div class="social-stack active-owner-list friends-discovery-list" style="margin-top:16px">
                                    <?php foreach ($active_garden_owners as $owner_card) :
                                        $owner_name = (string) ($owner_card['display_name'] ?? $owner_card['user_login'] ?? 'Chủ vườn');
                                        $owner_target = (string) ($owner_card['user_login'] ?? '');
                                        $friendship_status = (string) ($owner_card['friendship_status'] ?? 'none');
                                        $friendship_direction = (string) ($owner_card['friendship_direction'] ?? 'none');
                                        $cta_label = 'Kết bạn';
                                        $cta_disabled = false;
                                        if ($friendship_status === 'accepted') {
                                            $cta_label = 'Đã là bạn bè';
                                            $cta_disabled = true;
                                        } elseif ($friendship_status === 'pending' && $friendship_direction === 'outgoing') {
                                            $cta_label = 'Đã gửi lời mời';
                                            $cta_disabled = true;
                                        } elseif ($friendship_status === 'pending' && $friendship_direction === 'incoming') {
                                            $cta_label = 'Đang chờ phản hồi';
                                            $cta_disabled = true;
                                        }
                                    ?>
                                        <article class="social-item active-owner-item friend-directory-item is-compact">
                                            <div class="social-item-main">
                                                <div class="member-row-head">
                                                    <strong><?php echo esc_html($owner_name); ?></strong>
                                                </div>
                                            </div>
                                            <div class="social-actions">
                                                <button class="btn <?php echo $cta_disabled ? 'btn-secondary' : 'btn-primary'; ?>" type="button" data-send-friend-request-target="<?php echo esc_attr($owner_target); ?>"<?php echo $cta_disabled ? ' disabled aria-disabled="true"' : ''; ?>><?php echo esc_html($cta_label); ?></button>
                                            </div>
                                        </article>
                                    <?php endforeach; ?>
                                </div>
                            <?php else : ?>
                                <div class="notice friends-search-empty" style="margin-top:16px">Không tìm thấy người phù hợp với tên anh vừa nhập.</div>
                            <?php endif; ?>
                        </div>
                    </section>

                    <section class="portal-card span-5 friends-section friends-invites-section">

                        <?php if ($friend_invites) : ?>
                            <div class="social-stack friends-invite-stack">
                                <?php foreach ($friend_invites as $invite) : $sender = get_user_by('id', (int) $invite['requester_user_id']); ?>
                                    <article class="social-item highlight-item friend-request-item">
                                        <div class="social-item-main">
                                            <div class="member-row-head">
                                                <strong><?php echo esc_html($sender instanceof WP_User ? ($sender->display_name ?: $sender->user_login) : 'Người dùng'); ?></strong>
                                                <span class="member-badge is-sand">Đang chờ phản hồi</span>
                                            </div>
                                            <p style="margin:0">Nếu chấp nhận, người này sẽ xuất hiện ngay ở danh sách bạn bè hiện tại.</p>
                                        </div>
                                        <div class="social-actions">
                                            <button class="btn btn-primary" type="button" data-accept-friendship="<?php echo esc_attr((string) $invite['id']); ?>">Chấp nhận</button>
                                            <button class="btn btn-secondary" type="button" data-reject-friendship="<?php echo esc_attr((string) $invite['id']); ?>">Từ chối</button>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                            <div class="notice">Hiện anh chưa có lời mời kết bạn nào cần phản hồi.</div>
                        <?php endif; ?>
                    </section>

                    <section class="portal-card span-7 friends-section friends-current-section">

                        <?php if ($friends) : ?>
                            <div class="social-stack current-friends-stack">
                                <?php foreach ($friends as $friendship) :
                                    $friend_id = (int) (($friendship['requester_user_id'] == $current_user->ID) ? $friendship['addressee_user_id'] : $friendship['requester_user_id']);
                                    $friend = get_user_by('id', $friend_id);
                                    $friend_garden_key = $friend instanceof WP_User ? aitrongcay_preferred_garden_key_for_user($friend) : '';
                                    $friend_garden_url = $friend_garden_key !== ''
                                        ? add_query_arg('garden', rawurlencode($friend_garden_key), home_url('/portal/dashboard/'))
                                        : home_url('/portal/dashboard/');
                                ?>
                                    <article class="social-item current-friend-item">
                                        <div class="social-item-main">
                                            <div class="member-row-head">
                                                <strong><?php echo esc_html($friend instanceof WP_User ? ($friend->display_name ?: $friend->user_login) : 'Người dùng'); ?></strong>
                                                <span class="member-badge is-sky">Bạn bè</span>
                                            </div>
                                            
                                        </div>
                                        <div class="social-actions">
                                            <a class="btn btn-secondary" href="<?php echo esc_url($friend_garden_url); ?>">Xem vườn</a>
                                            <a class="btn btn-secondary" href="<?php echo esc_url($share_garden_url); ?>">Mời vào khu vườn</a>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                            <div class="notice">Anh chưa có bạn bè nào. Anh có thể gửi lời mời ở khung bên cạnh hoặc chọn một chủ vườn trong mục khám phá để bắt đầu.</div>
                        <?php endif; ?>
                    </section>
                </div>
            <?php elseif ($slug === 'portal/chia-se-khu-vuon') : ?>
                <div class="portal-cards">
                    <section class="portal-card span-12 share-hero-card"><div class="section-head" style="margin-bottom:0"><span class="eyebrow">Chia sẻ khu vườn</span><h3 style="margin-bottom:0" data-garden-display-name><?php echo esc_html($active_profile['garden_name']); ?></h3><p class="small subtle" style="margin:8px 0 0">Tại đây anh xem ai đang tham gia, ai còn chờ phản hồi và bước tiếp theo để mời thêm người.</p></div><div class="portal-switcher-inline-meta"><span class="member-badge <?php echo esc_attr($current_role_badge_class); ?>">Vai trò của anh: <?php echo esc_html($current_role_label); ?></span><span class="chip"><?php echo esc_html($active_member_count); ?> thành viên đang tham gia</span><span class="chip"><?php echo esc_html($pending_member_count); ?> lời mời đang chờ</span></div></section>
                    <section class="portal-card span-5"><div class="section-head" style="margin-bottom:18px"><span class="eyebrow">Lời mời gửi cho anh</span><h3 style="margin-bottom:0">Những khu vườn anh chưa phản hồi</h3></div><?php if ($garden_invites) : ?><div class="social-stack"><?php foreach ($garden_invites as $invite) : $invite_owner = aitrongcay_get_garden_owner_user((string) ($invite['garden_key'] ?? '')); $invite_profile = aitrongcay_portal_profile_for_garden_context((string) ($invite['garden_key'] ?? ''), $current_user instanceof WP_User ? $current_user : null); ?><article class="social-item highlight-item"><div class="social-item-main"><div class="member-row-head"><strong><?php echo esc_html($invite_profile['garden_name'] ?? 'Khu vườn được chia sẻ'); ?></strong><span class="member-badge is-sand">Đang chờ phản hồi</span></div><p style="margin:0">Nếu chấp nhận, khu vườn này sẽ xuất hiện trong bộ chọn vườn của anh.</p><div class="social-meta-row"><span class="member-badge <?php echo esc_attr(aitrongcay_get_role_badge_class((string) ($invite['role'] ?? 'viewer'))); ?>">Vai trò được mời: <?php echo esc_html(aitrongcay_get_role_label((string) ($invite['role'] ?? 'viewer'))); ?></span><span class="subtle"><?php if ($invite_owner instanceof WP_User) : ?>Chủ vườn: <?php echo esc_html($invite_owner->display_name ?: $invite_owner->user_login); ?><?php endif; ?></span></div></div><div class="social-actions"><button class="btn btn-primary" type="button" data-accept-garden-invite="<?php echo esc_attr((string) $invite['id']); ?>">Chấp nhận</button><button class="btn btn-secondary" type="button" data-decline-garden-invite="<?php echo esc_attr((string) $invite['id']); ?>">Từ chối</button></div></article><?php endforeach; ?></div><?php else : ?><div class="notice">Hiện chưa có khu vườn nào đang chờ anh phản hồi.</div><?php endif; ?></section>
                    <section class="portal-card span-7"><div class="section-head" style="margin-bottom:18px"><span class="eyebrow">Thành viên hiện tại</span><h3 style="margin-bottom:0">Những người đã vào khu vườn này</h3><p class="small subtle" style="margin:8px 0 0">Danh sách này chỉ gồm các thành viên đã chấp nhận lời mời.</p></div><div class="social-stack"><?php foreach ([['title' => 'Chủ vườn', 'items' => $owner_member ? [$owner_member] : []], ['title' => 'Đồng sở hữu', 'items' => $co_owner_members], ['title' => 'Người xem', 'items' => $viewer_members]] as $group) : ?><div class="member-group"><div class="member-group-head"><strong><?php echo esc_html($group['title']); ?></strong><span class="small subtle"><?php echo esc_html(count($group['items'])); ?> người</span></div><?php if ($group['items']) : ?><?php foreach ($group['items'] as $member) : $member_user = get_user_by('id', (int) $member['user_id']); $member_role = (string) ($member['role'] ?? 'viewer'); ?><article class="social-item member-row"><div class="social-item-main"><div class="member-row-head"><strong><?php echo esc_html($member_user instanceof WP_User ? ($member_user->display_name ?: $member_user->user_login) : 'Người dùng'); ?></strong><span class="member-badge <?php echo esc_attr(aitrongcay_get_role_badge_class($member_role)); ?>"><?php echo esc_html(aitrongcay_get_role_label($member_role)); ?></span><span class="member-badge is-sand"><?php echo esc_html(aitrongcay_get_member_status_label((string) ($member['status'] ?? 'active'))); ?></span></div><p style="margin:0"><?php echo esc_html((int) $member['user_id'] === (int) $current_user->ID ? 'Đây là account anh đang dùng để mở khu vườn này.' : ($member_role === 'viewer' ? 'Có thể xem dashboard, ảnh, log và trạng thái thiết bị; không điều khiển hay mời thêm người.' : ($member_role === 'owner' ? 'Giữ quyền quản trị chia sẻ và quyết định ai được tham gia khu vườn.' : 'Có thể theo dõi và điều khiển thiết bị, nhưng chưa quản trị danh sách thành viên.'))); ?></p></div><div class="social-actions<?php echo $can_manage_members && $member_role !== 'owner' ? '' : ' is-disabled'; ?>"><?php if ($can_manage_members && $member_role !== 'owner') : ?><div style="min-width:220px"><label class="small subtle" for="member-role-<?php echo esc_attr((string) $member['id']); ?>">Vai trò</label><select id="member-role-<?php echo esc_attr((string) $member['id']); ?>" data-member-role-select="<?php echo esc_attr((string) $member['id']); ?>" style="margin-top:8px;width:100%"><option value="co_owner"<?php selected($member_role, 'co_owner'); ?>>Đồng sở hữu</option><option value="viewer"<?php selected($member_role, 'viewer'); ?>>Chỉ xem</option></select></div><button class="btn btn-secondary" type="button" data-update-garden-member="<?php echo esc_attr((string) $member['id']); ?>">Lưu vai trò</button><button class="btn btn-ghost" type="button" data-remove-garden-member="<?php echo esc_attr((string) $member['id']); ?>">Gỡ khỏi khu vườn</button><?php else : ?><span class="subtle"><?php echo esc_html($member_role === 'owner' ? 'Chủ vườn luôn giữ quyền quản trị chính.' : 'Chỉ chủ vườn mới đổi vai trò hoặc gỡ thành viên.'); ?></span><?php endif; ?></div></article><?php endforeach; ?><?php else : ?><div class="notice">Chưa có ai trong nhóm này.</div><?php endif; ?></div><?php endforeach; ?></div></section>
                    <section class="portal-card span-7"><div class="section-head" style="margin-bottom:18px"><span class="eyebrow">Lời mời đang chờ</span><h3 style="margin-bottom:0">Những người đã được mời nhưng chưa vào vườn</h3></div><?php if ($pending_members) : ?><div class="social-stack"><?php foreach ($pending_members as $member) : $member_user = get_user_by('id', (int) $member['user_id']); $member_role = (string) ($member['role'] ?? 'viewer'); ?><article class="social-item highlight-item"><div class="social-item-main"><div class="member-row-head"><strong><?php echo esc_html($member_user instanceof WP_User ? ($member_user->display_name ?: $member_user->user_login) : 'Người dùng'); ?></strong><span class="member-badge <?php echo esc_attr(aitrongcay_get_role_badge_class($member_role)); ?>">Được mời làm <?php echo esc_html(aitrongcay_get_role_label($member_role)); ?></span><span class="member-badge is-sand"><?php echo esc_html(aitrongcay_get_member_status_label((string) ($member['status'] ?? 'invited'))); ?></span></div><p style="margin:0">Người này sẽ xuất hiện ở phần thành viên hiện tại sau khi chấp nhận lời mời.</p></div><div class="social-actions<?php echo $can_manage_members ? '' : ' is-disabled'; ?>"><?php if ($can_manage_members) : ?><button class="btn btn-ghost" type="button" data-remove-garden-member="<?php echo esc_attr((string) $member['id']); ?>">Hủy lời mời</button><?php else : ?><span class="subtle">Chỉ chủ vườn mới hủy lời mời đang chờ.</span><?php endif; ?></div></article><?php endforeach; ?></div><?php else : ?><div class="notice">Hiện chưa có lời mời nào đang chờ. Khi anh gửi lời mời mới, trạng thái sẽ hiện ở đây.</div><?php endif; ?></section>
                    <section class="portal-card span-5"><?php if ($can_manage_members) : ?><div class="section-head" style="margin-bottom:18px"><span class="eyebrow">Mời bạn vào khu vườn</span><h3 style="margin-bottom:0">Chọn đúng người, đúng vai trò</h3><p class="small subtle" style="margin:8px 0 0">Bước 1: chọn một người trong danh sách bạn bè. Bước 2: chọn vai trò. Bước 3: gửi lời mời.</p></div><?php if ($friends) : ?><div class="card soft-card"><label class="small subtle" for="garden-friend">Chọn bạn bè</label><select id="garden-friend" data-garden-friend style="margin-top:8px;width:100%"><option value="">-- Chọn người --</option><?php foreach ($friends as $friendship) : $friend_id = (int) (($friendship['requester_user_id'] == $current_user->ID) ? $friendship['addressee_user_id'] : $friendship['requester_user_id']); $friend = get_user_by('id', $friend_id); ?><option value="<?php echo esc_attr((string) $friend_id); ?>"><?php echo esc_html($friend instanceof WP_User ? ($friend->display_name ?: $friend->user_login) : 'Người dùng'); ?></option><?php endforeach; ?></select><label class="small subtle" for="garden-role" style="display:block;margin-top:12px">Vai trò khi vào vườn</label><select id="garden-role" data-garden-role style="margin-top:8px;width:100%"><option value="co_owner">Đồng sở hữu</option><option value="viewer">Chỉ xem</option></select><div class="inline-list" style="margin-top:12px"><button class="btn btn-primary" type="button" data-invite-garden-member disabled aria-disabled="true">Gửi lời mời</button></div><div class="small subtle" style="margin-top:8px" data-garden-share-message>Hãy chọn một người bạn trước để mở nút gửi lời mời.</div></div><?php else : ?><div class="notice">Anh cần có bạn bè trước khi chia sẻ khu vườn. Sang mục Bạn bè để kết nối trước.</div><?php endif; ?><?php else : ?><div class="notice role-explainer role-explainer--<?php echo esc_attr($current_role); ?>">Anh đang dùng quyền <?php echo esc_html($current_role_label); ?> nên chưa thể mời thêm người hoặc đổi vai trò thành viên. Nếu cần quản trị chia sẻ, hãy dùng tài khoản Chủ vườn.</div><?php endif; ?></section>
                </div>
            <?php elseif ($slug === 'portal/tro-ly-ai') : ?>
                <?php $is_ai_onboarding = isset($_GET['mode']) && sanitize_key((string) wp_unslash($_GET['mode'])) === 'onboarding'; ?>
                <div class="portal-cards">
                    <section class="portal-card span-12">
                        <div class="section-head" style="margin-bottom:16px">
                            <span class="eyebrow"><?php echo esc_html($is_ai_onboarding ? 'AI khởi tạo khay' : 'Trợ lý AI'); ?></span>
                            <?php if ($is_ai_onboarding) : ?>
                                <h3 style="margin:8px 0 0">Em sẽ giúp anh tạo khay đầu tiên bằng chat</h3>
                                <p class="small subtle" style="margin:8px 0 0">Chỉ cần nói anh muốn trồng cây gì. Em sẽ khởi tạo khay rồi đưa anh về dashboard vườn.</p>
                            <?php endif; ?>
                        </div>
                        <div class="garden-ai-chat-shell" data-garden-ai-chat<?php echo $is_ai_onboarding ? ' data-ai-onboarding="1"' : ''; ?>>
                            <div class="garden-ai-chat-head"></div>
                            <div class="garden-ai-chat-log garden-ai-chat-log-page" data-garden-ai-log><?php if ($is_ai_onboarding) : ?><div class="garden-ai-message assistant"><div class="garden-ai-bubble">Chào anh, em sẽ hỗ trợ tạo khay đầu tiên cho khu vườn này. Anh muốn trồng cây gì ạ?</div></div><?php else : ?><div class="garden-ai-message assistant"><div class="garden-ai-bubble"><?php echo esc_html($garden_ai['summary']); ?></div></div><?php endif; ?></div>
                            <form class="garden-ai-chat-form" data-garden-ai-form<?php echo $is_ai_onboarding ? ' data-ai-onboarding-form="1"' : ''; ?>><textarea data-garden-ai-input placeholder="<?php echo esc_attr($is_ai_onboarding ? 'Ví dụ: em muốn trồng cà chua bi' : 'Nhập câu hỏi của anh/chị...'); ?>" rows="4"></textarea><div class="garden-ai-chat-foot"><div class="small subtle" data-garden-ai-meta><?php echo esc_html($is_ai_onboarding ? 'Sau khi anh trả lời, em sẽ tạo khay ngay và mở dashboard để mình theo dõi tiếp.' : 'Trợ lý sẽ ưu tiên các đề xuất rõ ràng, dễ thực hiện và an toàn cho cây.'); ?></div><button class="btn btn-primary" type="submit" data-garden-ai-submit><?php echo esc_html($is_ai_onboarding ? 'Tạo khay này' : 'Gửi câu hỏi'); ?></button></div></form>
                        </div>
                    </section>
                </div>
            <?php else : ?>
                <div class="notice">Trang này đang được giữ tối giản trong lần vá ổn định hiện tại.</div>
            <?php endif; ?>
        </main>
    </div>
</div>
<script>
var AITR_AJAX_URL = <?php echo wp_json_encode(admin_url('admin-ajax.php')); ?>;
var AITR_AJAX_NONCE = <?php echo wp_json_encode(wp_create_nonce('aitrongcay_portal_actions')); ?>;
var AITR_GARDEN_KEY = <?php echo wp_json_encode($garden_key); ?>;

(function () {
  var style = document.createElement('style');
  style.textContent = '' +
    '.pot-inline-rename{margin:6px 0 0;max-width:min(100%,420px);}' +
    '.garden-inline-rename{max-width:min(100%,680px);}' +
    '.garden-inline-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}' +
    '.garden-inline-name-text{margin:0;font-size:clamp(28px,4vw,40px);line-height:1.08;color:#0f172a;}' +
    '.garden-inline-input{min-width:min(100%,420px);max-width:100%;padding:12px 14px;border-radius:14px;border:1px solid rgba(15,23,42,.16);font:inherit;font-size:clamp(22px,3vw,32px);font-weight:700;line-height:1.08;color:#0f172a;background:#fff;box-shadow:0 10px 24px rgba(15,23,42,.08);}' +
    '.garden-inline-input:focus{outline:none;border-color:rgba(47,123,69,.55);box-shadow:0 0 0 3px rgba(47,123,69,.12);}' +
    '.garden-inline-name-edit{display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border:none;border-radius:999px;background:rgba(47,123,69,.12);cursor:pointer;font-size:18px;line-height:1;transition:transform .18s ease,background .18s ease;}' +
    '.garden-inline-name-edit:hover{transform:translateY(-1px);background:rgba(47,123,69,.18);}' +
    '.garden-inline-save-status{font-size:13px;color:#5c6b61;}' +
    '.garden-inline-save-status.is-saving{color:#7a5d16;}' +
    '.garden-inline-save-status.is-success{color:#2f7b45;}' +
    '.garden-inline-save-status.is-error{color:#bb3e2a;}' +
    '.pot-inline-row{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}' +
    '.pot-inline-name-text{font-size:1.5rem;font-weight:700;line-height:1.2;min-width:0;}' +
    '.pot-inline-name-edit{display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;padding:0;border:0;border-radius:999px;background:rgba(15,23,42,.06);color:inherit;cursor:pointer;font-size:.95rem;opacity:.72;transition:opacity .18s ease, transform .18s ease, background .18s ease;}' +
    '.pot-inline-name-edit:hover,.pot-inline-name-edit:focus-visible{opacity:1;transform:translateY(-1px);background:rgba(47,123,69,.14);outline:none;}' +
    '.pot-inline-input{flex:1 1 220px;min-width:180px;max-width:100%;padding:6px 0;border:0;border-bottom:2px solid rgba(47,123,69,.36);border-radius:0;font:inherit;font-weight:700;font-size:1.5rem;line-height:1.2;color:#0f172a;background:transparent;box-shadow:none;}' +
    '.pot-inline-input:focus{outline:none;border-bottom-color:rgba(47,123,69,.75);box-shadow:none;}' +
    '.pot-inline-save-status{font-size:.82rem;color:#5b6470;white-space:nowrap;}' +
    '.pot-inline-save-status.is-saving{color:#2f7b45;}' +
    '.pot-inline-save-status.is-error{color:#b42318;}' +
    '.pot-inline-save-status.is-success{color:#2f7b45;}';
  document.head.appendChild(style);
})();

(function () {
  var btn = document.getElementById('garden-view-mode-toggle');
  if (!btn) return;
  var states = [
    { key: 'private', label: 'Chế độ view riêng tư', icon: '🔒', cls: 'is-private' },
    { key: 'friend', label: 'Friend view', icon: '👥', cls: 'is-friend' },
    { key: 'public', label: 'Public view', icon: '🌍', cls: 'is-public' }
  ];
  var label = btn.querySelector('.view-mode-label');
  var icon = btn.querySelector('.view-mode-icon');
  function render(stateKey) {
    var state = states.find(function (item) { return item.key === stateKey; }) || states[0];
    states.forEach(function (item) { btn.classList.remove(item.cls); });
    btn.classList.add(state.cls);
    btn.setAttribute('data-state', state.key);
    if (label) label.textContent = state.label;
    if (icon) icon.textContent = state.icon;
  }
  btn.onclick = function () {
    var current = btn.getAttribute('data-state') || 'private';
    var idx = states.findIndex(function (item) { return item.key === current; });
    var next = states[(idx + 1) % states.length];
    render(next.key);
  };
  render(btn.getAttribute('data-state') || 'private');
})();

(function () {
  var card = document.getElementById('blynk-live-card');
  if (!card) return;

  var tempEl = card.querySelector('[data-blynk-temp]');
  var humEl = card.querySelector('[data-blynk-hum]');
  var soilEl = card.querySelector('[data-blynk-soil]');
  var statusEl = card.querySelector('[data-blynk-status]');

  function setStatus(text) {
    if (statusEl) statusEl.textContent = text;
  }

  function post(action, extra) {
    var body = new URLSearchParams();
    body.set('action', action);
    body.set('nonce', AITR_AJAX_NONCE);
    Object.keys(extra || {}).forEach(function (k) { body.set(k, String(extra[k])); });
    return fetch(AITR_AJAX_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      body: body.toString(),
      credentials: 'same-origin'
    }).then(function (r) { return r.json(); });
  }

  function renderSegmentToggle(btn, isOn, onText, offText) {
    if (!btn) return;
    btn.classList.toggle('is-on', !!isOn);
    btn.classList.toggle('is-off', !isOn);
    btn.setAttribute('data-state', isOn ? '1' : '0');
    btn.setAttribute('aria-pressed', isOn ? 'true' : 'false');
    var statusPill = btn.querySelector('.blynk-light-status-pill');
    var statusText = btn.querySelector('.blynk-light-status-text');
    var actionText = btn.querySelector('.blynk-light-action-text');
    var icon = btn.querySelector('.blynk-light-icon');
    if (statusPill) statusPill.textContent = isOn ? 'Đang bật' : 'Đang tắt';
    if (statusText) statusText.textContent = isOn ? onText : offText;
    if (icon) {
      var iconOn = btn.getAttribute('data-icon-on') || '💡';
      var iconOff = btn.getAttribute('data-icon-off') || iconOn;
      icon.textContent = isOn ? iconOn : iconOff;
    }
    if (actionText) {
      var actionOn = btn.getAttribute('data-action-on') || 'Tắt';
      var actionOff = btn.getAttribute('data-action-off') || 'Bật';
      actionText.textContent = isOn ? actionOn : actionOff;
    }
  }

  function syncSharedClimate(d) {
    var tempText = (d.temp != null ? d.temp.toFixed(1) : '--') + ' °C';
    var humText = (d.hum != null ? d.hum.toFixed(1) : '--') + ' %';
    document.querySelectorAll('[data-pot-temp-chip]').forEach(function (chip) {
      chip.textContent = 'Nhiệt độ: ' + tempText;
    });
    document.querySelectorAll('[data-pot-hum-chip]').forEach(function (chip) {
      chip.textContent = 'Độ ẩm: ' + humText;
    });
  }

  function renderLightToggle(btn, isOn) {
    renderSegmentToggle(btn, isOn, '', '');
    if (!btn) return;
    var device = btn.getAttribute('data-blynk-light-toggle');
    var statusText = '';
    var chip = document.querySelector('[data-light-status-chip="' + device + '"]');
    var live = document.querySelector('[data-pot-live-status="' + device + '"]');
    if (chip) chip.textContent = '💡 ' + (btn.getAttribute('data-light-label') || 'Đèn') + ': ' + (isOn ? 'BẬT' : 'TẮT');
    if (live) live.textContent = statusText;
  }

  function renderPumpToggle(btn, isOn) {
    renderSegmentToggle(btn, isOn, 'Bơm đang bật', 'Bơm đang tắt');
    document.querySelectorAll('[data-pump-status-chip]').forEach(function (chip) {
      chip.textContent = '🫧 Bơm chung: ' + (isOn ? 'BẬT' : 'TẮT');
    });
  }

  function refresh() {
    setStatus('Đang lấy dữ liệu...');
    post('aitrongcay_blynk_get_status', { garden_key: AITR_GARDEN_KEY }).then(function (res) {
      if (!res || !res.success) throw new Error((res && res.data && res.data.message) || 'Lỗi đọc dữ liệu');
      var d = res.data || {};
      if (tempEl) tempEl.textContent = (d.temp != null ? d.temp.toFixed(1) : '--') + ' °C';
      if (humEl) humEl.textContent = (d.hum != null ? d.hum.toFixed(1) : '--') + ' %';
      if (soilEl) soilEl.textContent = (d.soil != null ? d.soil.toFixed(1) : '--') + ' %';
      syncSharedClimate(d);
      ['light1', 'light2', 'light3', 'light4'].forEach(function (key) {
        document.querySelectorAll('[data-blynk-light-toggle="' + key + '"]').forEach(function (btn) {
          renderLightToggle(btn, d[key] === 1);
        });
      });
      renderPumpToggle(document.querySelector('[data-blynk-pump-toggle="pump"]'), d.pump === 1);
      setStatus(
        'Bơm: ' + (d.pump === 1 ? 'BẬT' : 'TẮT') +
        ' • Đèn 1: ' + (d.light1 === 1 ? 'BẬT' : 'TẮT') +
        ' • Đèn 2: ' + (d.light2 === 1 ? 'BẬT' : 'TẮT') +
        ' • Đèn 3: ' + (d.light3 === 1 ? 'BẬT' : 'TẮT') +
        ' • Đèn 4: ' + (d.light4 === 1 ? 'BẬT' : 'TẮT')
      );
    }).catch(function () {
      setStatus('Không có tín hiệu từ thiết bị trong vườn');
    });
  }

  function handlePumpToggle(pumpBtn, event) {
    if (!pumpBtn) return false;
    if (event) {
      event.preventDefault();
      event.stopPropagation();
    }
    var pumpDevice = pumpBtn.getAttribute('data-blynk-pump-toggle');
    var pumpCurrentState = pumpBtn.getAttribute('data-state') === '1';
    var pumpNextState = pumpCurrentState ? '0' : '1';
    setStatus('Đang gửi lệnh...');
    post('aitrongcay_blynk_control', { garden_key: AITR_GARDEN_KEY, device: pumpDevice, state: pumpNextState }).then(function (res) {
      if (!res || !res.success) throw new Error();
      renderPumpToggle(pumpBtn, pumpNextState === '1');
      refresh();
    }).catch(function () {
      setStatus('Gửi lệnh thất bại');
    });
    return false;
  }

  function handleLightToggle(lightBtn, event) {
    if (!lightBtn) return false;
    if (event) {
      event.preventDefault();
      event.stopPropagation();
    }
    var lightDevice = lightBtn.getAttribute('data-blynk-light-toggle');
    var lightCurrentState = lightBtn.getAttribute('data-state') === '1';
    var lightNextState = lightCurrentState ? '0' : '1';
    setStatus('Đang gửi lệnh...');
    post('aitrongcay_blynk_control', { garden_key: AITR_GARDEN_KEY, device: lightDevice, state: lightNextState }).then(function (res) {
      if (!res || !res.success) throw new Error();
      renderLightToggle(lightBtn, lightNextState === '1');
      refresh();
    }).catch(function () {
      setStatus('Gửi lệnh thất bại');
    });
    return false;
  }

  window.aitrLightToggle = handleLightToggle;

  document.addEventListener('click', function (event) {
    var pumpBtn = event.target.closest('[data-blynk-pump-toggle]');
    if (pumpBtn) {
      handlePumpToggle(pumpBtn, event);
      return;
    }

    var lightBtn = event.target.closest('[data-blynk-light-toggle]');
    if (lightBtn) {
      handleLightToggle(lightBtn, event);
    }
  });

  var refreshBtn = card.querySelector('[data-blynk-refresh]');
  if (refreshBtn) refreshBtn.addEventListener('click', refresh);

  refresh();
  setInterval(refresh, 15000);
})();

(function () {
  var switcher = document.querySelector('[data-garden-switcher]');
  if (switcher) {
    switcher.addEventListener('change', function () {
      var nextGarden = switcher.value || '';
      var url = new URL(window.location.href);
      if (nextGarden) {
        url.searchParams.set('garden', nextGarden);
      } else {
        url.searchParams.delete('garden');
      }
      window.location.href = url.toString();
    });
  }

  function post(action, extra) {
    var body = new URLSearchParams();
    body.set('action', action);
    body.set('nonce', AITR_AJAX_NONCE);
    Object.keys(extra || {}).forEach(function (k) { body.set(k, String(extra[k])); });
    return fetch(AITR_AJAX_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      body: body.toString(),
      credentials: 'same-origin'
    }).then(function (r) { return r.json(); });
  }

  var friendTarget = document.querySelector('[data-friend-target]');
  var friendMessage = document.querySelector('[data-friend-message]');
  var sendFriendBtn = document.querySelector('[data-send-friend-request]');
  if (sendFriendBtn && friendTarget) {
    function syncFriendRequestState() {
      var hasValue = !!(friendTarget.value || '').trim();
      sendFriendBtn.disabled = !hasValue;
      sendFriendBtn.setAttribute('aria-disabled', hasValue ? 'false' : 'true');
      if (friendMessage && !hasValue) friendMessage.textContent = 'Nhập email hoặc username để mở nút gửi lời mời.';
    }
    syncFriendRequestState();
    friendTarget.addEventListener('input', syncFriendRequestState);
    sendFriendBtn.addEventListener('click', function () {
      if (!((friendTarget.value || '').trim())) {
        if (friendMessage) friendMessage.textContent = 'Anh cần nhập email hoặc username trước khi gửi.';
        syncFriendRequestState();
        return;
      }
      if (friendMessage) friendMessage.textContent = 'Đang gửi lời mời kết bạn...';
      post('aitrongcay_send_friend_request', { target: friendTarget.value || '' }).then(function (res) {
        if (friendMessage) friendMessage.textContent = res && res.success ? 'Đã gửi lời mời. Khi người kia chấp nhận, họ sẽ xuất hiện ngay ở danh sách bạn bè.' : ((res && res.data && res.data.message) || 'Chưa gửi được lời mời. Anh thử lại giúp em.');
      }).catch(function () {
        if (friendMessage) friendMessage.textContent = 'Chưa gửi được lời mời. Anh thử lại giúp em.';
      });
    });
  }

  document.querySelectorAll('[data-send-friend-request-target]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var target = btn.getAttribute('data-send-friend-request-target') || '';
      if (!target) return;
      if (friendTarget) {
        friendTarget.value = target;
        if (typeof syncFriendRequestState === 'function') syncFriendRequestState();
      }
      btn.disabled = true;
      btn.setAttribute('aria-disabled', 'true');
      btn.textContent = 'Đang gửi...';
      if (friendMessage) friendMessage.textContent = 'Đang gửi lời mời kết bạn đến ' + target + '...';
      post('aitrongcay_send_friend_request', { target: target }).then(function (res) {
        if (res && res.success) {
          btn.textContent = 'Đã gửi lời mời';
          btn.classList.remove('btn-primary');
          btn.classList.add('btn-secondary');
          if (friendMessage) friendMessage.textContent = 'Đã gửi lời mời. Khi người kia chấp nhận, họ sẽ xuất hiện ngay ở danh sách bạn bè.';
          return;
        }
        btn.disabled = false;
        btn.setAttribute('aria-disabled', 'false');
        btn.textContent = 'Kết bạn';
        if (friendMessage) friendMessage.textContent = (res && res.data && res.data.message) || 'Chưa gửi được lời mời. Anh thử lại giúp em.';
      }).catch(function () {
        btn.disabled = false;
        btn.setAttribute('aria-disabled', 'false');
        btn.textContent = 'Kết bạn';
        if (friendMessage) friendMessage.textContent = 'Chưa gửi được lời mời. Anh thử lại giúp em.';
      });
    });
  });

  document.querySelectorAll('[data-accept-friendship]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      post('aitrongcay_accept_friend_request', { friendship_id: btn.getAttribute('data-accept-friendship') || '' }).then(function () {
        window.location.reload();
      }).catch(function () {});
    });
  });

  document.querySelectorAll('[data-reject-friendship]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      post('aitrongcay_reject_friend_request', { friendship_id: btn.getAttribute('data-reject-friendship') || '' }).then(function () {
        window.location.reload();
      }).catch(function () {});
    });
  });

  document.querySelectorAll('[data-accept-garden-invite]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      post('aitrongcay_accept_garden_invite', { membership_id: btn.getAttribute('data-accept-garden-invite') || '' }).then(function (res) {
        if (res && res.success && res.data && res.data.garden_key) {
          window.location.href = '/portal/dashboard/?garden=' + encodeURIComponent(res.data.garden_key);
          return;
        }
        window.location.reload();
      }).catch(function () {});
    });
  });

  document.querySelectorAll('[data-decline-garden-invite]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      post('aitrongcay_decline_garden_invite', { membership_id: btn.getAttribute('data-decline-garden-invite') || '' }).then(function () {
        window.location.reload();
      }).catch(function () {});
    });
  });

  var gardenFriend = document.querySelector('[data-garden-friend]');
  var gardenRole = document.querySelector('[data-garden-role]');
  var gardenMessage = document.querySelector('[data-garden-share-message]');
  var inviteBtn = document.querySelector('[data-invite-garden-member]');
  if (inviteBtn && gardenFriend && gardenRole) {
    function syncGardenInviteState() {
      var hasFriend = !!(gardenFriend.value || '');
      inviteBtn.disabled = !hasFriend;
      inviteBtn.setAttribute('aria-disabled', hasFriend ? 'false' : 'true');
      if (!hasFriend && gardenMessage) gardenMessage.textContent = 'Chọn một người bạn để mở nút gửi lời mời vào khu vườn.';
      if (hasFriend && gardenMessage) {
        var roleLabel = gardenRole.value === 'co_owner' ? 'Đồng sở hữu' : 'Chỉ xem';
        gardenMessage.textContent = 'Sẵn sàng gửi lời mời với vai trò: ' + roleLabel + '.';
      }
    }
    syncGardenInviteState();
    gardenFriend.addEventListener('change', syncGardenInviteState);
    gardenRole.addEventListener('change', syncGardenInviteState);
    inviteBtn.addEventListener('click', function () {
      if (!(gardenFriend.value || '')) {
        if (gardenMessage) gardenMessage.textContent = 'Anh cần chọn một người bạn trước khi gửi lời mời.';
        syncGardenInviteState();
        return;
      }
      if (gardenMessage) gardenMessage.textContent = 'Đang gửi lời mời vào khu vườn...';
      post('aitrongcay_invite_garden_member', {
        garden_key: AITR_GARDEN_KEY,
        user_id: gardenFriend.value || '',
        role: gardenRole.value || 'viewer'
      }).then(function (res) {
        if (gardenMessage) gardenMessage.textContent = res && res.success ? 'Đã gửi lời mời. Người được mời sẽ nằm ở mục Lời mời đang chờ cho đến khi họ phản hồi.' : ((res && res.data && res.data.message) || 'Chưa gửi được lời mời. Anh thử lại giúp em.');
        if (res && res.success) window.location.reload();
      }).catch(function () {
        if (gardenMessage) gardenMessage.textContent = 'Chưa gửi được lời mời. Anh thử lại giúp em.';
      });
    });
  }

  document.querySelectorAll('[data-update-garden-member]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var membershipId = btn.getAttribute('data-update-garden-member') || '';
      var select = document.querySelector('[data-member-role-select="' + membershipId + '"]');
      post('aitrongcay_update_garden_member_role', {
        membership_id: membershipId,
        role: select ? (select.value || 'viewer') : 'viewer'
      }).then(function () {
        window.location.reload();
      }).catch(function () {});
    });
  });

  document.querySelectorAll('[data-remove-garden-member]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      post('aitrongcay_remove_garden_member', { membership_id: btn.getAttribute('data-remove-garden-member') || '' }).then(function () {
        window.location.reload();
      }).catch(function () {});
    });
  });
})();

(function () {
  function post(action, extra) {
    var body = new URLSearchParams();
    body.set('action', action);
    body.set('nonce', AITR_AJAX_NONCE);
    Object.keys(extra || {}).forEach(function (k) { body.set(k, String(extra[k])); });
    return fetch(AITR_AJAX_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      body: body.toString(),
      credentials: 'same-origin'
    }).then(function (r) { return r.json(); });
  }

  var gardenWrap = document.querySelector('[data-garden-inline-name]');
  if (gardenWrap) {
    var gardenEditButton = gardenWrap.querySelector('[data-garden-inline-edit]');
    var gardenInput = gardenWrap.querySelector('[data-garden-inline-input]');
    var gardenStatus = gardenWrap.querySelector('[data-garden-inline-status]');
    var gardenText = gardenWrap.querySelector('.garden-inline-name-text');
    var gardenOriginalName = (gardenWrap.getAttribute('data-garden-name') || gardenText.textContent || gardenInput.value || 'Khu vườn của bạn').trim();
    var gardenIsEditing = false;
    var gardenIsSaving = false;

    if (gardenEditButton && gardenInput && gardenStatus && gardenText) {
      function setGardenStatus(message, className, show) {
        gardenStatus.textContent = message || '';
        gardenStatus.className = 'garden-inline-save-status';
        if (className) gardenStatus.classList.add(className);
        gardenStatus.hidden = !show;
      }

      function openGardenEditor() {
        if (gardenIsSaving) return;
        gardenIsEditing = true;
        gardenText.hidden = true;
        gardenInput.hidden = false;
        gardenEditButton.hidden = true;
        gardenInput.value = gardenOriginalName;
        setGardenStatus('Nhấn Enter hoặc click ra ngoài để lưu', '', true);
        window.requestAnimationFrame(function () {
          gardenInput.focus();
          gardenInput.select();
        });
      }

      function closeGardenEditor(keepStatus) {
        gardenIsEditing = false;
        gardenText.hidden = false;
        gardenInput.hidden = true;
        gardenEditButton.hidden = false;
        gardenInput.value = gardenOriginalName;
        if (!keepStatus) gardenStatus.hidden = true;
      }

      function applyGardenName(nextName) {
        gardenOriginalName = nextName;
        gardenWrap.setAttribute('data-garden-name', nextName);
        gardenText.textContent = nextName;
        document.querySelectorAll('[data-garden-display-name]').forEach(function (node) {
          node.textContent = nextName;
        });
      }

      function saveGardenName(nextName) {
        nextName = (nextName || '').trim().replace(/\s+/g, ' ');
        if (!nextName) {
          nextName = gardenOriginalName || 'Khu vườn của bạn';
        }
        if (nextName === gardenOriginalName) {
          closeGardenEditor();
          return;
        }
        if (gardenIsSaving) return;
        gardenIsSaving = true;
        setGardenStatus('Đang lưu tên mới...', 'is-saving', true);
        post('aitrongcay_rename_garden', {
          garden_key: AITR_GARDEN_KEY,
          garden_name: nextName
        }).then(function (res) {
          if (!res || !res.success || !res.data) throw new Error((res && res.data && res.data.message) || 'Lưu thất bại');
          applyGardenName(res.data.garden_name || nextName);
          setGardenStatus('Đã lưu tự động.', 'is-success', true);
          closeGardenEditor(true);
          window.setTimeout(function () {
            if (!gardenIsEditing && !gardenIsSaving) gardenStatus.hidden = true;
          }, 1600);
        }).catch(function (err) {
          setGardenStatus((err && err.message) || 'Chưa lưu được tên khu vườn.', 'is-error', true);
          window.requestAnimationFrame(function () {
            gardenInput.focus();
            gardenInput.select();
          });
        }).finally(function () {
          gardenIsSaving = false;
        });
      }

      gardenEditButton.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        openGardenEditor();
      });

      gardenInput.addEventListener('click', function (event) {
        event.stopPropagation();
      });

      gardenInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
          event.preventDefault();
          saveGardenName(gardenInput.value);
        } else if (event.key === 'Escape') {
          event.preventDefault();
          gardenInput.value = gardenOriginalName;
          closeGardenEditor();
        }
      });

      gardenInput.addEventListener('blur', function () {
        saveGardenName(gardenInput.value);
      });
    }
  }

  document.querySelectorAll('[data-pot-inline-name]').forEach(function (wrap) {
    var editButton = wrap.querySelector('[data-pot-inline-edit]');
    var input = wrap.querySelector('[data-pot-inline-input]');
    var status = wrap.querySelector('[data-pot-inline-status]');
    var text = wrap.querySelector('.pot-inline-name-text');
    if (!editButton || !input || !status || !text) return;

    var originalName = (wrap.getAttribute('data-pot-name') || '').trim();
    var potCode = wrap.getAttribute('data-pot-code') || '';
    var isSaving = false;
    var isEditing = false;

    function setStatus(textValue, cls, keepVisible) {
      status.textContent = textValue;
      status.classList.remove('is-saving', 'is-error', 'is-success');
      if (cls) status.classList.add(cls);
      status.hidden = !keepVisible && !textValue;
    }

    function closeEditor(keepStatus) {
      isEditing = false;
      input.hidden = true;
      text.hidden = false;
      editButton.hidden = false;
      if (!keepStatus) {
        status.hidden = true;
        setStatus('', '', false);
      }
    }

    function openEditor() {
      isEditing = true;
      input.hidden = false;
      text.hidden = true;
      editButton.hidden = true;
      input.value = originalName;
      setStatus('Nhấn Enter hoặc click ra ngoài để lưu', '', true);
      window.requestAnimationFrame(function () {
        input.focus();
        input.select();
      });
    }

    function applyName(nextName) {
      originalName = nextName;
      wrap.setAttribute('data-pot-name', nextName);
      text.textContent = nextName;
      var img = wrap.closest('.pot-row-summary');
      if (img) {
        var media = img.querySelector('.pot-row-media img');
        if (media) media.alt = nextName;
      }
    }

    function saveName(nextName) {
      nextName = (nextName || '').trim().replace(/\s+/g, ' ');
      if (!nextName) {
        setStatus('Tên khay không được để trống.', 'is-error', true);
        input.value = originalName;
        window.requestAnimationFrame(function () {
          input.focus();
          input.select();
        });
        return;
      }
      if (nextName === originalName) {
        closeEditor();
        return;
      }
      if (isSaving) return;
      isSaving = true;
      setStatus('Đang lưu tên mới...', 'is-saving', true);
      post('aitrongcay_rename_pot', {
        garden_key: AITR_GARDEN_KEY,
        pot_code: potCode,
        pot_name: nextName
      }).then(function (res) {
        if (!res || !res.success || !res.data) throw new Error((res && res.data && res.data.message) || 'Lưu thất bại');
        applyName(res.data.pot_name || nextName);
        setStatus('Đã lưu tự động.', 'is-success', true);
        closeEditor(true);
        window.setTimeout(function () {
          if (!isEditing && !isSaving) {
            status.hidden = true;
          }
        }, 1600);
      }).catch(function (err) {
        setStatus((err && err.message) || 'Chưa lưu được tên khay.', 'is-error', true);
        window.requestAnimationFrame(function () {
          input.focus();
          input.select();
        });
      }).finally(function () {
        isSaving = false;
      });
    }

    editButton.addEventListener('click', function (event) {
      event.preventDefault();
      event.stopPropagation();
      openEditor();
    });

    input.addEventListener('click', function (event) {
      event.stopPropagation();
    });

    input.addEventListener('keydown', function (event) {
      if (event.key === 'Enter') {
        event.preventDefault();
        saveName(input.value);
      } else if (event.key === 'Escape') {
        event.preventDefault();
        input.value = originalName;
        closeEditor();
      }
    });

    input.addEventListener('blur', function () {
      saveName(input.value);
    });
  });
})();

(function () {
  var popup = document.querySelector('[data-tool-popup]');
  if (!popup) return;

  var image = popup.querySelector('[data-tool-popup-image]');
  var name = popup.querySelector('[data-tool-popup-name]');
  var type = popup.querySelector('[data-tool-popup-type]');
  var desc = popup.querySelector('[data-tool-popup-desc]');

  function openPopup(data) {
    if (image) { image.src = data.image || ''; image.alt = data.name || ''; }
    if (name) name.textContent = data.name || '';
    if (type) type.textContent = data.type || '';
    if (desc) desc.textContent = data.desc || '';
    popup.hidden = false;
    document.body.style.overflow = 'hidden';
  }

  function closePopup() {
    popup.hidden = true;
    document.body.style.overflow = '';
  }

  document.querySelectorAll('[data-tool-popup-open]').forEach(function (el) {
    el.addEventListener('click', function (event) {
      event.preventDefault();
      openPopup({
        image: el.getAttribute('data-tool-image') || '',
        name: el.getAttribute('data-tool-name') || '',
        type: el.getAttribute('data-tool-type') || '',
        desc: el.getAttribute('data-tool-desc') || ''
      });
    });
  });

  popup.querySelectorAll('[data-tool-popup-close]').forEach(function (el) {
    el.addEventListener('click', closePopup);
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && !popup.hidden) closePopup();
  });
})();
</script>
