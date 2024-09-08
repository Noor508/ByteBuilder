<?php


/*
Plugin Name: Minify
Plugin URI: https://github.com/Noor508/Minify.git
Description: A plugin to shorten URLs using Bitly and add share buttons.
Version: 1.0.0
Author: ByteBuilder Team
Author URI: https://github.com/Noor508/Minify.git
 * @Author: ByteBuilder
 * @Date:  2024-09-08
 * @Last Modified by:   ByteBuilder
 * @Website: https://github.com/Noor508/Minify.git
 * @Email:noorulaeen88@gmail.com
 * @Last Modified time: 2024-09-08
*License: GPLv2 or later
*Text Domain: su-bitly
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('SUBITLY_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('SUBITLY_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SUBITLY_PLUGIN_VERSION', '1.09');
define('SUBITLY_API_URL', 'https://api-ssl.bitly.com');
define('SUBITLY_BASENAME', plugin_basename(__FILE__));
define('SUBITLY_SETTINGS_URL', admin_url('options-general.php?page=su-bitly'));
function enqueue_su_scripts() {
    wp_enqueue_script('su-script', plugin_dir_url(__FILE__) . 'js/su-script.js', array('jquery'), null, true);
    wp_localize_script('su-script', 'suJS', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_su_scripts');


/**
 * Load Admin Assets
 */
require_once 'inc/su-assets.php';

/**
 * Load Util Functions
 */
require_once 'inc/su-util.php';

/**
 * Load Settings file
 */
require_once 'inc/su-settings.php';

/**
 * Load Bitly Integration
 */
require_once 'inc/su-integration.php';

/**
 * Load WordPress related hooks
 */
require_once 'inc/su-wp-functions.php';

/**
 * Meta Box
 */
require_once 'inc/su-metabox.php';

/**
 * Widget
 
 */
require_once 'inc/su-share-widget.php';

