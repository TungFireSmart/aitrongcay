<?php

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

function aitrongcay_page_content_definitions(): array
{
    return [
        'gioi_thieu' => [
            'option_name' => 'aitrongcay_content_gioi_thieu',
            'menu_title' => 'Giới thiệu',
            'page_title' => 'Sửa nội dung Giới thiệu',
            'preview_url' => home_url('/cach-hoat-dong/'),
            'sections' => [
                'hero' => [
                    'title' => 'Hero',
                    'fields' => [
                        'hero_eyebrow' => 'Eyebrow',
                        'hero_title' => 'Tiêu đề',
                        'hero_lead' => 'Mô tả',
                        'visual_kicker' => 'Visual - kicker',
                        'visual_title' => 'Visual - tiêu đề',
                        'visual_body' => 'Visual - mô tả',
                    ],
                ],
                'difference' => [
                    'title' => 'Khối khác biệt',
                    'fields' => [
                        'difference_eyebrow' => 'Eyebrow',
                        'difference_title' => 'Tiêu đề',
                        'difference_body' => 'Mô tả',
                        'benefit_eyebrow' => 'Khối phải - eyebrow',
                        'benefit_1' => 'Lợi ích 1',
                        'benefit_2' => 'Lợi ích 2',
                        'benefit_3' => 'Lợi ích 3',
                        'benefit_4' => 'Lợi ích 4',
                    ],
                ],
                'story' => [
                    'title' => 'Khối nội dung chính',
                    'fields' => [
                        'story_eyebrow' => 'Eyebrow',
                        'story_title' => 'Tiêu đề',
                        'story_p1' => 'Đoạn 1',
                        'story_p2' => 'Đoạn 2',
                        'story_p3' => 'Đoạn 3',
                    ],
                ],
                'cta' => [
                    'title' => 'Khối cuối trang',
                    'fields' => [
                        'cta_eyebrow' => 'Eyebrow',
                        'cta_title' => 'Tiêu đề',
                        'cta_body' => 'Mô tả',
                        'metric_1_label' => 'Metric 1 - nhãn',
                        'metric_1_value' => 'Metric 1 - giá trị',
                        'metric_2_label' => 'Metric 2 - nhãn',
                        'metric_2_value' => 'Metric 2 - giá trị',
                        'metric_3_label' => 'Metric 3 - nhãn',
                        'metric_3_value' => 'Metric 3 - giá trị',
                        'primary_cta' => 'Nút chính',
                        'secondary_cta' => 'Nút phụ',
                    ],
                ],
            ],
            'defaults' => [
                'hero_eyebrow' => 'Giới thiệu',
                'hero_title' => 'Ai trồng cây là một khu vườn số cho gia đình.',
                'hero_lead' => 'Anh/chị không chỉ nhận rau. Anh/chị còn theo dõi được cả quá trình. Có webcam, care log, dữ liệu môi trường và hồ sơ theo lô.',
                'visual_kicker' => 'Từ khu vườn đến bữa ăn',
                'visual_title' => 'Mọi thứ đủ rõ để gia đình yên tâm hơn',
                'visual_body' => 'Khu vườn không còn là một lời hứa mơ hồ. Anh/chị có thể mở ra và tự kiểm tra khi cần.',
                'difference_eyebrow' => 'Điểm khác biệt',
                'difference_title' => 'Không chỉ nhìn vào sản phẩm cuối',
                'difference_body' => 'Ai trồng cây cho anh/chị thấy cả quá trình. Nhờ vậy, cảm giác yên tâm đến từ những gì có thể kiểm tra, không chỉ từ lời giới thiệu.',
                'benefit_eyebrow' => 'Gia đình nhận được gì',
                'benefit_1' => 'Một khu vườn có thể xem bằng webcam',
                'benefit_2' => 'Nhật ký chăm sóc rõ ràng, dễ theo dõi',
                'benefit_3' => 'Dữ liệu môi trường được trình bày dễ hiểu',
                'benefit_4' => 'Hồ sơ theo lô đi cùng kỳ thu hoạch',
                'story_eyebrow' => 'Vì sao đáng bắt đầu',
                'story_title' => 'Một cách sống xanh gần gũi hơn',
                'story_p1' => 'Nhiều gia đình muốn ăn an tâm hơn. Nhưng không phải ai cũng có thời gian tự điều khiển và giám sát quá trình canh tác mỗi ngày.',
                'story_p2' => 'Ai trồng cây giữ lại cảm giác có một khu vườn của riêng mình. Đồng thời, hệ thống theo dõi giúp mọi thứ gọn hơn và rõ hơn.',
                'story_p3' => 'Mục tiêu cuối cùng là làm bữa ăn nhẹ lòng hơn. Và làm việc sống xanh trở nên dễ bắt đầu hơn.',
                'cta_eyebrow' => 'Bắt đầu từ sự phù hợp',
                'cta_title' => 'Mô hình này phù hợp khi gia đình cần sự rõ ràng và cảm giác gắn bó',
                'cta_body' => 'Anh/chị có thể xem trải nghiệm khu vườn trước. Hoặc đọc thêm phần an toàn thực phẩm để hiểu kỹ hơn.',
                'metric_1_label' => 'Trọng tâm',
                'metric_1_value' => 'Minh bạch',
                'metric_2_label' => 'Cảm giác',
                'metric_2_value' => 'Sở hữu',
                'metric_3_label' => 'Giá trị',
                'metric_3_value' => 'An tâm hơn',
                'primary_cta' => 'Xem trải nghiệm khu vườn',
                'secondary_cta' => 'Xem phần an toàn thực phẩm',
            ],
        ],
        'cho_que' => [
            'option_name' => 'aitrongcay_content_cho_que',
            'menu_title' => 'Chợ quê',
            'page_title' => 'Sửa nội dung Chợ quê',
            'preview_url' => home_url('/cho-que/'),
            'sections' => [
                'listing_hero' => [
                    'title' => 'Danh sách Chợ quê',
                    'fields' => [
                        'listing_eyebrow' => 'Eyebrow',
                        'listing_title' => 'Tiêu đề',
                        'listing_primary_cta' => 'Nút chính',
                        'listing_created_notice' => 'Thông báo đăng tin thành công',
                        'listing_contact_label' => 'Nút liên hệ ở card',
                        'listing_view_label' => 'Nút xem tin ở card',
                        'listing_edit_label' => 'Nút sửa tin',
                        'listing_delete_label' => 'Nút xóa tin',
                        'listing_like_label' => 'Nhãn chưa thích',
                        'listing_liked_label' => 'Nhãn đã thích',
                        'empty_notice' => 'Thông báo khi chưa có tin',
                    ],
                ],
                'detail' => [
                    'title' => 'Chi tiết tin Chợ quê',
                    'fields' => [
                        'detail_eyebrow' => 'Eyebrow',
                        'detail_back_label' => 'Nút quay về',
                        'detail_contact_label' => 'Nút liên hệ',
                        'detail_comments_title' => 'Tiêu đề bình luận',
                        'detail_comment_form_title' => 'Tiêu đề form bình luận',
                        'detail_comment_submit_label' => 'Nút gửi bình luận',
                        'detail_comment_login_notice' => 'Thông báo cần đăng nhập để bình luận',
                    ],
                ],
            ],
            'defaults' => [
                'listing_eyebrow' => 'Chợ quê',
                'listing_title' => 'Rau, hoa, giống cây và đồ nhà vườn',
                'listing_primary_cta' => 'Đăng tin từ vườn của tôi',
                'listing_created_notice' => 'Đã đăng tin thành công.',
                'listing_contact_label' => 'Liên hệ',
                'listing_view_label' => 'Xem tin',
                'listing_edit_label' => 'Sửa tin',
                'listing_delete_label' => 'Xóa tin',
                'listing_like_label' => 'Thích',
                'listing_liked_label' => 'Đã thích',
                'empty_notice' => 'Chưa có tin nào. Người bán và người mua có thể bắt đầu đăng tin từ đây.',
                'detail_eyebrow' => 'Chợ quê',
                'detail_back_label' => '← Về Chợ quê',
                'detail_contact_label' => 'Liên hệ người đăng',
                'detail_comments_title' => 'Bình luận',
                'detail_comment_form_title' => 'Để lại bình luận',
                'detail_comment_submit_label' => 'Gửi bình luận',
                'detail_comment_login_notice' => 'Đăng nhập để bình luận về tin này.',
            ],
        ],
        'an_toan_thuc_pham' => [
            'option_name' => 'aitrongcay_content_an_toan_thuc_pham',
            'menu_title' => 'An toàn thực phẩm',
            'page_title' => 'Sửa nội dung An toàn thực phẩm',
            'preview_url' => home_url('/an-toan-thuc-pham/'),
            'sections' => [
                'hero' => [
                    'title' => 'Hero',
                    'fields' => [
                        'hero_eyebrow' => 'Eyebrow',
                        'hero_title' => 'Tiêu đề',
                        'hero_lead' => 'Mô tả',
                    ],
                ],
                'cards' => [
                    'title' => '4 thẻ nội dung',
                    'fields' => [
                        'card_1_title' => 'Card 1 - tiêu đề',
                        'card_1_body' => 'Card 1 - mô tả',
                        'card_2_title' => 'Card 2 - tiêu đề',
                        'card_2_body' => 'Card 2 - mô tả',
                        'card_3_title' => 'Card 3 - tiêu đề',
                        'card_3_body' => 'Card 3 - mô tả',
                        'card_4_title' => 'Card 4 - tiêu đề',
                        'card_4_body' => 'Card 4 - mô tả',
                    ],
                ],
                'system' => [
                    'title' => 'Khối hệ thống đáng tin',
                    'fields' => [
                        'system_eyebrow' => 'Eyebrow',
                        'system_title' => 'Tiêu đề',
                        'layer_1' => 'Lớp 1',
                        'layer_2' => 'Lớp 2',
                        'layer_3' => 'Lớp 3',
                        'layer_4' => 'Lớp 4',
                        'needs_eyebrow' => 'Khối phải - eyebrow',
                        'needs_body' => 'Khối phải - mô tả',
                    ],
                ],
                'checklist' => [
                    'title' => 'Khối kiểm tra',
                    'fields' => [
                        'check_title' => 'Tiêu đề',
                        'check_left_title' => 'Cột trái - tiêu đề',
                        'check_left_1' => 'Cột trái - dòng 1',
                        'check_left_2' => 'Cột trái - dòng 2',
                        'check_left_3' => 'Cột trái - dòng 3',
                        'check_left_4' => 'Cột trái - dòng 4',
                        'check_left_5' => 'Cột trái - dòng 5',
                        'check_right_title' => 'Cột phải - tiêu đề',
                        'check_right_1' => 'Cột phải - dòng 1',
                        'check_right_2' => 'Cột phải - dòng 2',
                        'check_right_3' => 'Cột phải - dòng 3',
                        'check_right_4' => 'Cột phải - dòng 4',
                        'check_right_5' => 'Cột phải - dòng 5',
                    ],
                ],
            ],
            'defaults' => [
                'hero_eyebrow' => 'An toàn thực phẩm',
                'hero_title' => 'An toàn hơn khi gia đình có thể nhìn rõ quá trình',
                'hero_lead' => 'Ai trồng cây giúp gia đình theo dõi khu vườn qua dữ liệu, care log, webcam và hồ sơ theo lô. Nhờ vậy, cảm giác yên tâm đến từ những gì có thể kiểm tra.',
                'card_1_title' => 'Quy trình rõ từ đầu',
                'card_1_body' => 'Mùa vụ bắt đầu với nhịp chăm sóc và checklist được xác định trước, không làm theo cảm tính từng ngày.',
                'card_2_title' => 'Dữ liệu môi trường có theo dõi',
                'card_2_body' => 'Nhiệt độ, độ ẩm và các thay đổi quan trọng được ghi nhận để người dùng có thêm căn cứ khi nhìn lại.',
                'card_3_title' => 'Chăm sóc có nhật ký',
                'card_3_body' => 'Những lần can thiệp quan trọng nên có thời điểm, ghi chú và người thực hiện để tránh cảm giác mù mờ.',
                'card_4_title' => 'Thu hoạch gắn với mã lô',
                'card_4_body' => 'Để phần cuối của hành trình vẫn nối được với phần đầu, thay vì tách rời khỏi quá trình chăm sóc.',
                'system_eyebrow' => 'Một hệ thống đáng tin thường có',
                'system_title' => 'Niềm tin đến từ nhiều lớp bằng chứng nhỏ nhưng rõ ràng',
                'layer_1' => 'Giải thích quy trình vận hành bằng ngôn ngữ dễ hiểu ngay trên website.',
                'layer_2' => 'Cho xem tình trạng môi trường ở mức đủ đọc, đủ hiểu.',
                'layer_3' => 'Có care log để biết việc gì đã được làm và vào lúc nào.',
                'layer_4' => 'Có mã lô để nối từ khu vườn đến kỳ thu hoạch và bữa ăn.',
                'needs_eyebrow' => 'Điều người dùng cần',
                'needs_body' => 'Càng có nhiều điểm để đối chiếu, cảm giác yên tâm càng bền hơn và ít phụ thuộc vào lời giới thiệu.',
                'check_title' => 'Trang này nên giúp anh/chị kiểm tra được gì?',
                'check_left_title' => 'Những gì có thể xem',
                'check_left_1' => 'Kỳ chăm sóc hoặc mùa vụ đang diễn ra',
                'check_left_2' => 'Tình trạng môi trường ở mức tổng quan',
                'check_left_3' => 'Nhật ký các chăm sóc quan trọng',
                'check_left_4' => 'Mã lô của kỳ thu hoạch liên quan',
                'check_left_5' => 'Nhận định tóm tắt dễ hiểu',
                'check_right_title' => 'Những gì người dùng nhận lại',
                'check_right_1' => 'Biết mình đang tin vào điều gì',
                'check_right_2' => 'Thấy dịch vụ vận hành nghiêm túc hơn',
                'check_right_3' => 'Ít cảm giác mua trong mù mờ',
                'check_right_4' => 'An tâm hơn khi dùng cho người thân',
                'check_right_5' => 'Dễ gắn bó dài hạn nếu trải nghiệm phù hợp',
            ],
        ],
        'chuyen_nha_nong' => [
            'option_name' => 'aitrongcay_content_chuyen_nha_nong',
            'menu_title' => 'Chuyện nhà nông',
            'page_title' => 'Sửa nội dung Chuyện nhà nông',
            'preview_url' => home_url('/chuyen-nha-nong/'),
            'sections' => [
                'hero' => [
                    'title' => 'Hero',
                    'fields' => [
                        'hero_eyebrow' => 'Eyebrow',
                        'hero_title' => 'Tiêu đề',
                        'hero_lead' => 'Mô tả',
                        'hero_primary_cta' => 'Nút chính',
                        'hero_secondary_cta' => 'Nút phụ',
                        'featured_kicker' => 'Bài nổi bật - kicker',
                        'featured_title' => 'Bài nổi bật - tiêu đề',
                        'featured_body' => 'Bài nổi bật - mô tả',
                    ],
                ],
                'updates' => [
                    'title' => 'Khối bài cập nhật',
                    'fields' => [
                        'updates_eyebrow' => 'Eyebrow',
                        'updates_title' => 'Tiêu đề',
                        'update_1_title' => 'Bài 1 - tiêu đề',
                        'update_1_body' => 'Bài 1 - mô tả',
                        'update_2_title' => 'Bài 2 - tiêu đề',
                        'update_2_body' => 'Bài 2 - mô tả',
                        'update_3_title' => 'Bài 3 - tiêu đề',
                        'update_3_body' => 'Bài 3 - mô tả',
                    ],
                ],
                'quote' => [
                    'title' => 'Khối quote và chủ đề',
                    'fields' => [
                        'quote_title' => 'Quote',
                        'quote_body' => 'Mô tả quote',
                        'topics_eyebrow' => 'Chủ đề - eyebrow',
                        'topic_1_title' => 'Chủ đề 1 - tiêu đề',
                        'topic_1_body' => 'Chủ đề 1 - mô tả',
                        'topic_2_title' => 'Chủ đề 2 - tiêu đề',
                        'topic_2_body' => 'Chủ đề 2 - mô tả',
                        'topic_3_title' => 'Chủ đề 3 - tiêu đề',
                        'topic_3_body' => 'Chủ đề 3 - mô tả',
                    ],
                ],
            ],
            'defaults' => [
                'hero_eyebrow' => 'Chuyện nhà nông',
                'hero_title' => 'Những câu chuyện giúp gia đình sống xanh gần hơn mỗi ngày',
                'hero_lead' => 'Chuyện nhà nông là nơi kể về khu vườn, bữa ăn và nhịp sống nhẹ hơn. Nội dung ngắn, gần gũi và dễ đọc.',
                'hero_primary_cta' => 'Đọc bài mới',
                'hero_secondary_cta' => 'Bắt đầu nhẹ nhàng',
                'featured_kicker' => 'Bài viết nổi bật',
                'featured_title' => 'Làm sao để một khu vườn nhỏ vẫn mang lại cảm giác an yên cho cả gia đình?',
                'featured_body' => 'Đây là nơi người xem thấy việc trồng cây có thể dịu dàng, có nhịp riêng và đủ gần với đời sống bận rộn của mình.',
                'updates_eyebrow' => 'Mới cập nhật',
                'updates_title' => 'Những bài viết nên đọc vào một buổi sáng nhẹ hoặc một tối muốn chậm lại',
                'update_1_title' => '5 dấu hiệu cho thấy rau ăn lá đang phát triển trong điều kiện rất tốt',
                'update_1_body' => 'Từ màu lá đến độ đồng đều của tán cây — những dấu hiệu rất đời thường nhưng giúp người mới nhìn vườn tự tin hơn nhiều.',
                'update_2_title' => 'Vì sao nên ghi nhật ký chăm cây, dù chỉ vài dòng mỗi tuần?',
                'update_2_body' => 'Một cuốn nhật ký nhỏ hoặc care log điện tử giúp việc chăm vườn bớt rối, bớt quên và cũng giữ lại được nhiều niềm vui nhỏ.',
                'update_3_title' => 'Vì sao ngày càng nhiều gia đình thành thị quay lại với những thú vui chậm và xanh hơn?',
                'update_3_body' => 'Từ nấu ăn tại nhà đến chăm một khu vườn nhỏ, nhiều người đang tìm lại cảm giác bình yên qua những việc tưởng rất đơn giản.',
                'quote_title' => 'Đọc một bài viết hay về cây cối cũng giống như mở cửa sổ cho căn nhà thoáng hơn một chút.',
                'quote_body' => 'Chuyện nhà nông nên là nơi khiến người xem thấy dễ chịu, không vội, không nặng nề và có cảm hứng quay lại.',
                'topics_eyebrow' => 'Chủ đề nên xem',
                'topic_1_title' => 'Mẹo chăm rau ăn lá tại nhà',
                'topic_1_body' => 'Những điều nhỏ giúp người mới vẫn thấy dễ hiểu.',
                'topic_2_title' => 'Kinh nghiệm cho người mới bắt đầu',
                'topic_2_body' => 'Ít khẩu hiệu hơn, nhiều chi tiết đời sống hơn.',
                'topic_3_title' => 'Nhịp sống gia đình quanh khu vườn',
                'topic_3_body' => 'Bữa cơm nhà, trẻ con và niềm vui từ một luống rau xanh.',
            ],
        ],
        'faq' => [
            'option_name' => 'aitrongcay_content_faq',
            'menu_title' => 'FAQ',
            'page_title' => 'Sửa nội dung FAQ',
            'preview_url' => home_url('/faq/'),
            'sections' => [
                'hero' => [
                    'title' => 'Hero',
                    'fields' => [
                        'hero_eyebrow' => 'Eyebrow',
                        'hero_title' => 'Tiêu đề',
                        'hero_lead' => 'Mô tả',
                    ],
                ],
                'questions' => [
                    'title' => 'Câu hỏi & trả lời',
                    'fields' => [
                        'q1' => 'Câu hỏi 1',
                        'a1' => 'Trả lời 1',
                        'q2' => 'Câu hỏi 2',
                        'a2' => 'Trả lời 2',
                        'q3' => 'Câu hỏi 3',
                        'a3' => 'Trả lời 3',
                        'q4' => 'Câu hỏi 4',
                        'a4' => 'Trả lời 4',
                        'q5' => 'Câu hỏi 5',
                        'a5' => 'Trả lời 5',
                        'q6' => 'Câu hỏi 6',
                        'a6' => 'Trả lời 6',
                    ],
                ],
            ],
            'defaults' => [
                'hero_eyebrow' => 'FAQ',
                'hero_title' => 'Những câu hỏi thường gặp về khu vườn số cho gia đình',
                'hero_lead' => 'FAQ này giúp anh/chị hiểu mô hình nhanh hơn. Câu hỏi ngắn. Câu trả lời rõ và dễ hiểu.',
                'q1' => 'Tôi đang mua rau hay đang đăng ký một khu vườn?',
                'a1' => 'Hiểu đúng hơn là anh/chị đang đăng ký một khu vườn được vận hành cho gia đình mình. Rau là phần kết quả nhận về, còn giá trị chính nằm ở việc có thể theo dõi và hiểu cả quá trình.',
                'q2' => 'Tôi sẽ xem được những gì trong khu vườn số?',
                'a2' => 'Khu vườn số có webcam, ảnh theo mốc, cập nhật môi trường, nhật ký chăm sóc và hồ sơ theo lô khi đến kỳ thu hoạch.',
                'q3' => 'Nếu tôi không rành kỹ thuật thì có theo dõi được không?',
                'a3' => 'Có. Hệ thống được viết theo hướng gần gũi để người dùng phổ thông vẫn biết hôm nay vườn ổn hay có điều gì đáng lưu ý.',
                'q4' => 'Gia đình có thể cùng xem khu vườn không?',
                'a4' => 'Có. Mô hình phù hợp với việc chia sẻ để cả nhà cùng theo dõi, nhất là khi muốn con nhỏ hoặc người thân cùng tham gia hành trình này.',
                'q5' => 'Điều gì làm mô hình này đáng tin hơn mua rau thông thường?',
                'a5' => 'Khác biệt nằm ở chỗ anh/chị có thêm các lớp để kiểm tra: quy trình, theo dõi môi trường, nhật ký chăm sóc và truy xuất theo lô, thay vì chỉ nhận sản phẩm cuối cùng.',
                'q6' => 'Sau khi tôi gửi form thì bước tiếp theo là gì?',
                'a6' => 'Bên em sẽ liên hệ ngắn để hiểu nhu cầu thực tế. Nếu phù hợp, mình mới bàn tiếp về gói, nhịp bắt đầu và cách theo dõi khu vườn.',
            ],
        ],
    ];
}

