<?php
class WC_Conditional_Content_Rule_Schedule_Date extends WC_Conditional_Content_Rule_Base {

    public function __construct() {
        parent::__construct( 'schedule_date' );
    }

    public function get_possible_rule_operators(): array {
        $operators = [
            '>=' => __( 'starts', 'woocommerce-conditional-blocks' ),
            '<=' => __( 'ends', 'woocommerce-conditional-blocks' ),
        ];

        return $operators;
    }

    public function get_condition_input_type(): string {
        return 'Date';
    }

    public function is_match( $rule_data, $arguments = null ): bool {
        $result = false;
        if ( isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {

            $date = strtotime( $rule_data['condition'] );

            switch ( $rule_data['operator'] ) {
                case '>=':
                    $result = strtotime( date( 'Y-m-d' ) ) >= $date;
                    break;
                case '<=':
                    $result = strtotime( date( 'Y-m-d' ) ) <= $date;
                    break;
                default:
                    $result = false;
                    break;
            }
        }

        return $this->return_is_match( $result, $rule_data, $arguments );
    }
}


class WC_Conditional_Content_Rule_Schedule_Day extends WC_Conditional_Content_Rule_Base {

    public function __construct() {
        parent::__construct( 'schedule_day' );
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
            '0' => __( 'Sunday', 'woocommerce-conditional-blocks' ),
            '1' => __( 'Monday', 'woocommerce-conditional-blocks' ),
            '2' => __( 'Tuesday', 'woocommerce-conditional-blocks' ),
            '3' => __( 'Wednesday', 'woocommerce-conditional-blocks' ),
            '4' => __( 'Thursday', 'woocommerce-conditional-blocks' ),
            '5' => __( 'Friday', 'woocommerce-conditional-blocks' ),
            '6' => __( 'Saturday', 'woocommerce-conditional-blocks' ),
        ];

        return $options;
    }


    public function get_condition_input_type(): string {
        return 'Select';
    }

    public function is_match( $rule_data, $arguments = null ): bool {
        $result = false;
        if ( isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {

            $date = intval( $rule_data['condition'] );

            switch ( $rule_data['operator'] ) {
                case '==':
                    $result = date( 'w' ) == $date;
                    break;
                case '!=':
                    $result = date( 'w' ) != $date;
                    break;
                default:
                    $result = false;
                    break;
            }
        }

        return $this->return_is_match( $result, $rule_data, $arguments );
    }
}


class WC_Conditional_Content_Rule_Schedule_Time extends WC_Conditional_Content_Rule_Base {

    public function __construct() {
        parent::__construct( 'schedule_time' );
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
        $result = false;
        if ( isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
            $time = strtotime( $rule_data['condition'] );
            $now  = strtotime( date_i18n( 'H:i:s' ) );
            switch ( $rule_data['operator'] ) {
                case '==':
                    $result = $time == $now;
                    break;
                case '!=':
                    $result = $time != $now;
                    break;
                case '>':
                    $result = $time < $now;
                    break;
                case '<':
                    $result = $now < $time;
                    break;
                case '>=':
                    $result = $time <= $now;
                    break;
                case '<=':
                    $result = $now <= $time;
                    break;
                default:
                    $result = false;
                    break;
            }
        }

        return $this->return_is_match( $result, $rule_data, $arguments );
    }
}
