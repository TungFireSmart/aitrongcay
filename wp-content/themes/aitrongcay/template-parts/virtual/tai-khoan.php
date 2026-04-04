<?php

declare(strict_types=1);

if (! is_user_logged_in()) {
    wp_safe_redirect(home_url('/dang-nhap/?auth_status=login-required'));
    exit;
}

$current_user = wp_get_current_user();
$phone = (string) get_user_meta($current_user->ID, 'aitrongcay_phone', true);
$city = (string) get_user_meta($current_user->ID, 'aitrongcay_city', true);
$household = (string) get_user_meta($current_user->ID, 'aitrongcay_household', true);
$address_line = (string) get_user_meta($current_user->ID, 'aitrongcay_address_line', true);
$ward = (string) get_user_meta($current_user->ID, 'aitrongcay_ward', true);
$district = (string) get_user_meta($current_user->ID, 'aitrongcay_district', true);
$account_note = (string) get_user_meta($current_user->ID, 'aitrongcay_account_note', true);
$notify_email = (string) get_user_meta($current_user->ID, 'aitrongcay_notify_email', true) !== '0';
$notify_sms = (string) get_user_meta($current_user->ID, 'aitrongcay_notify_sms', true) === '1';
$notify_harvest = (string) get_user_meta($current_user->ID, 'aitrongcay_notify_harvest', true) !== '0';
$member_since = get_date_from_gmt($current_user->user_registered, 'd/m/Y');
$avatar_id = (int) get_user_meta($current_user->ID, 'aitrongcay_avatar_id', true);
$avatar_url = $avatar_id ? (wp_get_attachment_image_url($avatar_id, 'medium') ?: wp_get_attachment_url($avatar_id)) : '';
$avatar_fallback = mb_strtoupper(mb_substr($current_user->display_name ?: $current_user->user_login, 0, 1));
?>
<section class="section-hero auth-hero">
  <div class="container" style="max-width:980px">
    <div class="card auth-card auth-card-wide">
      <?php aitrongcay_render_account_notice(); ?>

      <div class="portal-entry-card" style="margin-bottom:18px">
        <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap">
          <?php if ($avatar_url) : ?>
            <img src="<?php echo esc_url($avatar_url); ?>" alt="Avatar" style="width:72px;height:72px;border-radius:999px;object-fit:cover;border:2px solid rgba(31,107,69,.14)">
          <?php else : ?>
            <div style="width:72px;height:72px;border-radius:999px;display:grid;place-items:center;background:linear-gradient(135deg,var(--forest),var(--fresh));color:#fff;font-size:28px;font-weight:800"><?php echo esc_html($avatar_fallback); ?></div>
          <?php endif; ?>
          <div>
            <h1 style="margin:0 0 6px;font-size:2rem"><?php echo esc_html($current_user->display_name ?: $current_user->user_login); ?></h1>
            <div class="small subtle"><?php echo esc_html($current_user->user_email); ?></div>
            <div class="small subtle">Thành viên từ <?php echo esc_html($member_since); ?></div>
          </div>
        </div>
        <div style="display:grid;gap:10px;min-width:min(100%,260px)">
          <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data" style="display:grid;gap:10px">
            <input type="hidden" name="action" value="aitrongcay_account_avatar_update">
            <?php wp_nonce_field('aitrongcay_account_avatar_submit', 'aitrongcay_account_avatar_nonce'); ?>
            <input id="account-avatar" type="file" name="avatar" accept="image/*">
            <button class="btn btn-secondary" type="submit">Cập nhật ảnh</button>
          </form>
          <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="aitrongcay_account_avatar_remove">
            <?php wp_nonce_field('aitrongcay_account_avatar_remove_submit', 'aitrongcay_account_avatar_remove_nonce'); ?>
            <button class="btn btn-ghost" type="submit">Xóa ảnh</button>
          </form>
        </div>
      </div>

      <form class="auth-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="aitrongcay_account_update">
        <?php wp_nonce_field('aitrongcay_account_update_submit', 'aitrongcay_account_update_nonce'); ?>

        <details open class="account-accordion-card">
          <summary class="account-accordion-summary">Thông tin</summary>
          <div class="account-accordion-body form-grid">
            <div><label for="account-display-name">Tên hiển thị</label><input id="account-display-name" name="display_name" value="<?php echo esc_attr($current_user->display_name); ?>" autocomplete="name"></div>
            <div><label for="account-email">Email</label><input id="account-email" name="email" value="<?php echo esc_attr($current_user->user_email); ?>" autocomplete="email"></div>
            <div><label for="account-phone">Số điện thoại</label><input id="account-phone" name="phone" value="<?php echo esc_attr($phone); ?>" autocomplete="tel"></div>
            <div><label for="account-city">Thành phố</label><input id="account-city" name="city" value="<?php echo esc_attr($city); ?>"></div>
            <div><label for="account-household">Ghi chú ngắn</label><input id="account-household" name="household" value="<?php echo esc_attr($household); ?>"></div>
          </div>
        </details>

        <details class="account-accordion-card">
          <summary class="account-accordion-summary">Địa chỉ</summary>
          <div class="account-accordion-body form-grid">
            <div class="span-2"><label for="account-address">Địa chỉ</label><input id="account-address" name="address_line" value="<?php echo esc_attr($address_line); ?>"></div>
            <div><label for="account-ward">Phường / Xã</label><input id="account-ward" name="ward" value="<?php echo esc_attr($ward); ?>"></div>
            <div><label for="account-district">Quận / Huyện</label><input id="account-district" name="district" value="<?php echo esc_attr($district); ?>"></div>
          </div>
        </details>

        <details class="account-accordion-card">
          <summary class="account-accordion-summary">Thông báo</summary>
          <div class="account-accordion-body" style="display:grid;gap:12px">
            <label class="check-row"><input type="checkbox" name="notify_email" value="1" <?php checked($notify_email); ?>> <span>Email</span></label>
            <label class="check-row"><input type="checkbox" name="notify_sms" value="1" <?php checked($notify_sms); ?>> <span>SMS</span></label>
            <label class="check-row"><input type="checkbox" name="notify_harvest" value="1" <?php checked($notify_harvest); ?>> <span>Nhắc thu hoạch</span></label>
            <div><label for="account-note">Ghi chú</label><textarea id="account-note" name="account_note"><?php echo esc_textarea($account_note); ?></textarea></div>
          </div>
        </details>

        <div class="auth-actions" style="margin-top:16px">
          <button class="btn btn-primary" type="submit">Lưu</button>
          <a class="btn btn-secondary" href="<?php echo esc_url(home_url('/portal/dashboard/')); ?>">Khu vườn của tôi</a>
        </div>
      </form>

      <details id="doi-mat-khau" class="account-accordion-card" open style="margin-top:22px">
        <summary class="account-accordion-summary">Đổi mật khẩu</summary>
        <form class="auth-form account-accordion-body" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
          <input type="hidden" name="action" value="aitrongcay_account_password_update">
          <?php wp_nonce_field('aitrongcay_account_password_submit', 'aitrongcay_account_password_nonce'); ?>
          <div class="form-grid">
            <div><label for="account-new-password">Mật khẩu mới</label><input id="account-new-password" name="new_password" type="password" autocomplete="new-password"></div>
            <div><label for="account-confirm-password">Xác nhận mật khẩu mới</label><input id="account-confirm-password" name="confirm_password" type="password" autocomplete="new-password"></div>
          </div>
          <div class="auth-actions" style="margin-top:16px">
            <button class="btn btn-primary" type="submit">Cập nhật mật khẩu</button>
            <a class="btn btn-secondary" href="<?php echo esc_url(wp_logout_url(home_url('/dang-nhap/?auth_status=logged-out'))); ?>">Đăng xuất</a>
          </div>
        </form>
      </details>
    </div>
  </div>
</section>
