<?php
class WC_Conditional_Content_Rule_Stock_Status extends WC_Conditional_Content_Rule_Base {

    public function __construct() {
        parent::__construct( 'stock_status' );
    }

    public function get_possible_rule_operators(): array {
        $operators = [
            '==' => __( 'is', 'woocommerce-conditional-blocks' ),
            '!=' => __( 'is not', 'woocommerce-conditional-blocks' ),
        ];
        return $operators;
    }

    public function get_possible_rule_values(): array {
        $options = [
            '0' => __( 'Out of Stock', 'woocommerce-conditional-blocks' ),
            '1' => __( 'In Stock', 'woocommerce-conditional-blocks' ),
        ];

        return $options;
    }

    public function get_condition_input_type(): string {
        return 'Select';
    }

    public function is_match( $rule_data, $arguments = null ): bool {

        $result  = false;
        $product = wc_get_product( get_the_ID() );
        if ( $product && isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
            $in = $product->is_in_stock();
            if ( $rule_data['operator'] == '==' ) {
                $result = $rule_data['condition'] == 1 ? $in : ! $in;
            }

            if ( $rule_data['operator'] == '!=' ) {
                $result = ! ( $rule_data['condition'] == 1 ? $in : ! $in );
            }
        }

        return $this->return_is_match( $result, $rule_data, $arguments );
    }
}

class WC_Conditional_Content_Rule_Stock_Level extends WC_Conditional_Content_Rule_Base {

    public function __construct() {
        parent::__construct( 'stock_level' );
    }

    public function get_possible_rule_operators(): array {
        $operators = [
            '==' => __( 'is equal to', 'woocommerce-conditional-blocks' ),
            '!=' => __( 'is not equal to', 'woocommerce-conditional-blocks' ),
            '>'  => __( 'is greater than', 'woocommerce-conditional-blocks' ),
            '<'  => __( 'is less than', 'woocommerce-conditional-blocks' ),
            '>=' => __( 'is greater or equal to', 'woocommerce-conditional-blocks' ),
            '<=' => __( 'is less or equal to', 'woocommerce-conditional-blocks' ),
        ];
        return $operators;
    }

    public function get_condition_input_type(): string {
        return 'Text';
    }

    public function is_match( $rule_data, $arguments = null ): bool {
        global $post;
        $result  = false;
        $product = wc_get_product( get_the_ID() );
        if ( $product && isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
            $stock = $product->get_stock_quantity();
            $value = (float) $rule_data['condition'];

            switch ( $rule_data['operator'] ) {
                case '==':
                    $result = $stock == $value;
                    break;
                case '!=':
                    $result = $stock != $value;
                    break;
                case '>':
                    $result = $stock > $value;
                    break;
                case '<':
                    $result = $stock < $value;
                    break;
                case '>=':
                    $result = $stock >= $value;
                    break;
                case '<=':
                    $result = $stock <= $value;
                    break;
                default:
                    $result = false;
                    break;
            }
        }

        return $this->return_is_match( $result, $rule_data, $arguments );
    }
}
