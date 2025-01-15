<?php
/**
 * The main admin controller for Conditional Content.
 *
 * Handles adding the meta boxes to the wccc post type and manages the saving of data.
 */
class WC_Conditional_Content_Admin_Controller {

    /**
     * The single instance of the WC_Conditional_Content_Admin_Controller class
     *
     * @var WC_Conditional_Content_Admin_Controller
     */
    private static $instance;

    /**
     * Registers a single instance of the WC_Conditional_Content_Admin_Controller class
     */
    public static function register(): void {
        if ( null === self::$instance ) {
            self::$instance = new WC_Conditional_Content_Admin_Controller();
        }
    }

    /**
     * Returns a single instance of the WC_Conditional_Content_Admin_Controller class.
     *
     * @return WC_Conditional_Content_Admin_Controller
     */
    public static function instance() {
        self::register();

        return self::$instance;
    }

    /**
     * Registers the settings and rules metabox for the wccc post type.  Called from the metabox callback as defined in
     * WC_Conditional_Content_Taxonomy.
     */
    public static function add_metaboxes() {
        $instance = self::instance();
        add_meta_box( 'wccc_settings', 'Conditional Block Settings', [ $instance, 'settings_metabox' ], 'wccc', 'side', 'low' );
        add_meta_box( 'wccc_rules', 'Rules', [ $instance, 'rules_metabox' ], 'wccc', 'normal', 'high' );
    }

    /**
     * Creates a new instance of the WC_Conditional_Content_Admin_Controller class
     */
    public function __construct() {
        add_action( 'admin_enqueue_scripts', [ $this, 'on_enqueue_scripts' ], 100 );

        // Save Data
        add_action( 'save_post', [ $this, 'save_data' ], 10, 2 );

        // Hook up the ajax actions
        add_action( 'wp_ajax_wccc_change_rule_type', [ $this, 'ajax_render_rule_choice' ] );
    }

