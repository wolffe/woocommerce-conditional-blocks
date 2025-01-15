<?php
class WC_Conditional_Content_Rule_Store_Order_Count extends WC_Conditional_Content_Rule_Base {

    public function __construct() {
        parent::__construct( 'store_order_count' );
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
        return 'Order_Status';
    }

    public function is_match( $rule_data, $arguments = null ): bool {
        $result = false;
        if ( isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
            $value  = $rule_data['condition']['qty'];
            $status = $rule_data['condition']['status'];

            $count = $this->get_order_count( $status );

            switch ( $rule_data['operator'] ) {
                case '==':
                    $result = $count == $value;
                    break;
                case '!=':
                    $result = $count != $value;
                    break;
                case '>':
                    $result = $count > $value;
                    break;
                case '<':
                    $result = $count < $value;
                    break;
                case '>=':
                    $result = $count >= $value;
                    break;
                case '<=':
                    $result = $count <= $value;
                    break;
                default:
                    $result = false;
                    break;
            }
        }

        return $this->return_is_match( $result, $rule_data, $arguments );
    }

    private function get_order_count( $status ) {
        return count(
            wc_get_orders(
                [
                    'status' => $status,
                    'return' => 'ids',
                    'limit'  => - 1,
                ]
            )
        );
    }
}


class WC_Conditional_Content_Rule_Store_Order_History extends WC_Conditional_Content_Rule_Base {

    public function __construct() {
        parent::__construct( 'store_order_history' );
    }

    public function get_possible_rule_operators(): array {
        $operators = [
            'in'    => __( 'has purchased', 'woocommerce-conditional-blocks' ),
            'notin' => __( 'has not purchased', 'woocommerce-conditional-blocks' ),
        ];

        return $operators;
    }

    public function get_condition_input_type(): string {
        return 'Product_Select';
    }

    public function is_match( $rule_data, $arguments = null ): bool {
        $result                 = false;
        $customer_billing_email = null;

        // check if this user purchased a product as a guest user.
        if ( get_current_user_id() === 0 ) {
            try {
                $customer = new WC_Customer( get_current_user_id(), true );

                // since billing is required use that for the purchase check.
                $customer_billing_email = $customer->get_billing_email();
            } catch ( Exception $e ) {

            }
        }

        $bought = false;
        if ( isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
            foreach ( $rule_data['condition'] as $product_id ) {

                // check for variation parent, since those can be selected in the UI.
                $product = wc_get_product( $product_id );
                if ( $product->is_type( 'variation' ) ) {
                    if ( wc_customer_bought_product( $customer_billing_email, get_current_user_id(), $product->get_parent_id() ) ) {
                        $bought = true;
                    }
                }

                // check for the specific product, variation or not.
                if ( wc_customer_bought_product( $customer_billing_email, get_current_user_id(), $product_id ) ) {
                    $bought = true;
                }
            }
            $result = $rule_data['operator'] == 'in' ? $bought : ! $bought;
        }

        return $this->return_is_match( $result, $rule_data, $arguments );
    }
}
