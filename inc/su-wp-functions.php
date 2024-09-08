<?php

/**
 * @Author: ByteBuilder
 * @Date:   2024-09-08 
 * @Last Modified by:   ByteBuilder
 * @Website: https://github.com/Noor508/Minify.git
 * @Email: noorulaeen88@gmail.com
 * @Last Modified time: 2024-09-08 
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

add_filter('pre_get_shortlink', 'change_core_short_link_with_su_link', 10, 5);
function change_core_short_link_with_su_link($status, $id, $context, $allow_slugs) {
    $bitly_url = get_su_bitly_short_url($id);
    if ($bitly_url) {
        return $bitly_url;
    }
    return $status;
}

function add_su_columns($columns) {
    // Only add the Share Count column, remove the Bitly URL column
    $columns['su_share_count'] = __('Share Count', 'su-bitly');
    return $columns;
}

add_filter('manage_post_posts_columns', 'add_su_columns');
add_filter('manage_page_posts_columns', 'add_su_columns'); // Ensure this is for pages as well

function display_su_columns($column, $post_id) {
    // Only display the Share Count column
    if ($column == 'su_share_count') {
        $share_count = get_post_meta($post_id, '_su_share_count', true);
        echo $share_count ? $share_count : 0;
    }
}

add_action('manage_post_posts_custom_column', 'display_su_columns', 10, 2);
add_action('manage_page_posts_custom_column', 'display_su_columns', 10, 2);

add_action('wp_ajax_su_increment_share_count', 'su_increment_share_count');
add_action('wp_ajax_nopriv_su_increment_share_count', 'su_increment_share_count'); // Allow non-logged-in users to access

function su_increment_share_count() {
    if (!isset($_POST['post_id'])) {
        wp_send_json_error('Post ID not provided');
    }

    $post_id = (int)$_POST['post_id'];
    $share_count = get_post_meta($post_id, '_su_share_count', true);
    $share_count = $share_count ? $share_count + 1 : 1;
    update_post_meta($post_id, '_su_share_count', $share_count);

    wp_send_json_success(['share_count' => $share_count]);
}

// Function to display share buttons
function su_display_share_buttons() {
    if (is_user_logged_in() && current_user_can('manage_options')) {
        return; // Do not display buttons for admin users
    }

    if (is_singular('post') || is_singular('page')) {
        $bitly_url = get_su_bitly_short_url(get_the_ID());
        if (!$bitly_url) {
            $bitly_url_data = su_generate_shorten_url(get_permalink());
            if (!$bitly_url_data['error']) {
                $bitly_url = $bitly_url_data['link'];
                save_su_bitly_short_url($bitly_url, get_the_ID());
            }
        }

        $share_buttons = '<div class="su_social_share_buttons">
            <a href="https://www.facebook.com/sharer/sharer.php?u=' . urlencode($bitly_url) . '" target="_blank" class="su_share_button su_facebook_share">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=' . urlencode($bitly_url) . '" target="_blank" class="su_share_button su_linkedin_share">
                <i class="fab fa-linkedin-in"></i>
            </a>
            <a href="mailto:?subject=I wanted to share this post with you&body=' . urlencode($bitly_url) . '" class="su_share_button su_email_share">
                <i class="fas fa-envelope"></i>
            </a>
        </div>';

        echo $share_buttons;
    }
}
add_action('wp_footer', 'su_display_share_buttons');

add_action('save_post', 'update_su_share_count');
function update_su_share_count($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $share_count = get_post_meta($post_id, '_su_share_count', true);
    $share_count = $share_count ? $share_count + 1 : 1;
    update_post_meta($post_id, '_su_share_count', $share_count);
}
