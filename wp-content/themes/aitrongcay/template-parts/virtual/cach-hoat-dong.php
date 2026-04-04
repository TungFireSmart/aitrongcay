<section class="section-hero">
    <div class="container grid-2" style="align-items:center">
        <div>
            <span class="eyebrow"><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'hero_eyebrow')); ?></span>
            <h1><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'hero_title')); ?></h1>
            <p class="lead"><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'hero_lead')); ?></p>
        </div>
        <div class="glass-card editorial-card">
            <div class="card-media">
                <img class="media-thumb" src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/story-morning.svg'); ?>" alt="Giới thiệu Ai trồng cây - mô hình vườn thuê số hóa cho gia đình" loading="lazy">
            </div>
            <div class="kicker"><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'visual_kicker')); ?></div>
            <h3><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'visual_title')); ?></h3>
            <p><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'visual_body')); ?></p>
        </div>
    </div>
</section>

<section class="section alt">
    <div class="container split-card">
        <div class="card">
            <span class="eyebrow"><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'difference_eyebrow')); ?></span>
            <h2><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'difference_title')); ?></h2>
            <p><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'difference_body')); ?></p>
        </div>
        <div class="card">
            <span class="eyebrow"><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'benefit_eyebrow')); ?></span>
            <ul class="check-list">
                <li><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'benefit_1')); ?></li>
                <li><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'benefit_2')); ?></li>
                <li><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'benefit_3')); ?></li>
                <li><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'benefit_4')); ?></li>
            </ul>
        </div>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width:980px">
        <div class="section-head">
            <span class="eyebrow"><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'story_eyebrow')); ?></span>
            <h2><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'story_title')); ?></h2>
        </div>
        <div class="entry-content" style="display:grid;gap:16px;font-size:1.05rem;line-height:1.8">
            <p><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'story_p1')); ?></p>
            <p><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'story_p2')); ?></p>
            <p><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'story_p3')); ?></p>
        </div>
    </div>
</section>

<section class="section dark-band">
    <div class="container grid-2">
        <div>
            <span class="eyebrow" style="background:rgba(255,255,255,.12);color:white"><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'cta_eyebrow')); ?></span>
            <h2><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'cta_title')); ?></h2>
            <p><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'cta_body')); ?></p>
        </div>
        <div class="portal-card">
            <div class="kpi-row">
                <div class="metric"><span class="subtle"><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'metric_1_label')); ?></span><strong><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'metric_1_value')); ?></strong></div>
                <div class="metric"><span class="subtle"><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'metric_2_label')); ?></span><strong><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'metric_2_value')); ?></strong></div>
                <div class="metric"><span class="subtle"><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'metric_3_label')); ?></span><strong><?php echo esc_html(aitrongcay_page_text('gioi_thieu', 'metric_3_value')); ?></strong></div>
            </div>
        </div>
    </div>
</section>

<?php get_template_part('template-parts/home/hero'); ?>
<?php get_template_part('template-parts/home/value-props'); ?>
<?php get_template_part('template-parts/home/journey'); ?>
<?php get_template_part('template-parts/home/portal'); ?>
<?php get_template_part('template-parts/home/packages'); ?>
<?php get_template_part('template-parts/home/trust'); ?>
<?php get_template_part('template-parts/home/faq'); ?>
<?php get_template_part('template-parts/home/cta'); ?>
