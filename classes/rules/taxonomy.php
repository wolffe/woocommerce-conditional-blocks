<?php


class WC_Conditional_Content_Rule_Taxonomy_Product_Category extends WC_Conditional_Content_Rule_Base {

    public function __construct() {
        parent::__construct( 'taxonomy_product_category' );
    }

    public function get_possible_rule_operators(): array {
        $operators = [
            'in'    => __( 'is', 'woocommerce-conditional-blocks' ),
            'notin' => __( 'is not', 'woocommerce-conditional-blocks' ),
        ];

        return $operators;
    }

    public function get_possible_rule_values(): array {
        $result = [];

        $terms = wc_conditional_content_get_all_product_categories();
        if ( $terms && ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                $result[ $term->term_id ] = $term->name;
            }
        }

        return $result;
    }

    public function get_condition_input_type(): string {
        return 'Chosen_Select';
    }

    public function is_match( $rule_data, $arguments = null ): bool {

        $result = false;

        if ( isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
            $in = false;

            foreach ( $rule_data['condition'] as $term ) {
                if ( is_product_category( $term ) ) {
                    $in = true;
                }
            }
            $result = $rule_data['operator'] == 'in' ? $in : ! $in;
        }

        return $this->return_is_match( $result, $rule_data, $arguments );
    }
}
