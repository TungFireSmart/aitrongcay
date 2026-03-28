<?php

declare(strict_types=1);

$nav_items = aitrongcay_primary_nav_items();
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
            <a class="btn btn-secondary" href="<?php echo esc_url(home_url('/dang-ky-tu-van/')); ?>">Nhận tư vấn</a>
            <a class="btn btn-primary" href="<?php echo esc_url(home_url('/portal/')); ?>">Mở khu vườn của bạn</a>
            <a class="small-link" href="<?php echo esc_url(home_url('/dang-nhap/')); ?>">Đăng nhập</a>
        </div>

        <button class="btn btn-secondary menu-toggle" data-mobile-toggle>Menu</button>
    </div>
    <div class="container" data-mobile-panel style="display:none"></div>
</header>
