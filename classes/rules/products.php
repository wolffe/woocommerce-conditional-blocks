<?php
class WC_Conditional_Content_Rule_Product_Select extends WC_Conditional_Content_Rule_Base {

    public function __construct() {
        parent::__construct( 'product_select' );
    }

    public function get_possible_rule_operators(): array {
        $operators = [
            'in'    => __( 'is', 'woocommerce-conditional-blocks' ),
            'notin' => __( 'is not', 'woocommerce-conditional-blocks' ),
        ];

        return $operators;
    }

    public function get_condition_input_type(): string {
        return 'Product_Select';
    }

    public function is_match( $rule_data, $arguments = null ): bool {
        $result  = false;
        $product = wc_get_product( get_the_ID() );
        if ( $product && isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
            $in     = in_array( $product->get_id(), $rule_data['condition'] );
            $result = $rule_data['operator'] == 'in' ? $in : ! $in;
        }

        return $this->return_is_match( $result, $rule_data, $arguments );
    }
}

class WC_Conditional_Content_Rule_Product_Type extends WC_Conditional_Content_Rule_Base {

    public function __construct() {
        parent::__construct( 'product_type' );
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

        $terms = wc_conditional_content_get_product_types();
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

        $result  = false;
        $product = wc_get_product( get_the_ID() );
        if ( $product && isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
            $product_types = wp_get_post_terms( $product->get_id(), 'product_type', [ 'fields' => 'ids' ] );
            if ( $product_types && ! is_wp_error( $product_types ) ) {
                $in     = count( array_intersect( $product_types, $rule_data['condition'] ) ) > 0;
                $result = $rule_data['operator'] == 'in' ? $in : ! $in;
            }
        }

        return $this->return_is_match( $result, $rule_data, $arguments );
    }
}

class WC_Conditional_Content_Rule_Product_Category extends WC_Conditional_Content_Rule_Base {

    public function __construct() {
        parent::__construct( 'product_category' );
    }

    public function get_possible_rule_operators(): array {
        $operators = [
            'in'    => __( 'in', 'woocommerce-conditional-blocks' ),
            'notin' => __( 'not in', 'woocommerce-conditional-blocks' ),
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
        $product = wc_get_product( get_the_ID() );
        $result  = false;

        if ( $product && isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
            $terms = $product->get_category_ids();
            if ( $terms && ! is_wp_error( $terms ) && is_array( $terms ) ) {
                $in     = count( array_intersect( $terms, $rule_data['condition'] ) ) > 0;
                $result = $rule_data['operator'] == 'in' ? $in : ! $in;
            }
        }

        return $this->return_is_match( $result, $rule_data, $arguments );
    }
}

class WC_Conditional_Content_Rule_Product_Attribute extends WC_Conditional_Content_Rule_Base {

    public function __construct() {
        parent::__construct( 'product_attribute' );
    }

    public function get_possible_rule_operators(): array {
        $operators = [
            'in'    => __( 'has', 'woocommerce-conditional-blocks' ),
            'notin' => __( 'does not have', 'woocommerce-conditional-blocks' ),
        ];

        return $operators;
    }

