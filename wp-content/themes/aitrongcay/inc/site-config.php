<?php

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

function aitrongcay_primary_nav_items(): array
{
    return [
        ['label' => 'Cách hoạt động', 'url' => home_url('/cach-hoat-dong/')],
        ['label' => 'Chợ quê', 'url' => home_url('/cho-que/')],
        ['label' => 'An toàn thực phẩm', 'url' => home_url('/an-toan-thuc-pham/')],
        ['label' => 'Chuyện nhà nông', 'url' => home_url('/chuyen-nha-nong/')],
        ['label' => 'FAQ', 'url' => home_url('/faq/')],
    ];
}

function aitrongcay_footer_groups(): array
{
    return [
        'explore' => [
            'title' => 'Khám phá',
            'items' => [
                ['label' => 'Cách hoạt động', 'url' => home_url('/cach-hoat-dong/')],
                ['label' => 'Chợ quê', 'url' => home_url('/cho-que/')],
            ],
        ],
        'trust' => [
            'title' => 'Niềm tin',
            'items' => [
                ['label' => 'An toàn thực phẩm', 'url' => home_url('/an-toan-thuc-pham/')],
                ['label' => 'Chuyện nhà nông', 'url' => home_url('/chuyen-nha-nong/')],
                ['label' => 'FAQ', 'url' => home_url('/faq/')],
            ],
        ],
        'start' => [
            'title' => 'Bắt đầu',
            'items' => [
                ['label' => 'Đăng ký tư vấn', 'url' => home_url('/dang-ky-tu-van/')],
                ['label' => 'Onboarding', 'url' => home_url('/onboarding/')],
                ['label' => 'Đăng nhập', 'url' => home_url('/dang-nhap/')],
            ],
        ],
    ];
}

function aitrongcay_company_profile(): array
{
    return [
        'brand' => 'Ai trồng cây',
        'tagline' => 'Một hành trình rau sạch minh bạch hơn, gần gũi hơn và đáng theo dõi mỗi ngày.',
        'company' => 'CÔNG TY CỔ PHẦN NGHIÊN CỨU GIẢI PHÁP VÀ PHÁT TRIỂN CÔNG NGHỆ XANH',
        'address' => 'Số 180A, đường Âu Cơ, Phường Tứ Liên, Quận Tây Hồ, Thành phố Hà Nội, Việt Nam',
        'phone' => '0983.660.988 – 0876.666.114',
        'description' => 'Dịch vụ vườn thuê số hóa dành cho gia đình muốn theo dõi khu vườn sạch của mình bằng webcam, care log, AI summary và hồ sơ chất lượng rõ ràng.',
    ];
}
