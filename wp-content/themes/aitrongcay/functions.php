<?php

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

require_once get_template_directory() . '/inc/site-config.php';

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

function aitrongcay_enqueue_assets(): void
{
    $theme = wp_get_theme();

    wp_enqueue_style(
        'aitrongcay-fonts',
        'https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap',
        [],
        null
    );

    wp_enqueue_style(
        'aitrongcay-theme',
        get_template_directory_uri() . '/assets/css/styles.css',
        ['aitrongcay-fonts'],
        $theme->get('Version')
    );

    wp_enqueue_script(
        'aitrongcay-theme',
        get_template_directory_uri() . '/assets/js/main.js',
        [],
        $theme->get('Version'),
        true
    );

    wp_localize_script('aitrongcay-theme', 'aitrongcayTheme', [
        'rootUrl' => home_url('/'),
        'portalUrl' => home_url('/portal/'),
        'authUrl' => home_url('/dang-nhap/'),
        'signupUrl' => home_url('/dang-ky-tu-van/'),
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
