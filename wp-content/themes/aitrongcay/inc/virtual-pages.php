<?php

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

function aitrongcay_virtual_pages(): array
{
    return [
        'cach-hoat-dong' => ['title' => 'Giới thiệu', 'template' => 'template-parts/virtual/cach-hoat-dong.php'],
        'cho-que' => ['title' => 'Chợ quê', 'template' => 'template-parts/virtual/cho-que.php'],
        'an-toan-thuc-pham' => ['title' => 'An toàn thực phẩm', 'template' => 'template-parts/virtual/an-toan-thuc-pham.php'],
        'chuyen-nha-nong' => ['title' => 'Chuyện nhà nông', 'template' => 'template-parts/virtual/chuyen-nha-nong.php'],
        'faq' => ['title' => 'FAQ', 'template' => 'template-parts/virtual/faq.php'],
        'dang-ky-tu-van' => ['title' => 'Đăng ký tư vấn', 'template' => 'template-parts/virtual/dang-ky-tu-van.php'],
        'onboarding' => ['title' => 'Onboarding', 'template' => 'template-parts/virtual/onboarding.php'],
        'dang-nhap' => ['title' => 'Đăng nhập', 'template' => 'template-parts/virtual/dang-nhap.php'],
        'tai-khoan' => ['title' => 'Tài khoản', 'template' => 'template-parts/virtual/tai-khoan.php'],
        'portal' => ['title' => 'Portal', 'template' => 'template-parts/virtual/portal.php'],
        'portal/dashboard' => ['title' => 'Tổng quan khu vườn', 'template' => 'template-parts/virtual/portal.php'],
        'portal/webcam' => ['title' => 'Live webcam 24/7', 'template' => 'template-parts/virtual/portal.php'],
        'portal/tinh-trang-vuon' => ['title' => 'Tình trạng khu vườn', 'template' => 'template-parts/virtual/portal.php'],
        'portal/nhat-ky-cham-soc' => ['title' => 'Nhật ký chăm sóc', 'template' => 'template-parts/virtual/portal.php'],
        'portal/chat-luong-an-toan' => ['title' => 'Hồ sơ chất lượng', 'template' => 'template-parts/virtual/portal.php'],
        'portal/tro-ly-ai' => ['title' => 'AI gardener', 'template' => 'template-parts/virtual/portal.php'],
        'portal/kho-nong-cu' => ['title' => 'Kho nông cụ', 'template' => 'template-parts/virtual/portal.php'],
        'portal/ban-be' => ['title' => 'Bạn bè', 'template' => 'template-parts/virtual/portal.php'],
        'portal/chia-se-khu-vuon' => ['title' => 'Chia sẻ khu vườn', 'template' => 'template-parts/virtual/portal.php'],
    ];
}

function aitrongcay_portal_nav_items(): array
{
    return [
        ['slug' => 'portal/kho-nong-cu', 'label' => 'Kho nông cụ'],
        ['slug' => 'portal/tro-ly-ai', 'label' => 'Trợ lý AI'],
        ['slug' => 'portal/ban-be', 'label' => 'Bạn bè'],
        ['slug' => 'portal/chia-se-khu-vuon', 'label' => 'Chia sẻ khu vườn'],
    ];
}

function aitrongcay_register_virtual_page_rules(): void
{
    add_rewrite_tag('%aitrongcay_page%', '(.+)');

    foreach (array_keys(aitrongcay_virtual_pages()) as $slug) {
        add_rewrite_rule('^' . preg_quote($slug, '#') . '/?$', 'index.php?aitrongcay_page=' . $slug, 'top');
    }
}
add_action('init', 'aitrongcay_register_virtual_page_rules');

function aitrongcay_maybe_flush_virtual_rules(): void
{
    $version = (string) wp_get_theme()->get('Version');
    $option_key = 'aitrongcay_virtual_rules_version';
    if (get_option($option_key) === $version) {
        return;
    }

    aitrongcay_register_virtual_page_rules();
    flush_rewrite_rules(false);
    update_option($option_key, $version, false);
}
add_action('init', 'aitrongcay_maybe_flush_virtual_rules', 20);

function aitrongcay_force_flush_virtual_rules_once(): void
{
    $token = isset($_GET['aitr_flush_routes']) ? (string) $_GET['aitr_flush_routes'] : '';
    if ($token !== '20260403-banbe-share') {
        return;
    }

    aitrongcay_register_virtual_page_rules();
    flush_rewrite_rules(false);
    update_option('aitrongcay_virtual_rules_version', (string) wp_get_theme()->get('Version'), false);
}
add_action('init', 'aitrongcay_force_flush_virtual_rules_once', 99);

function aitrongcay_current_virtual_page(): ?array
{
    $slug = get_query_var('aitrongcay_page');
    $pages = aitrongcay_virtual_pages();

    if (! is_string($slug) || $slug === '' || ! isset($pages[$slug])) {
        $request_uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
        if ($request_uri !== '') {
            $path = (string) wp_parse_url($request_uri, PHP_URL_PATH);
            $home_path = (string) wp_parse_url(home_url('/'), PHP_URL_PATH);
            if ($home_path !== '' && $home_path !== '/' && str_starts_with($path, rtrim($home_path, '/'))) {
                $path = substr($path, strlen(rtrim($home_path, '/')));
            }
            $path = trim($path, '/');
            if ($path !== '' && isset($pages[$path])) {
                $slug = $path;
            }
        }
    }

    if (! is_string($slug) || $slug === '' || ! isset($pages[$slug])) {
        return null;
    }

    $native_page = get_page_by_path($slug);
    if ($native_page instanceof WP_Post) {
        return null;
    }

    return $pages[$slug] + ['slug' => $slug];
}

function aitrongcay_template_include_virtual_page(string $template): string
{
    $page = aitrongcay_current_virtual_page();
    if ($page) {
        status_header(200);
        global $wp_query;
        if ($wp_query instanceof WP_Query) {
            $wp_query->is_404 = false;
        }
        return get_template_directory() . '/templates/virtual-page.php';
    }

    if (is_page()) {
        $native_slug = get_post_field('post_name', get_queried_object_id());
        $native_overrides = [
            'cach-hoat-dong' => 'template-parts/virtual/cach-hoat-dong.php',
            'cho-que' => 'template-parts/virtual/cho-que.php',
            'an-toan-thuc-pham' => 'template-parts/virtual/an-toan-thuc-pham.php',
            'chuyen-nha-nong' => 'template-parts/virtual/chuyen-nha-nong.php',
            'faq' => 'template-parts/virtual/faq.php',
            'tai-khoan' => 'template-parts/virtual/tai-khoan.php',
        ];
        if (is_string($native_slug) && isset($native_overrides[$native_slug])) {
            return get_template_directory() . '/templates/virtual-page.php';
        }
    }

    return $template;
}
add_filter('template_include', 'aitrongcay_template_include_virtual_page');

function aitrongcay_virtual_page_title(string $title): string
{
    $page = aitrongcay_current_virtual_page();
    if (! $page) {
        return $title;
    }

    return $page['title'] . ' – ' . aitrongcay_company_profile()['brand'];
}
add_filter('pre_get_document_title', 'aitrongcay_virtual_page_title');

function aitrongcay_virtual_body_classes(array $classes): array
{
    $page = aitrongcay_current_virtual_page();
    if (! $page) {
        return $classes;
    }

    $classes[] = 'virtual-page';
    $classes[] = 'virtual-page-' . sanitize_html_class(str_replace('/', '-', $page['slug']));
    return $classes;
}
add_filter('body_class', 'aitrongcay_virtual_body_classes');
