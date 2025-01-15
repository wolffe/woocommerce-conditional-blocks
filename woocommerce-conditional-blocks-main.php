<?php
/**
 * woocommerce_product_addons class
 * */
if ( ! class_exists( 'WC_Conditional_Content' ) ) {

    /**
     * The main class for Conditional Content
     */
    class WC_Conditional_Content {

        private static $instance;

        /**
         * Boots up the conditional content extension.
         */
        public static function register() {
            if ( empty( self::$instance ) ) {
                self::$instance = new WC_Conditional_Content();
            }
        }

        private static $version = '2.1.4';


        /**
         * Creates a new instance of the WC_Conditional_Content class.
         */
        public function __construct() {
            //include some defaults via our filters
            add_filter( 'wc_conditional_content_get_locations', [ $this, 'default_locations' ], 0 );
            add_filter( 'wc_conditional_content_get_rule_types', [ $this, 'default_rule_types' ], 0 );
            add_filter( 'wc_conditional_content_get_rule_operators', [ $this, 'default_rule_operators' ], 0 );

            //Include some core functions
            include 'classes/class-wc-conditional-content-compatibility.php';
            include 'woocommerce-conditional-blocks-functions.php';

            //Include our default rule classes
            include 'classes/rules/base.php';
            include 'classes/rules/general.php';
            include 'classes/rules/products.php';
            include 'classes/rules/schedule.php';
            include 'classes/rules/stock.php';
            include 'classes/rules/store.php';
            include 'classes/rules/sales.php';
            include 'classes/rules/users.php';
            include 'classes/rules/taxonomy.php';

            include 'classes/rules/cart.php';

            include 'classes/rules/geo.php';
            include 'classes/rules/wpml.php';

            //Include and register the taxonomy for storing the content blocks and their rules.
            include 'classes/class-wc-conditional-content-taxonomy.php';
            WC_Conditional_Content_Taxonomy::register();

            if ( is_admin() || defined( 'DOING_AJAX' ) ) {
                include 'admin/class-wc-conditional-blocks-admin-controller.php';
                WC_Conditional_Content_Admin_Controller::register();

                //Include the admin interface builder
                include 'classes/class-wc-conditional-content-input-builder.php';
                include 'classes/inputs/input.php';
                include 'classes/inputs/html-always.php';
                include 'classes/inputs/text.php';
                include 'classes/inputs/date.php';
                include 'classes/inputs/select.php';
                include 'classes/inputs/product-select.php';
                include 'classes/inputs/cart-product-select.php';
                include 'classes/inputs/cart-category-select.php';
                include 'classes/inputs/chosen-select.php';
                include 'classes/inputs/order-status.php';
            } else {
                include 'classes/class-wc-conditional-content-display.php';
                WC_Conditional_Content_Display::register();
            }
        }

        /**
         * Hooked into wc_conditional_content_get_rule_types to get the default list of rule types.
         *
         * @param array $types Current list, if any, of rule types.
         *
         * @return array the list of rule types.
         */
        public function default_rule_types( $types ): array {
            $types = [
                __( 'General', 'woocommerce-conditional-blocks' ) => [
                    'general_always' => __( 'Always', 'woocommerce-conditional-blocks' ),
                ],
                __( 'Product', 'woocommerce-conditional-blocks' ) => [
                    'product_select'    => __( 'Products', 'woocommerce-conditional-blocks' ),
                    'product_type'      => __( 'Product Type', 'woocommerce-conditional-blocks' ),
                    'product_category'  => __( 'Product Category', 'woocommerce-conditional-blocks' ),
                    'product_attribute' => __( 'Product Attributes', 'woocommerce-conditional-blocks' ),
                    'product_tag'       => __( 'Product Tag', 'woocommerce-conditional-blocks' ),
                    'product_price'     => __( 'Product Price', 'woocommerce-conditional-blocks' ),
                ],
                __( 'Schedule', 'woocommerce-conditional-blocks' ) => [
                    'schedule_date' => __( 'Date', 'woocommerce-conditional-blocks' ),
                    'schedule_day'  => __( 'Day', 'woocommerce-conditional-blocks' ),
                    'schedule_time' => __( 'Time', 'woocommerce-conditional-blocks' ),
                ],
                __( 'Stock', 'woocommerce-conditional-blocks' ) => [
                    'stock_status' => __( 'Stock Status', 'woocommerce-conditional-blocks' ),
                    'stock_level'  => __( 'Stock Level', 'woocommerce-conditional-blocks' ),
                ],
                __( 'Sales', 'woocommerce-conditional-blocks' ) => [
                    'sale_schedule' => __( 'Sale Date', 'woocommerce-conditional-blocks' ),
                    'sale_status'   => __( 'Sale Status', 'woocommerce-conditional-blocks' ),
                ],
                __( 'Membership', 'woocommerce-conditional-blocks' ) => [
                    'users_user'           => __( 'User', 'woocommerce-conditional-blocks' ),
                    'users_role'           => __( 'Role', 'woocommerce-conditional-blocks' ),
                    'users_authentication' => __( 'Authentication', 'woocommerce-conditional-blocks' ),
                ],
                __( 'Store', 'woocommerce-conditional-blocks' ) => [
                    'store_order_count' => __( 'Order Count and Status', 'woocommerce-conditional-blocks' ),
                ],
                __( 'Cart', 'woocommerce-conditional-blocks' ) => [
                    'cart_quantity'           => __( 'Cart Total Quantity', 'woocommerce-conditional-blocks' ),
                    'cart_total'              => __( 'Cart Total Monetary Amount', 'woocommerce-conditional-blocks' ),
                    'cart_product'            => __( 'Cart Products', 'woocommerce-conditional-blocks' ),
                    'cart_category'           => __( 'Cart Categories', 'woocommerce-conditional-blocks' ),
                    'cart_line_item_product'  => __( 'Cart Line Item Product', 'woocommerce-conditional-blocks' ),
                    'cart_line_item_quantity' => __( 'Cart Line Item Quantity', 'woocommerce-conditional-blocks' ),
                ],
                __( 'Geography', 'woocommerce-conditional-blocks' ) => [
                    'geo_country_code' => __( 'Country', 'woocommerce-conditional-blocks' ),
                    'wpml_language'    => __( 'WPML Language Code', 'woocommerce-conditional-blocks' ),
                ],
                __( 'Taxonomy', 'woocommerce-conditional-blocks' ) => [
                    'taxonomy_product_category' => __( 'Product Category Archive', 'woocommerce-conditional-blocks' ),
                ],
            ];

            return $types;
        }

        /**
         * Hooked into wc_conditional_content_get_rule_operators.  Get's the default list of rule operators.
         *
         * @param array $operators The current list, if any, of operators for rule types.
         *
         * @return array
         */
        public function default_rule_operators( $operators ) {
            $operators = [
                '==' => __( 'is equal to', 'woocommerce-conditional-blocks' ),
                '!=' => __( 'is not equal to', 'woocommerce-conditional-blocks' ),
            ];

            return $operators;
        }

        /**
         * Hooked into wc_conditional_content_get_locations.  Get's a list of actions and filters which can be used to
         * output conditional content.
         *
         * @param array $locations The current list, if any, of configured action hooks and filters.
         *
         * @return array
         */
        public function default_locations( $locations ) {
            $locations = [
                'woocommerce'    => [
                    'title'       => __( 'WooCommerce', 'woocommerce-conditional-blocks' ),
                    'description' => __( 'The single product page', 'woocommerce-conditional-blocks' ),
                    'hooks'       => [
                        'woocommerce_before_main_content' => [
                            'action'      => 'woocommerce_before_main_content',
                            'priority'    => 0,
                            'title'       => __( 'Before main content', 'woocommerce-conditional-blocks' ),
                            'description' => '',
                        ],
                        'woocommerce_after_main_content'  => [
                            'action'      => 'woocommerce_after_main_content',
                            'priority'    => 0,
                            'title'       => __( 'After main content', 'woocommerce-conditional-blocks' ),
                            'description' => '',
                        ],
                    ],
                ],
                'shop'           => [
                    'title'       => __( 'Shop', 'woocommerce-conditional-blocks' ),
                    'description' => __( 'The single product page', 'woocommerce-conditional-blocks' ),
                    'hooks'       => [
                        'woocommerce_before_shop_loop' => [
                            'action'      => 'woocommerce_before_shop_loop',
                            'priority'    => 0,
                            'title'       => __( 'Before shop loop', 'woocommerce-conditional-blocks' ),
                            'description' => '',
                        ],
                        'woocommerce_after_shop_loop'  => [
                            'action'      => 'woocommerce_after_shop_loop',
                            'priority'    => 0,
                            'title'       => __( 'After shop loop', 'woocommerce-conditional-blocks' ),
                            'description' => '',
                        ],
                    ],
                ],
                'single-product' => [
                    'title'       => __( 'Single Product', 'woocommerce-conditional-blocks' ),
                    'description' => __( 'The single product page', 'woocommerce-conditional-blocks' ),
                    'hooks'       => [
                        'woocommerce_before_single_product_summary' => [
                            'action'      => 'woocommerce_before_single_product_summary',
                            'priority'    => 0,
                            'title'       => __( 'Before single product summary', 'woocommerce-conditional-blocks' ),
                            'description' => '',
                        ],
                        'woocommerce_after_single_product_summary' => [
                            'action'      => 'woocommerce_after_single_product_summary',
                            'priority'    => 0,
                            'title'       => __( 'After single product summary', 'woocommerce-conditional-blocks' ),
                            'description' => '',
                        ],
                        'woocommerce_show_product_sale_flash' => [
                            'action'      => 'woocommerce_before_single_product_summary',
                            'priority'    => 11,
                            'title'       => __( 'Before sale flash', 'woocommerce-conditional-blocks' ),
                            'description' => '',
                        ],
                        'woocommerce_show_product_images'  => [
                            'action'      => 'woocommerce_before_single_product_summary',
                            'priority'    => 21,
                            'title'       => __( 'Before product images', 'woocommerce-conditional-blocks' ),
                            'description' => '',
                        ],
                        'woocommerce_template_single_title' => [
                            'action'      => 'woocommerce_single_product_summary',
                            'priority'    => 6,
                            'title'       => __( 'After product title', 'woocommerce-conditional-blocks' ),
                            'description' => '',
                        ],
                        'woocommerce_template_single_price' => [
                            'action'      => 'woocommerce_single_product_summary',
                            'priority'    => 11,
                            'title'       => __( 'After product price', 'woocommerce-conditional-blocks' ),
                            'description' => '',
                        ],
                        'woocommerce_template_single_excerpt' => [
                            'action'      => 'woocommerce_single_product_summary',
                            'priority'    => 21,
                            'title'       => __( 'After product description', 'woocommerce-conditional-blocks' ),
                            'description' => '',
                        ],
                        'woocommerce_template_single_add_to_cart' => [
                            'action'      => 'woocommerce_single_product_summary',
                            'priority'    => 31,
                            'title'       => __( 'After add to cart', 'woocommerce-conditional-blocks' ),
                            'description' => '',
                        ],
                        'woocommerce_template_single_meta' => [
                            'action'      => 'woocommerce_single_product_summary',
                            'priority'    => 41,
                            'title'       => __( 'After product meta', 'woocommerce-conditional-blocks' ),
                            'description' => '',
                        ],
                        'woocommerce_template_single_sharing' => [
                            'action'      => 'woocommerce_single_product_summary',
                            'priority'    => 51,
                            'title'       => __( 'After product sharing', 'woocommerce-conditional-blocks' ),
                            'description' => '',
                        ],
                        'woocommerce_output_product_data_tabs' => [
                            'action'      => 'woocommerce_after_single_product_summary',
                            'priority'    => 11,
                            'title'       => __( 'After product data tabs', 'woocommerce-conditional-blocks' ),
                            'description' => '',
                        ],
                        'woocommerce_output_related_products' => [
                            'action'      => 'woocommerce_after_single_product_summary',
                            'priority'    => 21,
                            'title'       => __( 'After related products', 'woocommerce-conditional-blocks' ),
                            'description' => '',
                        ],
                    ],
                ],
            ];

            return $locations;
        }

        /** Helper Functions **************************************************************** */

        /**
         * Return a nonce field.
         *
         * @access public
         *
         * @param mixed $action
         * @param bool $referer (default: true)
         * @param bool $echo_output
         *
         * @return string
         */
        public static function nonce_field( $action, bool $referer = true, bool $echo_output = true ): string {
            return wp_nonce_field( 'wcccaction-' . $action, '_n', $referer, $echo_output );
        }

        /**
         * Return a url with a nonce appended.
         *
         * @access public
         *
         * @param string $action
         * @param string $url (default: '')
         *
         * @return string
         */
        public static function nonce_url( $action, $url = '' ) {
            return add_query_arg(
                [
                    '_n'         => wp_create_nonce( 'wcccaction-' . $action ),
                    'wcccaction' => $action,
                ],
                $url
            );
        }

        public static function plugin_version(): string {
            return self::$version;
        }

        /**
         * Get the plugin url.
         *
         * @access public
         * @return string
         */
        public static function plugin_url(): string {
            return plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) );
        }

        /**
         * Get the plugin path.
         *
         * @access public
         * @return string
         */
        public static function plugin_path(): string {
            return untrailingslashit( plugin_dir_path( __FILE__ ) );
        }
    }

}

/*
 * Register the main conditional content class.
 */
WC_Conditional_Content::register();
