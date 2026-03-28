<?php

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

get_header();
?>
<main>
    <?php get_template_part('template-parts/home/hero'); ?>
    <?php get_template_part('template-parts/home/value-props'); ?>
    <?php get_template_part('template-parts/home/journey'); ?>
    <?php get_template_part('template-parts/home/portal'); ?>
    <?php get_template_part('template-parts/home/packages'); ?>
    <?php get_template_part('template-parts/home/trust'); ?>
    <?php get_template_part('template-parts/home/faq'); ?>
    <?php get_template_part('template-parts/home/cta'); ?>
</main>
<?php
get_footer();
