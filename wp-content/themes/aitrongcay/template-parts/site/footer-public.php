<?php

declare(strict_types=1);

$company = aitrongcay_company_profile();
$footer_groups = aitrongcay_footer_groups();
?>
<footer class="footer">
    <div class="container footer-top">
        <div class="footer-card">
            <h3><?php echo esc_html($company['company']); ?></h3>
            <p>Địa chỉ: <?php echo esc_html($company['address']); ?></p>
            <p>Điện thoại: <?php echo esc_html($company['phone']); ?></p>
        </div>
        <div class="footer-card">
            <h3><?php echo esc_html($company['brand']); ?></h3>
            <p><?php echo esc_html($company['description']); ?></p>
        </div>
    </div>

    <div class="container footer-grid">
        <div>
            <div class="logo" style="margin-bottom:12px"><span class="logo-badge">🌿</span><span><?php echo esc_html($company['brand']); ?></span></div>
            <p><?php echo esc_html($company['tagline']); ?></p>
        </div>
        <?php foreach ($footer_groups as $group) : ?>
            <div>
                <h3><?php echo esc_html($group['title']); ?></h3>
                <p>
                    <?php foreach ($group['items'] as $item) : ?>
                        <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['label']); ?></a><br>
                    <?php endforeach; ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="container footer-meta">
        <span>© <?php echo esc_html((string) gmdate('Y')); ?> <?php echo esc_html($company['brand']); ?></span>
        <span>Theme WordPress foundation đã sẵn sàng để thay dần các trang HTML tĩnh bằng template và page content thật.</span>
    </div>
</footer>