function aitrongcay_page_content_defaults(string $page): array
{
    $definitions = aitrongcay_page_content_definitions();
    return $definitions[$page]['defaults'] ?? [];
}

function aitrongcay_page_content_option_name(string $page): string
{
    $definitions = aitrongcay_page_content_definitions();
    return (string) ($definitions[$page]['option_name'] ?? '');
}

function aitrongcay_page_content_preview_url(string $page): string
{
    $definitions = aitrongcay_page_content_definitions();
    return isset($definitions[$page]['preview_url']) ? (string) $definitions[$page]['preview_url'] : home_url('/');
}

function aitrongcay_page_content(string $page): array
{
    $defaults = aitrongcay_page_content_defaults($page);
    $option_name = aitrongcay_page_content_option_name($page);
    $saved = $option_name !== '' ? get_option($option_name, []) : [];
    return array_merge($defaults, is_array($saved) ? $saved : []);
}

function aitrongcay_page_text(string $page, string $key): string
{
    $content = aitrongcay_page_content($page);
    return isset($content[$key]) ? (string) $content[$key] : '';
}

function aitrongcay_register_page_content_settings(): void
{
    foreach (aitrongcay_page_content_definitions() as $page => $definition) {
        register_setting(
            'aitrongcay_content_group_' . $page,
            (string) $definition['option_name'],
            [
                'type' => 'array',
                'sanitize_callback' => static fn($input): array => aitrongcay_sanitize_page_content($page, $input),
                'default' => (array) $definition['defaults'],
            ]
        );
    }
}
add_action('admin_init', 'aitrongcay_register_page_content_settings');

