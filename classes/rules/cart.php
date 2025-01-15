<?php
class WC_Conditional_Content_Rule_Cart_Total extends WC_Conditional_Content_Rule_Base {

    public function __construct() {
        parent::__construct( 'cart_total' );
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

        $result = false;
        if ( ! wc_prices_include_tax() ) {
            $price = WC()->cart->get_cart_contents_total();
        } else {
            $price = WC()->cart->get_cart_contents_total() + WC()->cart->get_cart_contents_tax();
        }

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

        return $this->return_is_match( $result, $rule_data, $arguments );
    }
}

class WC_Conditional_Content_Rule_Cart_Quantity extends WC_Conditional_Content_Rule_Base {

    public function __construct() {
        parent::__construct( 'cart_quantity' );
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

        $cart_contents  = WC()->cart->get_cart();
        $found_quantity = 0;
        if ( $cart_contents && count( $cart_contents ) ) {
            foreach ( $cart_contents as $cart_item_key => $cart_item ) {
                $found_quantity += $cart_item['quantity'];
            }
        }

        $value = (float) $rule_data['condition'];
        switch ( $rule_data['operator'] ) {
            case '==':
                $result = $found_quantity == $value;
                break;
            case '!=':
                $result = $found_quantity != $value;
                break;
            case '>':
                $result = $found_quantity > $value;
                break;
            case '<':
                $result = $found_quantity < $value;
                break;
            case '>=':
                $result = $found_quantity >= $value;
                break;
            case '<=':
                $result = $found_quantity <= $value;
                break;
            default:
                $result = false;
                break;
        }

        return $this->return_is_match( $result, $rule_data, $arguments );
    }
}

class WC_Conditional_Content_Rule_Cart_Product extends WC_Conditional_Content_Rule_Base {

    public function __construct() {
        parent::__construct( 'cart_product' );
    }

    public function get_possible_rule_operators(): array {

        $operators = [
            '<'  => __( 'contains less than', 'woocommerce-conditional-blocks' ),
            '>'  => __( 'contains at least', 'woocommerce-conditional-blocks' ),
            '==' => __( 'contains exactly', 'woocommerce-conditional-blocks' ),
        ];

        return $operators;
    }

    public function get_condition_input_type(): string {
        return 'Cart_Product_Select';
    }

    public function is_match( $rule_data, $arguments = null ): bool {
        $result        = false;
        $cart_contents = WC()->cart->get_cart();

        $products = $rule_data['condition']['products'];
        $quantity = $rule_data['condition']['qty'];
        $type     = $rule_data['operator'];

        $found_quantity = 0;

        if ( $cart_contents && count( $cart_contents ) ) {
            foreach ( $cart_contents as $cart_item_key => $cart_item ) {
                if ( in_array( '0', $products ) ) {
                    $found_quantity += $cart_item['quantity'];
                } elseif ( in_array( $cart_item['product_id'], $products ) || ( isset( $cart_item['variation_id'] ) && in_array( $cart_item['variation_id'], $products ) ) ) {
                    $found_quantity += $cart_item['quantity'];
                }
            }
        }

        switch ( $type ) {
            case '<':
                $result = $quantity >= $found_quantity;
                break;
            case '>':
                $result = $quantity <= $found_quantity;
                break;
            case '==':
                $result = $quantity == $found_quantity;
                break;
            default:
                $result = false;
                break;
        }

        return $this->return_is_match( $result, $rule_data, $arguments );
    }
}

class WC_Conditional_Content_Rule_Cart_Category extends WC_Conditional_Content_Rule_Base {

    public function __construct() {
        parent::__construct( 'cart_category' );
    }

    public function get_possible_rule_operators(): array {

        $operators = [
            '<'  => __( 'contains less than', 'woocommerce-conditional-blocks' ),
            '>'  => __( 'contains at least', 'woocommerce-conditional-blocks' ),
            '==' => __( 'contains exactly', 'woocommerce-conditional-blocks' ),
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
        return 'Cart_Category_Select';
    }

    public function is_match( $rule_data, $arguments = null ): bool {
        $result        = false;
        $cart_contents = WC()->cart->get_cart();

        $categories = $rule_data['condition']['categories'];
        $quantity   = $rule_data['condition']['qty'];
        $type       = $rule_data['operator'];

        $found_quantity = 0;

        if ( $cart_contents && count( $cart_contents ) ) {
            foreach ( $cart_contents as $cart_item_key => $cart_item ) {
                $product = $cart_item['data'];

                if ( $product->is_type( 'variation' ) ) {
                    $product = wc_get_product( $product->get_parent_id() );
                }

                $terms = $product->get_category_ids();
                if ( $terms && ! is_wp_error( $terms ) && count( array_intersect( $terms, $categories ) ) > 0 ) {
                    $found_quantity += $cart_item['quantity'];
                }
            }
        }

        switch ( $type ) {
            case '<':
                $result = $quantity > $found_quantity;
                break;
            case '>':
                $result = $quantity <= $found_quantity;
                break;
            case '==':
                $result = $quantity == $found_quantity;
                break;
            default:
                $result = false;
                break;
        }

        return $this->return_is_match( $result, $rule_data, $arguments );
    }
}

class WC_Conditional_Content_Rule_Cart_Line_Item_Product extends WC_Conditional_Content_Rule_Base {

    public function __construct() {
        parent::__construct( 'cart_line_item_product' );
    }

    public function get_possible_rule_operators(): array {
        $operators = [
            'in'    => __( 'in', 'woocommerce-conditional-blocks' ),
            'notin' => __( 'not in', 'woocommerce-conditional-blocks' ),
        ];

        return $operators;
    }

    public function get_condition_input_type(): string {
        return 'Product_Select';
    }

    public function is_match( $rule_data, $arguments = null ): bool {
        $result  = false;
        $product = ! empty( $arguments ) && isset( $arguments[0] ) && isset( $arguments[0]['data'] ) ? $arguments[0]['data'] : false;
        if ( $product && isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
            $in     = in_array( $product->get_id(), $rule_data['condition'] );
            $result = $rule_data['operator'] == 'in' ? $in : ! $in;
        }

        return $this->return_is_match( $result, $rule_data, $arguments );
    }
}

class WC_Conditional_Content_Rule_Cart_Line_Item_Quantity extends WC_Conditional_Content_Rule_Base {

    public function __construct() {
        parent::__construct( 'cart_line_item_quantity' );
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
        $result         = false;
        $quantity       = $rule_data['condition']; //The quantity input.
        $type           = $rule_data['operator'];
        $found_quantity = 0;
        if ( ! empty( $arguments ) && isset( $arguments[0] ) && isset( $arguments[0]['quantity'] ) ) {
            $found_quantity = $arguments[0]['quantity'];
        }

        switch ( $rule_data['operator'] ) {
            case '==':
                $result = $found_quantity == $quantity;
                break;
            case '!=':
                $result = $found_quantity != $quantity;
                break;
            case '>':
                $result = $found_quantity > $quantity;
                break;
            case '<':
                $result = $found_quantity < $quantity;
                break;
            case '>=':
                $result = $found_quantity >= $quantity;
                break;
            case '<=':
                $result = $found_quantity <= $quantity;
                break;
            default:
                $result = false;
                break;
        }

        return $this->return_is_match( $result, $rule_data, $arguments );
    }
}
