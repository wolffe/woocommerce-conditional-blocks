<?php
class WC_Conditional_Content_Input_Html_Always extends WC_Conditional_Content_Input_Base {
    public function __construct() {
        $this->type = 'Html_Always';

        $this->defaults = [
            'default_value' => '',
            'class'         => '',
            'placeholder'   => '',
        ];
    }

    public function render( $field, $value = null ): void {
        esc_html_e( 'Blocks will always display for all shoppers on your site. This will override any other rule you define.', 'woocommerce-conditional-blocks' );
    }
}