function aitrongcay_sanitize_page_content(string $page, $input): array
{
    $defaults = aitrongcay_page_content_defaults($page);
    $input = is_array($input) ? $input : [];
    $output = [];

    foreach ($defaults as $key => $default) {
        $output[$key] = sanitize_textarea_field((string) ($input[$key] ?? $default));
    }

    return $output;
}

function aitrongcay_add_content_admin_menu(): void
{
    add_menu_page(
        'Sửa nội dung website',
        'Sửa nội dung website',
        'edit_pages',
        'aitrongcay-site-content',
        'aitrongcay_render_site_content_welcome',
        'dashicons-edit-large',
        31
    );

    add_submenu_page(
        'aitrongcay-site-content',
        'Sửa nội dung trang chủ',
        'Trang chủ',
        'edit_pages',
        'aitrongcay-homepage-content-main',
        'aitrongcay_render_homepage_admin_page'
    );

    foreach (aitrongcay_page_content_definitions() as $page => $definition) {
        add_submenu_page(
            'aitrongcay-site-content',
            (string) $definition['page_title'],
            (string) $definition['menu_title'],
            'edit_pages',
            'aitrongcay-content-' . $page,
            static fn() => aitrongcay_render_page_content_admin_page($page)
        );
    }
}
add_action('admin_menu', 'aitrongcay_add_content_admin_menu');

