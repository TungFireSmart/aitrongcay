<?php

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$market_post_id = absint($_GET['market_post'] ?? 0);
$market_post = $market_post_id ? get_post($market_post_id) : null;
$created_post = absint($_GET['created_post'] ?? 0);
$market_context_garden_key = aitrongcay_market_context_garden_key();
$market_view_garden_key = '';
if (isset($_GET['garden'])) {
    $requested_market_garden = sanitize_text_field((string) wp_unslash($_GET['garden']));
    if ($requested_market_garden !== '' && is_user_logged_in() && aitrongcay_user_can_view_garden($requested_market_garden, get_current_user_id())) {
        $market_view_garden_key = $requested_market_garden;
    }
}
$market_context_profile = $market_view_garden_key !== '' ? aitrongcay_portal_profile_for_garden_context($market_view_garden_key, wp_get_current_user()) : null;
$market_list_url = $market_view_garden_key !== '' ? add_query_arg('garden', $market_view_garden_key, home_url('/cho-que/')) : home_url('/cho-que/');

if ($market_post && $market_post->post_type === 'aitr_market_post') {
    $gallery = array_map('absint', (array) get_post_meta($market_post->ID, '_aitrongcay_market_gallery', true));
    $market_structured = function_exists('aitrongcay_get_market_structured_data') ? aitrongcay_get_market_structured_data($market_post->ID) : [];
    $market_summary_line = function_exists('aitrongcay_market_summary_line') ? aitrongcay_market_summary_line($market_structured) : '';
    $author_id = (int) $market_post->post_author;
    $author_name = get_the_author_meta('display_name', $author_id);
    $author_email = get_the_author_meta('user_email', $author_id);
    $detail_likes = array_map('intval', (array) get_post_meta($market_post->ID, '_aitrongcay_market_likes', true));
    $detail_liked = is_user_logged_in() && in_array(get_current_user_id(), $detail_likes, true);
    $market_comments = get_comments(['post_id' => $market_post->ID, 'status' => 'approve']);
    ?>
    <section class="section-tight market-page-shell">
        <article class="glass-card market-detail-card market-detail-clean">
            <div class="market-detail-head">
                <div>
                    <span class="eyebrow"><?php echo esc_html(aitrongcay_page_text('cho_que', 'detail_eyebrow')); ?></span>
                    <h1><?php echo esc_html(get_the_title($market_post)); ?></h1>
                    <div class="market-meta-line">
                        <span><?php echo esc_html($author_name); ?></span>
                        <span><?php echo esc_html(get_the_date('d/m/Y H:i', $market_post)); ?></span>
                    </div>
                    <?php if ($market_summary_line !== '') : ?>
                        <div class="market-card-meta" style="margin-top:10px"><span><?php echo esc_html($market_summary_line); ?></span></div>
                    <?php endif; ?>
                </div>
                <div class="inline-list">
                    <a class="btn btn-primary" href="<?php echo esc_url(aitrongcay_market_zalo_action_url((int) $market_post->ID)); ?>" style="display:inline-flex;align-items:center;gap:8px"><span class="market-social-icon" aria-hidden="true"><svg viewBox="0 0 24 24" width="16" height="16" fill="none"><rect x="3" y="3" width="18" height="18" rx="6" fill="#0068FF"></rect><path d="M7.5 8.2h9l-6.05 7.6h5.95l-9 0 6.05-7.6H7.5Z" fill="#fff"></path></svg></span><span><?php echo esc_html(aitrongcay_page_text('cho_que', 'detail_contact_label')); ?></span></a>
                    <a class="btn btn-secondary" href="<?php echo esc_url($market_list_url); ?>"><?php echo esc_html(aitrongcay_page_text('cho_que', 'detail_back_label')); ?></a>
                    <?php if (is_user_logged_in()) : ?>
                        <button class="btn btn-secondary" type="button" data-like-market-post="<?php echo esc_attr((string) $market_post->ID); ?>"><span data-like-label><?php echo esc_html($detail_liked ? aitrongcay_page_text('cho_que', 'listing_liked_label') : aitrongcay_page_text('cho_que', 'listing_like_label')); ?></span> · <span data-like-count><?php echo esc_html((string) count($detail_likes)); ?></span></button>
                    <?php endif; ?>
                </div>
            </div>

            <?php
            $detail_thumb_url = has_post_thumbnail($market_post) ? (string) get_the_post_thumbnail_url($market_post, 'full') : '';
            $detail_gallery_url = ($gallery && ! empty($gallery[0])) ? (string) wp_get_attachment_image_url($gallery[0], 'large') : '';
            $detail_image_url = $detail_thumb_url ?: $detail_gallery_url;
            $market_fallback_url = get_template_directory_uri() . '/assets/images/market-harvest.svg';
            ?>
            <div class="card-media media-frame media-frame-16x9" style="margin-top:16px">
                <img class="media-thumb media-fit-cover" src="<?php echo esc_url($detail_image_url ? set_url_scheme($detail_image_url, 'https') : $market_fallback_url); ?>" alt="<?php echo esc_attr(get_the_title($market_post)); ?>" loading="lazy" onerror="this.onerror=null;this.src='<?php echo esc_url($market_fallback_url); ?>';this.alt='<?php echo esc_attr(get_the_title($market_post)); ?>';">
            </div>

            <?php if ($gallery) : ?>
                <div class="cards-4 market-gallery" style="margin-top:18px">
                    <?php foreach ($gallery as $gallery_id) : ?>
                        <?php $gallery_item_url = (string) wp_get_attachment_image_url($gallery_id, 'large'); ?>
                        <img class="media-thumb media-fit-cover" src="<?php echo esc_url($gallery_item_url ? set_url_scheme($gallery_item_url, 'https') : $market_fallback_url); ?>" alt="<?php echo esc_attr(get_the_title($market_post)); ?>" loading="lazy" onerror="this.onerror=null;this.src='<?php echo esc_url($market_fallback_url); ?>';this.alt='<?php echo esc_attr(get_the_title($market_post)); ?>';">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="entry-content market-copy-clean" style="margin-top:20px">
                <?php echo wpautop(wp_kses_post($market_post->post_content)); ?>
            </div>

            <div class="market-comments-block" style="margin-top:28px">
                <h3 style="margin-bottom:14px"><?php echo esc_html(aitrongcay_page_text('cho_que', 'detail_comments_title')); ?></h3>
                <?php if ($market_comments) : ?>
                    <div class="comment-list">
                        <?php foreach ($market_comments as $comment_item) : ?>
                            <div class="comment">
                                <strong><?php echo esc_html($comment_item->comment_author); ?></strong>
                                <div class="small subtle"><?php echo esc_html(get_date_from_gmt($comment_item->comment_date_gmt, 'd/m/Y H:i')); ?></div>
                                <p style="margin-top:8px"><?php echo esc_html($comment_item->comment_content); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (is_user_logged_in()) : ?>
                    <?php comment_form(['title_reply' => aitrongcay_page_text('cho_que', 'detail_comment_form_title'), 'label_submit' => aitrongcay_page_text('cho_que', 'detail_comment_submit_label')], $market_post->ID); ?>
                <?php else : ?>
                    <div class="notice"><?php echo esc_html(aitrongcay_page_text('cho_que', 'detail_comment_login_notice')); ?></div>
                <?php endif; ?>
            </div>
        </article>
    </section>
    <?php
    return;
}