    /*
     * Load the required scripts and style sheets on the wccc post type admin screens.
     */
    public function on_enqueue_scripts( $handle ) {
        global $post_type, $woocommerce;

        if ( ( $handle === 'post-new.php' || $handle === 'post.php' || $handle === 'edit.php' ) && $post_type === 'wccc' ) {
            // styles.
            $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
            wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', [], WC_Conditional_Content::plugin_version() );
            wp_enqueue_style( 'wccc-admin-app', WC_Conditional_Content::plugin_url() . '/assets/admin/css/wccc-admin-app.css', [], WC_Conditional_Content::plugin_version() );

            // chosen
            wp_enqueue_style( 'chosen', WC_Conditional_Content::plugin_url() . '/assets/css/chosen.css', [], WC_Conditional_Content::plugin_version() );
            wp_register_script( 'chosen', WC_Conditional_Content::plugin_url() . '/assets/js/chosen/chosen.jquery' . $suffix . '.js', [ 'jquery' ], WC_Conditional_Content_Compatibility::get_wc_version(), true );

            // woocommerce
            wp_enqueue_style( 'jquery-ui-datepicker' );
            wp_enqueue_style( 'woocommerce_admin_styles' );
            wp_enqueue_style( 'jquery-ui-style' );
            wp_enqueue_style( 'wp-color-picker' );

            // enhanced dropdowns
            wp_register_script( 'selectWoo', WC()->plugin_url() . '/assets/js/selectWoo/selectWoo.full.min.js', [ 'jquery' ], WC_Conditional_Content_Compatibility::get_wc_version(), true );
            wp_register_script(
                'wc-enhanced-select',
                WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select.min.js',
                [
                    'jquery',
                    'selectWoo',
                ],
                WC_Conditional_Content_Compatibility::get_wc_version(),
                true
            );
            wp_localize_script(
                'wc-enhanced-select',
                'wc_enhanced_select_params',
                [
                    'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'woocommerce-conditional-blocks' ),
                    'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'woocommerce-conditional-blocks' ),
                    'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'woocommerce-conditional-blocks' ),
                    'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'woocommerce-conditional-blocks' ),
                    'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'woocommerce-conditional-blocks' ),
                    'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'woocommerce-conditional-blocks' ),
                    'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'woocommerce-conditional-blocks' ),
                    'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'woocommerce-conditional-blocks' ),
                    'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'woocommerce-conditional-blocks' ),
                    'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce-conditional-blocks' ),
                    'ajax_url'                  => admin_url( 'admin-ajax.php' ),
                    'search_products_nonce'     => wp_create_nonce( 'search-products' ),
                    'search_customers_nonce'    => wp_create_nonce( 'search-customers' ),
                ]
            );

            wp_enqueue_script( 'jquery-ui-datepicker' );
            wp_enqueue_script(
                'wccc-admin-app',
                WC_Conditional_Content::plugin_url() . '/assets/admin/js/wccc-admin-app.js',
                [
                    'jquery',
                    'jquery-ui-datepicker',
                    'underscore',
                    'backbone',
                    'selectWoo',
                    'wc-enhanced-select',
                    'chosen',
                ],
                WC_Conditional_Content::plugin_version(),
                true
            );

            $data = [
                'ajax_nonce'            => wp_create_nonce( 'wcccaction-admin' ),
                'plugin_url'            => WC_Conditional_Content::plugin_url(),
                'ajax_url'              => admin_url( 'admin-ajax.php' ),
                'ajax_chosen'           => wp_create_nonce( 'json-search' ),
                'search_products_nonce' => wp_create_nonce( 'search-products' ),
                'text_or'               => __( 'or', 'woocommerce-conditional-blocks' ),
                'text_apply_when'       => __( 'Apply this content when these conditions are matched', 'woocommerce-conditional-blocks' ),
                'remove_text'           => __( 'Remove', 'woocommerce-conditional-blocks' ),
            ];

            wp_localize_script( 'wccc-admin-app', 'WCCCParams', $data );
        }
    }

    /**
     * Renders the rules metabox.
     */
    public function rules_metabox() {
        include 'views/metabox-rules.php';
    }

    /**
     * Renders the settings metabox.
     */
    public function settings_metabox() {
        include 'views/metabox-settings.php';
    }

    /**
     * Saves the data for the wccc post type.
     *
     * @param int $post_id Post ID.
     * @param WP_Post $post Post Object.
     *
     * @return null
     */
    public function save_data( $post_id, $post ) {
        if ( empty( $post_id ) || empty( $post ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( is_int( wp_is_post_revision( $post ) ) ) {
            return;
        }
        if ( is_int( wp_is_post_autosave( $post ) ) ) {
            return;
        }
        if ( 'wccc' !== $post->post_type ) {
            return;
        }

        $nonce_name = 'admin';
        if ( isset( $_REQUEST[ $nonce_name ] ) && ! wp_verify_nonce( sanitize_key( $_REQUEST[ $nonce_name ] ), 'admin' ) ) {
            return;
        }

        if ( isset( $_POST['wccc_settings_location'] ) ) {
            $location = explode( ':', $_POST['wccc_settings_location'] );
            $settings = [
                'location' => $location[0],
                'hook'     => $location[1],
            ];

            if ( 'custom' === $settings['hook'] ) {
                $settings['custom_hook']          = $_POST['wccc_settings_location_custom_hook'];
                $settings['custom_priority']      = $_POST['wccc_settings_location_custom_priority'];
                $settings['custom_accepted_args'] = $_POST['wccc_settings_location_custom_accepted_args'];
            } else {
                $settings['custom_hook']          = '';
                $settings['custom_priority']      = '';
                $settings['custom_accepted_args'] = 0;
            }

            $settings['type'] = $_POST['wccc_settings_type'];

            update_post_meta( $post_id, '_wccc_settings', $settings );
        }

        if ( isset( $_POST['wccc_rule'] ) ) {
            update_post_meta( $post_id, 'wccc_rule', $_POST['wccc_rule'] );
        } else {
            delete_post_meta( $post_id, 'wccc_rule' );
        }

        return null;
    }

    /**
     * Ajax and PHP Rendering Functions for Options. Renders the correct Operator and Values controls.
     *
     * @param array $options The group config options to render the template with.
     */
    public function ajax_render_rule_choice( $options ) {
        // defaults.
        $defaults = [
            'group_id'  => 0,
            'rule_id'   => 0,
            'rule_type' => null,
            'condition' => null,
            'operator'  => null,
        ];

        $is_ajax = false;
        if ( isset( $_POST['action'] ) && 'wccc_change_rule_type' === $_POST['action'] ) {
            $is_ajax = true;
        }

        if ( $is_ajax ) {

            if ( ! check_ajax_referer( 'wcccaction-admin', 'security' ) ) {
                die();
            }

            $options = array_merge( $defaults, $_POST );
        } else {
            $options = array_merge( $defaults, $options );
        }

        if ( ! isset( $options['rule_type'] ) ) {
            error_log( sprintf( 'Rule type not set in options: %s', print_r( $options, true ) ) );
        }

        $rule_object = woocommerce_conditional_content_get_rule_object( $options['rule_type'] );
        if ( ! empty( $rule_object ) ) {
            $values               = $rule_object->get_possible_rule_values();
            $operators            = $rule_object->get_possible_rule_operators();
            $condition_input_type = $rule_object->get_condition_input_type();

            // create operators field.
            $operator_args = [
                'input'   => 'select',
                'name'    => 'wccc_rule[' . $options['group_id'] . '][' . $options['rule_id'] . '][operator]',
                'choices' => $operators,
            ];

            echo '<td class="operator">';
            if ( ! empty( $operators ) ) {
                WC_Conditional_Content_Input_Builder::create_input_field( $operator_args, $options['operator'] );
            } else {
                echo '<input type="hidden" name="' . esc_attr( $operator_args['name'] ) . '" value="==" />';
            }
            echo '</td>';

            // create values field.
            $value_args = [
                'input'   => $condition_input_type,
                'name'    => 'wccc_rule[' . $options['group_id'] . '][' . $options['rule_id'] . '][condition]',
                'choices' => $values,
            ];

            echo '<td class="condition">';
            WC_Conditional_Content_Input_Builder::create_input_field( $value_args, $options['condition'] );
            echo '</td>';
        }

        // ajax?
        if ( $is_ajax ) {
            die();
        }
    }

    /**
     * Called from the metabox_settings.php screen.  Renders the template for a rule group that has already been saved.
     *
     * @param array $options The group config options to render the template with.
     */
    public function render_rule_choice_template( $options ) {
        // defaults.
        $defaults = [
            'group_id'  => 0,
            'rule_id'   => 0,
            'rule_type' => null,
            'condition' => null,
            'operator'  => null,
        ];

        $options     = array_merge( $defaults, $options );
        $rule_object = woocommerce_conditional_content_get_rule_object( $options['rule_type'] );

        $values               = $rule_object->get_possible_rule_values();
        $operators            = $rule_object->get_possible_rule_operators();
        $condition_input_type = $rule_object->get_condition_input_type();

        // create operators field.
        $operator_args = [
            'input'   => 'select',
            'name'    => 'wccc_rule[<%= groupId %>][<%= ruleId %>][operator]',
            'choices' => $operators,
        ];

        echo '<td class="operator">';
        if ( ! empty( $operators ) ) {
            WC_Conditional_Content_Input_Builder::create_input_field( $operator_args, $options['operator'] );
        } else {
            echo '<input type="hidden" name="' . esc_attr( $operator_args['name'] ) . '" value="==" />';
        }
        echo '</td>';

        // create values field.
        $value_args = [
            'input'   => $condition_input_type,
            'name'    => 'wccc_rule[<%= groupId %>][<%= ruleId %>][condition]',
            'choices' => $values,
        ];

        echo '<td class="condition">';
        WC_Conditional_Content_Input_Builder::create_input_field( $value_args, $options['condition'] );
        echo '</td>';
    }
}
