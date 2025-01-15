<?php
/**
 * Plugin Name: WooCommerce Conditional Blocks
 * Plugin URI: https://getbutterfly.com/wordpress-plugins/woocommerce-conditional-blocks/
 * Description: WooCommerce conditional blocks allows you to display additional or alternate content based on a set of rules and conditions, including current users role, product categories, product tags, prices, cart contents, and many, many more.
 * Version: 1.0.1
 * Author: getButterfly
 * Author URI: http://getbutterfly.com/
 * Update URI: http://getbutterfly.com/
 * Requires at least: 6.0
 * Requires Plugins: woocommerce
 * Tested up to: 6.7.1
 * License: GNU General Public License v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: woocommerce-bulk-discount
 *
 * WC requires at least: 8.0
 * WC tested up to: 9.5.2
 *
 * Parts of the code taken from Lucas Stark's Conditional Content plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
    die;
}

define( 'WCCC_VERSION', '1.0.1' );
define( 'WCCC_PLUGIN_URL', WP_PLUGIN_URL . '/' . dirname( plugin_basename( __FILE__ ) ) );
define( 'WCCC_PLUGIN_PATH', WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) );
define( 'WCCC_PLUGIN_FILE_PATH', WP_PLUGIN_DIR . '/' . plugin_basename( __FILE__ ) );

require WCCC_PLUGIN_PATH . '/includes/updater.php';

if ( ! class_exists( 'WC_Dependencies' ) ) {
    require_once 'woo-includes/class-wc-dependencies.php';
}

if ( ! function_exists( 'is_woocommerce_active' ) ) {
    function is_woocommerce_active() {
        return WC_Dependencies::woocommerce_active_check();
    }
}

if ( is_woocommerce_active() ) {
    // Declare support for features.
    add_action(
        'before_woocommerce_init',
        function () {
            if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__ );
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__ );
            }
        }
    );

    require_once 'woocommerce-conditional-blocks-main.php';

    // Hook to add the metaboxes for the `wccc` post type.
    add_action( 'add_meta_boxes', [ 'WC_Conditional_Content_Admin_Controller', 'add_metaboxes' ] );
}