$market_posts = new WP_Query(aitrongcay_market_posts_query_args($market_view_garden_key, 24));
$active_market_category = sanitize_text_field((string) ($_GET['market_category'] ?? ''));
$active_market_offer_type = sanitize_text_field((string) ($_GET['market_offer_type'] ?? ''));
$active_market_sort = sanitize_key((string) ($_GET['market_sort'] ?? 'newest'));
?>
<?php $market_categories = ['Hạt giống', 'Cây giống', 'Dinh dưỡng cho cây', 'Các loại rau', 'Hoa']; $market_offer_types = ['Bán', 'Trao đổi', 'Chia sẻ', 'Nhận đặt trước']; ?>
<section class="section-tight">
    <div class="container market-page-shell" id="market-drafts">
    <div class="market-board-head">
        <div>
            <span class="eyebrow"><?php echo esc_html(aitrongcay_page_text('cho_que', 'listing_eyebrow')); ?></span>
            <h2><?php echo esc_html(aitrongcay_page_text('cho_que', 'listing_title')); ?></h2>
        </div>
        <div class="inline-list">
            <?php if (is_user_logged_in()) : ?>
                <a class="btn btn-primary" href="<?php echo esc_url(home_url('/portal/dashboard/#photo-library')); ?>"><?php echo esc_html(aitrongcay_page_text('cho_que', 'listing_primary_cta')); ?></a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($created_post) : ?>
        <div class="notice success" style="margin:12px 0 20px"><strong><?php echo esc_html(aitrongcay_page_text('cho_que', 'listing_created_notice')); ?></strong></div>
    <?php endif; ?>

    <div class="market-compose-modal" data-market-edit-modal hidden>
        <div class="market-compose-backdrop" data-close-market-edit></div>
        <div class="market-compose-dialog market-edit-dialog" role="dialog" aria-modal="true" aria-labelledby="market-edit-title">
            <button class="market-compose-close" type="button" aria-label="Đóng" data-close-market-edit>×</button>
            <div class="market-compose-head market-compose-head-ai">
                <div>
                    <span class="eyebrow">Sửa tin đăng</span>
                    <h3 id="market-edit-title">Chỉnh sửa tin Chợ quê</h3>
                </div>
            </div>
            <form class="market-compose-form" data-market-edit-form>
                <input type="hidden" data-market-edit-post-id>
                <div class="market-compose-surface">
                    <div class="market-compose-title-wrap">
                        <input id="market-edit-title-input" type="text" maxlength="140" placeholder="Tiêu đề" aria-label="Tiêu đề" data-market-edit-title>
                        <div class="small subtle" data-market-title-hint></div>
                    </div>
                    <div class="market-structured-grid">
                        <div><select id="market-edit-category" aria-label="Danh mục" data-market-edit-category><option value="">Danh mục</option><option>Hạt giống</option><option>Cây giống</option><option>Dinh dưỡng cho cây</option><option>Các loại rau</option><option>Hoa</option></select></div>
                        <div><select id="market-edit-offer-type" aria-label="Hình thức" data-market-edit-offer-type><option value="">Hình thức</option><option>Bán</option><option>Trao đổi</option><option>Chia sẻ</option><option>Nhận đặt trước</option></select></div>
                        <div><input id="market-edit-quantity" type="text" placeholder="Số lượng" aria-label="Số lượng" data-market-edit-quantity></div>
                        <div><input id="market-edit-area" type="text" placeholder="Khu vực" aria-label="Khu vực" data-market-edit-area></div>
                        <div><input id="market-edit-availability" type="text" placeholder="Thời gian nhận/giao" aria-label="Thời gian nhận hoặc giao" data-market-edit-availability></div>
                        <div><input id="market-edit-contact" type="text" placeholder="Liên hệ" aria-label="Liên hệ" data-market-edit-contact></div>
                    </div>
                    <div class="market-compose-body-wrap">
                        <textarea id="market-edit-content-input" placeholder="Nội dung" aria-label="Nội dung" data-market-edit-content></textarea>
                        <div class="small subtle" data-market-content-hint></div>
                    </div>
                    <div class="market-compose-toolbar">
                        <label class="market-compose-upload" for="market-edit-photo-input">
                            <span>＋</span>
                            <strong>Thay ảnh</strong>
                        </label>
                        <input id="market-edit-photo-input" type="file" accept="image/*" multiple data-market-edit-files>
                    </div>
                </div>
                <div class="market-edit-current-media" data-market-edit-current-media></div>
                <div class="market-compose-preview" data-market-edit-preview></div>
                <div class="market-edit-card-preview" data-market-edit-card-preview></div>
                <div class="market-compose-foot">
                    <div class="notice" data-market-edit-notice style="display:none;margin:0"></div>
                    <div class="inline-list market-compose-actions" style="justify-content:flex-end">
                        <button class="btn btn-ghost" type="button" data-close-market-edit>Đóng</button>
                        <button class="btn btn-primary" type="submit" data-market-edit-submit>Lưu thay đổi</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="market-layout-clean">
        <aside class="market-sidebar-clean">
            <div class="card soft-card market-filter-card" style="padding:18px">
                <div class="kicker" style="margin-bottom:8px">Bộ lọc</div>
                <form method="get" class="market-filter-form" style="display:grid;gap:10px">
                    <select name="market_category">
                        <option value="">Tất cả danh mục</option>
                        <?php foreach ($market_categories as $category_label) : ?>
                            <option value="<?php echo esc_attr($category_label); ?>" <?php selected($active_market_category, $category_label); ?>><?php echo esc_html($category_label); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="market_offer_type">
                        <option value="">Tất cả hình thức</option>
                        <?php foreach ($market_offer_types as $offer_label) : ?>
                            <option value="<?php echo esc_attr($offer_label); ?>" <?php selected($active_market_offer_type, $offer_label); ?>><?php echo esc_html($offer_label); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="market_sort">
                        <option value="newest" <?php selected($active_market_sort, 'newest'); ?>>Mới nhất</option>
                        <option value="popular" <?php selected($active_market_sort, 'popular'); ?>>Nhiều chia sẻ</option>
                    </select>
                    <div class="inline-list" style="gap:8px;justify-content:flex-start">
                        <button class="btn btn-secondary" type="submit">Lọc</button>
                        <a class="btn btn-ghost" href="<?php echo esc_url($market_list_url); ?>">Xóa lọc</a>
                    </div>
                </form>
            </div>
        </aside>
        <div class="market-list-clean">
        <div class="market-results-bar">
            <div class="small subtle">Có <strong><?php echo esc_html((string) $market_posts->found_posts); ?></strong> kết quả<?php echo $active_market_category !== '' || $active_market_offer_type !== '' ? ' sau khi lọc' : ''; ?>.</div>
        </div>
        <?php if ($market_posts->have_posts()) : ?>
            <?php while ($market_posts->have_posts()) : $market_posts->the_post(); ?>
                <?php
                $post_garden_key = aitrongcay_migrate_market_post_garden_key(get_the_ID());
                $post_garden_profile = $post_garden_key !== '' ? aitrongcay_portal_profile_for_garden_context($post_garden_key, wp_get_current_user()) : null;
                $gallery = array_map('absint', (array) get_post_meta(get_the_ID(), '_aitrongcay_market_gallery', true));
                $market_structured = function_exists('aitrongcay_get_market_structured_data') ? aitrongcay_get_market_structured_data(get_the_ID()) : [];
                $market_summary_line = function_exists('aitrongcay_market_summary_line') ? aitrongcay_market_summary_line($market_structured) : '';
                $author_id = (int) get_post_field('post_author', get_the_ID());
                $author_name = get_the_author_meta('display_name', $author_id);
                $author_email = get_the_author_meta('user_email', $author_id);
                $likes = array_map('intval', (array) get_post_meta(get_the_ID(), '_aitrongcay_market_likes', true));
                $liked = is_user_logged_in() && in_array(get_current_user_id(), $likes, true);
                $share_count = (int) get_post_meta(get_the_ID(), '_aitrongcay_market_share_count', true);
                $thumb_url = has_post_thumbnail() ? (string) get_the_post_thumbnail_url(get_the_ID(), 'large') : '';
                $gallery_url = ($gallery && ! empty($gallery[0])) ? (string) wp_get_attachment_image_url($gallery[0], 'large') : '';
                $market_image_url = $thumb_url ?: $gallery_url;
                $market_fallback_url = get_template_directory_uri() . '/assets/images/market-harvest.svg';
                $market_gallery_payload = [];
                foreach ($gallery as $attachment_id) {
                    $attachment_url = (string) (wp_get_attachment_image_url($attachment_id, 'large') ?: wp_get_attachment_url($attachment_id));
                    if (! $attachment_url) {
                        continue;
                    }
                    $market_gallery_payload[] = [
                        'id' => $attachment_id,
                        'url' => set_url_scheme($attachment_url, 'https'),
                        'title' => get_the_title($attachment_id),
                    ];
                }
                ?>
                <article class="market-row-card<?php echo $created_post === get_the_ID() ? ' created-market-post' : ''; ?>" data-market-card>
                    <div class="market-row-media media-frame media-frame-16x9">
                        <?php if ($market_image_url) : ?>
                            <img class="media-thumb media-fit-cover" src="<?php echo esc_url(set_url_scheme($market_image_url, 'https')); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" onerror="this.onerror=null;this.src='<?php echo esc_url($market_fallback_url); ?>';this.alt='<?php echo esc_attr(get_the_title()); ?>';">
                        <?php else : ?>
                            <img class="media-thumb media-fit-cover" src="<?php echo esc_url($market_fallback_url); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
                        <?php endif; ?>
                    </div>
                    <div class="market-row-text">
                        <div class="market-card-topline">
                            <span class="kicker"><?php echo esc_html($post_garden_key !== '' ? (string) ($post_garden_profile['garden_name'] ?? $post_garden_key) : $author_name); ?></span>
                            <span class="small subtle"><?php echo esc_html(get_the_date('d/m/Y', get_the_ID())); ?></span>
                        </div>
                        <h3 data-market-title-render><?php the_title(); ?></h3>
                        <?php if ($market_summary_line !== '') : ?>
                            <div class="market-card-meta">
                                <span><?php echo esc_html($market_summary_line); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="market-card-meta">
                            <span><?php echo esc_html(!empty($market_structured['contact_text']) ? $market_structured['contact_text'] : 'Nhắn Zalo'); ?></span>
                        </div>
                        <div class="entry-content market-copy-clean" data-market-content-render><?php echo wpautop(esc_html(wp_trim_words(wp_strip_all_tags(get_the_content()), 30, '...'))); ?></div>
                        <div class="market-card-actions-row single-line">
                            <div class="market-card-social single-line">
                                <a class="market-social-btn market-owner-pill" href="<?php echo esc_url(aitrongcay_market_zalo_action_url((int) get_the_ID())); ?>" aria-label="Nhắn Zalo"><span class="market-social-icon" aria-hidden="true"><svg viewBox="0 0 24 24" width="16" height="16" fill="none"><rect x="3" y="3" width="18" height="18" rx="6" fill="#0068FF"></rect><path d="M7.5 8.2h9l-6.05 7.6h5.95l-9 0 6.05-7.6H7.5Z" fill="#fff"></path></svg></span><span>Zalo</span></a>
                                <a class="market-icon-btn" href="<?php echo esc_url(add_query_arg(array_filter(['market_post' => (string) get_the_ID(), 'garden' => $market_view_garden_key]), home_url('/cho-que/'))); ?>" aria-label="Xem chi tiết tin"><span class="market-social-icon" aria-hidden="true"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></span></a>
                                <?php if (is_user_logged_in()) : ?>
                                    <button class="market-social-btn<?php echo $liked ? ' active' : ''; ?>" type="button" data-like-market-post="<?php echo esc_attr((string) get_the_ID()); ?>" aria-label="Yêu thích tin đăng"><span class="market-social-icon" aria-hidden="true"><svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M12 21s-6.7-4.35-9.33-8.07C.54 9.93 2.2 5.5 6.54 5.5c2.11 0 3.52 1.17 4.3 2.33.78-1.16 2.2-2.33 4.31-2.33 4.33 0 6 4.43 3.87 7.43C18.7 16.65 12 21 12 21Z"/></svg></span><span data-like-count><?php echo esc_html((string) count($likes)); ?></span></button>
                                <?php endif; ?>
                                <button class="market-social-btn" type="button" data-share-market-post="<?php echo esc_attr((string) get_the_ID()); ?>" aria-label="Chia sẻ tin đăng"><span class="market-social-icon" aria-hidden="true"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17 17 7"/><path d="M8 7h9v9"/></svg></span><span data-share-count><?php echo esc_html((string) $share_count); ?></span></button>
                                <?php if ((int) get_post_field('post_author', get_the_ID()) === get_current_user_id()) : ?>
                                    <button class="market-social-btn market-owner-pill" type="button" data-edit-market-post="<?php echo esc_attr((string) get_the_ID()); ?>" data-market-title="<?php echo esc_attr(get_the_title()); ?>" data-market-content="<?php echo esc_attr(wp_strip_all_tags(get_the_content())); ?>" data-market-image="<?php echo esc_url($market_image_url ? set_url_scheme($market_image_url, 'https') : $market_fallback_url); ?>" data-market-gallery="<?php echo esc_attr(wp_json_encode($market_gallery_payload)); ?>" data-market-category="<?php echo esc_attr((string) ($market_structured['category'] ?? '')); ?>" data-market-offer-type="<?php echo esc_attr((string) ($market_structured['offer_type'] ?? '')); ?>" data-market-quantity="<?php echo esc_attr((string) ($market_structured['quantity'] ?? '')); ?>" data-market-area="<?php echo esc_attr((string) ($market_structured['area'] ?? '')); ?>" data-market-availability="<?php echo esc_attr((string) ($market_structured['availability'] ?? '')); ?>" data-market-contact="<?php echo esc_attr((string) ($market_structured['contact_text'] ?? '')); ?>"><span class="market-social-icon" aria-hidden="true"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4Z"/></svg></span><span><?php echo esc_html(aitrongcay_page_text('cho_que', 'listing_edit_label')); ?></span></button>
                                    <button class="market-social-btn market-owner-pill market-owner-pill-danger" type="button" data-delete-market-post="<?php echo esc_attr((string) get_the_ID()); ?>"><span class="market-social-icon" aria-hidden="true"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg></span><span><?php echo esc_html(aitrongcay_page_text('cho_que', 'listing_delete_label')); ?></span></button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endwhile; wp_reset_postdata(); ?>
        <?php else : ?>
            <div class="notice"><?php echo esc_html($market_view_garden_key !== '' ? 'Khu vườn này chưa có tin Chợ quê nào. Anh/chị có thể đăng từ portal của đúng vườn để giữ context đồng nhất.' : aitrongcay_page_text('cho_que', 'empty_notice')); ?></div>
        <?php endif; ?>
        </div>
    </div>
</section>
