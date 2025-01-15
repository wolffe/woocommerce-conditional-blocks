<?php
/**
 * Helper class to register and manage the conditional content post type, wccc
 */
class WC_Conditional_Content_Taxonomy {
    private static $instance;

    /**
     * Registers a single instance of the WC_Conditional_Content_Taxonomy class
     */
    public static function register() {
        if ( self::$instance == null ) {
            self::$instance = new WC_Conditional_Content_Taxonomy();
        }
    }

    /**
     * Creates a new instnace of the WC_Conditional_Content_Taxonomy class
     */
    public function __construct() {
        add_action( 'init', [ &$this, 'on_woocommerce_init' ], 99 );
    }

    /**
     * Registers the wccc post type after woocommerce_init.
     */
    public function on_woocommerce_init(): void {
        $menu_name    = _x( 'Conditional Blocks', 'Admin menu name', 'woocommerce-conditional-blocks' );
        $show_in_menu = current_user_can( 'manage_woocommerce' ) ? 'woocommerce' : true;
        register_post_type(
            'wccc',
            apply_filters(
                'woocommerce_conditional_content_post_type',
                [
                    'labels'              => [
                        'name'               => __( 'Conditional Blocks', 'woocommerce-conditional-blocks' ),
                        'singular_name'      => __( 'Conditional Block', 'woocommerce-conditional-blocks' ),
                        'add_new'            => __( 'Add Conditional Block', 'woocommerce-conditional-blocks' ),
                        'add_new_item'       => __( 'Add New Conditional Block', 'woocommerce-conditional-blocks' ),
                        'edit'               => __( 'Edit', 'woocommerce-conditional-blocks' ),
                        'edit_item'          => __( 'Edit Conditional Block', 'woocommerce-conditional-blocks' ),
                        'new_item'           => __( 'New Conditional Blocks', 'woocommerce-conditional-blocks' ),
                        'view'               => __( 'View Conditional Block', 'woocommerce-conditional-blocks' ),
                        'view_item'          => __( 'View Conditional Block', 'woocommerce-conditional-blocks' ),
                        'search_items'       => __( 'Search Conditional Blocks', 'woocommerce-conditional-blocks' ),
                        'not_found'          => __( 'No Conditional Blocks found', 'woocommerce-conditional-blocks' ),
                        'not_found_in_trash' => __( 'No Conditional Blocks found in trash', 'woocommerce-conditional-blocks' ),
                        'parent'             => __( 'Parent Conditional Blocks', 'woocommerce-conditional-blocks' ),
                        'menu_name'          => $menu_name,
                    ],
                    'description'         => __( 'This is where conditional blocks and their associated rules are stored.', 'woocommerce-conditional-blocks' ),
                    'public'              => false, // Not publicly accessible
                    'show_ui'             => true,
                    'capability_type'     => 'product',
                    'map_meta_cap'        => true,
                    'publicly_queryable'  => false, // Prevent queryable
                    'exclude_from_search' => true, // Exclude from search
                    'show_in_menu'        => $show_in_menu,
                    'hierarchical'        => false,
                    'show_in_nav_menus'   => false,
                    'rewrite'             => false, // No rewrite rules
                    'query_var'           => true,
                    'supports'            => [ 'title', 'editor' ],
                    'has_archive'         => false,
                    'show_in_rest'        => true, // Enable block editor support
                    //'register_meta_box_cb' => [ 'WC_Conditional_Content_Admin_Controller', 'add_metaboxes' ], // This will force the classic editor
                ]
            )
        );
    }
}