    public function get_possible_rule_values(): array {
        global $woocommerce;

        $result = [];

        $attribute_taxonomies = WC_Conditional_Content_Compatibility::wc_get_attribute_taxonomies();

        if ( $attribute_taxonomies ) {
            //usort($attribute_taxonomies, array(&$this, 'sort_attribute_taxonomies'));

            foreach ( $attribute_taxonomies as $tax ) {
                $attribute_taxonomy_name = WC_Conditional_Content_Compatibility::wc_attribute_taxonomy_name( $tax->attribute_name );
                if ( taxonomy_exists( $attribute_taxonomy_name ) ) {
                    $terms = get_terms( $attribute_taxonomy_name, 'orderby=name&hide_empty=0' );
                    if ( $terms && ! is_wp_error( $terms ) ) {
                        foreach ( $terms as $term ) {
                            $result[ $attribute_taxonomy_name . '|' . $term->term_id ] = $tax->attribute_name . ': ' . $term->name;
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function get_condition_input_type(): string {
        return 'Chosen_Select';
    }

    public function sort_attribute_taxonomies( $taxa, $taxb ) {
        return strcmp( $taxa->attribute_name, $taxb->attribute_name );
    }

    public function is_match( $rule_data, $arguments = null ): bool {
        $product = wc_get_product( get_the_ID() );
        $result  = false;

        if ( $product && isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {

            foreach ( $rule_data['condition'] as $condition ) {

                $term_data = explode( '|', $condition );

                $attribute_taxonomy_name = $term_data[0];
                $term_id                 = $term_data[1];

                $post_terms = wp_get_post_terms( $product->get_id(), $attribute_taxonomy_name, [ 'fields' => 'ids' ] );
                if ( $post_terms && ! is_wp_error( $post_terms ) ) {
                    $in     = in_array( $term_id, $post_terms );
                    $result = $rule_data['operator'] == 'in' ? $in : ! $in;
                } else {
                    $result = false;
                }
            }
        }

        return $this->return_is_match( $result, $rule_data, $arguments );
    }
}


class WC_Conditional_Content_Rule_Product_Tag extends WC_Conditional_Content_Rule_Base {

    public function __construct() {
        parent::__construct( 'product_tag' );
    }

    public function get_possible_rule_operators(): array {
        $operators = [
            'in'    => __( 'in', 'woocommerce-conditional-blocks' ),
            'notin' => __( 'not in', 'woocommerce-conditional-blocks' ),
        ];

        return $operators;
    }

    public function get_possible_rule_values(): array {
        $result = [];

        $terms = wc_conditional_content_get_all_product_tags();
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
        $product = wc_get_product( get_the_ID() );
        $result  = false;

        if ( $product && isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
            $terms = $product->get_tag_ids();
            if ( $terms && ! is_wp_error( $terms ) && is_array( $terms ) ) {
                $in     = count( array_intersect( $terms, $rule_data['condition'] ) ) > 0;
                $result = $rule_data['operator'] == 'in' ? $in : ! $in;
            }
        }

        return $this->return_is_match( $result, $rule_data, $arguments );
    }
}

class WC_Conditional_Content_Rule_Product_Price extends WC_Conditional_Content_Rule_Base {

    public function __construct() {
        parent::__construct( 'product_price' );
    }

    public function get_possible_rule_operators(): array {
        $operators = [
            '==' => __( 'is equal to', 'woocommerce-conditional-blocks' ),
            '!=' => __( 'is not equal to', 'woocommerce-conditional-blocks' ),
            '>'  => __( 'is greater than', 'woocommerce-conditional-blocks' ),
            '<'  => __( 'is less than', 'woocommerce-conditional-blocks' ),
            '>=' => __( 'is greater or equal to', 'woocommerce-conditional-blocks' ),
            '=<' => __( 'is less or equal to', 'woocommerce-conditional-blocks' ),
        ];

        return $operators;
    }

    public function get_condition_input_type(): string {
        return 'Text';
    }

    public function is_match( $rule_data, $arguments = null ): bool {
        $result  = false;
        $product = wc_get_product( get_the_ID() );
        if ( $product && isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
            $price = $product->get_price();
            $value = (float) $rule_data['condition'];

            switch ( $rule_data['operator'] ) {
                case '==':
                    $result = $price == $value;
                    break;
                case '!=':
                    $result = $price != $value;
                    break;
                case '>':
                    $result = $price > $value;
                    break;
                case '<':
                    $result = $price < $value;
                    break;
                case '>=':
                    $result = $price >= $value;
                    break;
                case '<=':
                    $result = $price <= $value;
                    break;
                default:
                    $result = false;
                    break;
            }
        }

        return $this->return_is_match( $result, $rule_data, $arguments );
    }
}
