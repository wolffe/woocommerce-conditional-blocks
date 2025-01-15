<?php


class WC_Conditional_Content_Rule_Users_Authentication extends WC_Conditional_Content_Rule_Base {

    public function __construct() {
        parent::__construct( 'users_authentication' );
    }

    public function get_possible_rule_operators(): array {
        $operators = [
            '==' => __( 'is', 'woocommerce-conditional-blocks' ),
            '!=' => __( 'is not', 'woocommerce-conditional-blocks' ),
        ];

        return $operators;
    }

    public function get_possible_rule_values(): array {
        $result = [
            'logged-out' => 'Logged Out',
            'logged-in'  => 'Logged In',
        ];

        return $result;
    }

    public function get_condition_input_type(): string {
        return 'Select';
    }

    public function is_match( $rule_data, $arguments = null ): bool {
        $result = false;
        if ( $rule_data['condition'] ) {

            if ( $rule_data['condition'] == 'logged-in' ) {
                if ( $rule_data['operator'] == '==' ) {
                    $result = is_user_logged_in();
                } elseif ( $rule_data['operator'] == '!=' ) {
                    $result = ! is_user_logged_in();
                }
            } elseif ( $rule_data['condition'] == 'logged-out' ) {
                if ( $rule_data['operator'] == '==' ) {
                    $result = ! is_user_logged_in();
                } elseif ( $rule_data['operator'] == '!=' ) {
                    $result = is_user_logged_in();
                }
            }
        }

        return $this->return_is_match( $result, $rule_data, $arguments );
    }
}

class WC_Conditional_Content_Rule_Users_Role extends WC_Conditional_Content_Rule_Base {

    public function __construct() {
        parent::__construct( 'users_role' );
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

        $editable_roles = get_editable_roles();

        if ( $editable_roles ) {
            foreach ( $editable_roles as $role => $details ) {
                $name            = translate_user_role( $details['name'] );
                $result[ $role ] = $name;
            }
        }

        return $result;
    }

    public function get_condition_input_type(): string {
        return 'Chosen_Select';
    }

    public function is_match( $rule_data, $arguments = null ): bool {
        $result = false;
        if ( $rule_data['condition'] && is_array( $rule_data['condition'] ) ) {
            foreach ( $rule_data['condition'] as $role ) {
                $result |= current_user_can( $role );
            }
        }

        $result = $rule_data['operator'] == 'in' ? $result : ! $result;

        return $this->return_is_match( $result, $rule_data, $arguments );
    }

    public function sort_attribute_taxonomies( $taxa, $taxb ) {
        return strcmp( $taxa->attribute_name, $taxb->attribute_name );
    }
}

class WC_Conditional_Content_Rule_Users_User extends WC_Conditional_Content_Rule_Base {

    public function __construct() {
        parent::__construct( 'users_user' );
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

        $users = get_users();

        if ( $users ) {
            foreach ( $users as $user ) {
                $result[ $user->ID ] = $user->display_name;
            }
        }

        return $result;
    }

    public function get_condition_input_type(): string {
        return 'Chosen_Select';
    }

    public function is_match( $rule_data, $arguments = null ): bool {
        $result = in_array( get_current_user_id(), $rule_data['condition'] );
        $result = $rule_data['operator'] == 'in' ? $result : ! $result;

        return $this->return_is_match( $result, $rule_data, $arguments );
    }
}
