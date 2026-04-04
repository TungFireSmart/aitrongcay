<?php

declare(strict_types=1);

$nav_items = aitrongcay_primary_nav_items();
$current_user = wp_get_current_user();
$is_logged_in = is_user_logged_in();
?>
<header class="site-header">
    <div class="container nav-row">
        <a class="logo" href="<?php echo esc_url(home_url('/')); ?>">
            <span class="logo-badge">🌿</span>
            <span><?php echo esc_html(aitrongcay_company_profile()['brand']); ?></span>
        </a>

        <nav class="nav-menu" aria-label="<?php esc_attr_e('Main navigation', 'aitrongcay'); ?>">
            <?php foreach ($nav_items as $item) : ?>
                <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['label']); ?></a>
            <?php endforeach; ?>
        </nav>

        <div class="nav-actions">
            <?php if (! $is_logged_in) : ?>
                <a class="btn btn-primary" href="<?php echo esc_url(home_url('/portal/')); ?>">Xem trải nghiệm khu vườn</a>
            <?php endif; ?>
            <?php if ($is_logged_in) : ?>
                <details class="account-menu">
                    <summary class="small-link account-menu-toggle" aria-haspopup="menu">
                        <span class="account-menu-avatar"><?php echo esc_html(mb_strtoupper(mb_substr($current_user->display_name ?: $current_user->user_login, 0, 1))); ?></span>
                        <span>Xin chào, <?php echo esc_html($current_user->display_name ?: $current_user->user_login); ?></span>
                    </summary>
                    <div class="account-menu-panel">
                        <div class="account-menu-head">
                            <div class="account-menu-avatar large"><?php echo esc_html(mb_strtoupper(mb_substr($current_user->display_name ?: $current_user->user_login, 0, 1))); ?></div>
                            <div>
                                <strong style="display:block"><?php echo esc_html($current_user->display_name ?: $current_user->user_login); ?></strong>
                                <span class="subtle small"><?php echo esc_html($current_user->user_email); ?></span>
                            </div>
                        </div>
                        <div class="account-menu-links" role="menu">
                            <a class="account-menu-link" href="<?php echo esc_url(home_url('/portal/dashboard/')); ?>">Khu vườn của tôi</a>
                            <a class="account-menu-link" href="<?php echo esc_url(home_url('/cho-que/')); ?>">Chợ quê</a>
                            <a class="account-menu-link" href="<?php echo esc_url(home_url('/tai-khoan/')); ?>">Hồ sơ tài khoản</a>
                            <a class="account-menu-link" href="<?php echo esc_url(home_url('/tai-khoan/#doi-mat-khau')); ?>">Đổi mật khẩu</a>
                            <a class="account-menu-link danger" href="<?php echo esc_url(wp_logout_url(home_url('/dang-nhap/?auth_status=logged-out'))); ?>">Đăng xuất</a>
                        </div>
                    </div>
                </details>
            <?php else : ?>
                <a class="small-link" href="<?php echo esc_url(home_url('/dang-nhap/')); ?>">Đăng nhập</a>
            <?php endif; ?>
        </div>

        <button class="btn btn-secondary menu-toggle" data-mobile-toggle>Menu</button>
    </div>
    <div class="container" data-mobile-panel style="display:none"></div>
</header>