function aitrongcay_render_site_content_welcome(): void
{
    ?>
    <div class="wrap">
        <h1>Sửa nội dung website</h1>
        <p>Anh có thể sửa nhanh text của các trang chính ngay trong menu này.</p>
        <ul style="line-height:1.9">
            <li><a href="<?php echo esc_url(admin_url('admin.php?page=aitrongcay-homepage-content-main')); ?>">Trang chủ</a></li>
            <li><a href="<?php echo esc_url(admin_url('admin.php?page=aitrongcay-content-cho_que')); ?>">Chợ quê</a></li>
            <li><a href="<?php echo esc_url(admin_url('admin.php?page=aitrongcay-content-an_toan_thuc_pham')); ?>">An toàn thực phẩm</a></li>
            <li><a href="<?php echo esc_url(admin_url('admin.php?page=aitrongcay-content-chuyen_nha_nong')); ?>">Chuyện nhà nông</a></li>
            <li><a href="<?php echo esc_url(admin_url('admin.php?page=aitrongcay-content-faq')); ?>">FAQ</a></li>
        </ul>
    </div>
    <?php
}

function aitrongcay_render_page_content_admin_page(string $page): void
{
    if (! current_user_can('edit_pages')) {
        return;
    }

    $definitions = aitrongcay_page_content_definitions();
    $definition = $definitions[$page] ?? null;
    if (! is_array($definition)) {
        return;
    }

    $content = aitrongcay_page_content($page);
    ?>
    <div class="wrap">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap">
            <div>
                <h1><?php echo esc_html((string) $definition['page_title']); ?></h1>
                <p style="margin-top:6px">Sửa xong có thể bấm Lưu rồi mở Preview nhanh để xem ngay ngoài site.</p>
            </div>
            <div>
                <a class="button button-secondary button-large" href="<?php echo esc_url(aitrongcay_page_content_preview_url($page)); ?>" target="_blank" rel="noopener">Preview nhanh</a>
            </div>
        </div>
        <form method="post" action="options.php">
            <?php settings_fields('aitrongcay_content_group_' . $page); ?>
            <?php $section_index = 1; ?>
            <?php foreach ((array) $definition['sections'] as $section) : ?>
                <section style="margin-top:24px;background:#fff;border:1px solid #dcdcde;border-radius:14px;padding:20px;box-shadow:0 1px 2px rgba(0,0,0,.03)">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;margin-bottom:14px">
                        <div>
                            <div style="font-size:12px;font-weight:700;letter-spacing:.08em;color:#2271b1;text-transform:uppercase;margin-bottom:4px">Vị trí trên page</div>
                            <h2 style="margin:0 0 6px"><?php echo esc_html((string) $section['title']); ?></h2>
                            <p style="margin:0;color:#50575e;max-width:900px">Block này được đặt theo đúng thứ tự hiển thị trên trang để anh dễ hình dung đang sửa đoạn nào.</p>
                        </div>
                        <span style="display:inline-block;background:#f0f6fc;color:#0a4b78;border:1px solid #c3d9ed;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:600">Block <?php echo esc_html((string) $section_index); ?></span>
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:18px 24px">
                        <?php foreach ((array) $section['fields'] as $key => $label) : ?>
                            <div>
                                <label for="<?php echo esc_attr($page . '_' . $key); ?>" style="display:block;font-weight:600;margin-bottom:6px"><?php echo esc_html((string) $label); ?></label>
                                <textarea class="large-text" rows="3" style="width:100%;max-width:980px" id="<?php echo esc_attr($page . '_' . $key); ?>" name="<?php echo esc_attr((string) $definition['option_name']); ?>[<?php echo esc_attr((string) $key); ?>]"><?php echo esc_textarea((string) ($content[$key] ?? '')); ?></textarea>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php $section_index++; ?>
            <?php endforeach; ?>
            <div style="margin-top:22px"><?php submit_button('Lưu nội dung trang'); ?></div>
        </form>
    </div>
    <?php
}
