<?php

class WC_Conditional_Content_Input_Chosen_Select extends WC_Conditional_Content_Input_Base {

    public function __construct() {
        // vars
        $this->type = 'Chosen_Select';

        $this->defaults = [
            'multiple'      => 0,
            'allow_null'    => 0,
            'choices'       => [],
            'default_value' => [],
            'class'         => '',
        ];
    }

    public function render( $field, $value = null ): void {

        $field = array_merge( $this->defaults, $field );
        if ( ! isset( $field['id'] ) ) {
            $field['id'] = sanitize_title( $field['id'] );
        }

        $current = $value ?: [];
        $choices = $field['choices'];
        ?>

        <select aria-label="<?php _e( 'Select Product', 'woocommerce-conditional-blocks' ); ?>" id="<?php echo $field['id']; ?>" name="<?php echo $field['name']; ?>[]" class="wc-enhanced-select <?php echo esc_attr( $field['class'] ); ?>" multiple="multiple" data-placeholder="<?php echo ( $field['placeholder'] ?? __( 'Search...', 'woocommerce-conditional-blocks' ) ); ?>">
            <?php
            foreach ( $choices as $choice => $title ) {
                $selected = in_array( $choice, $current );
                echo '<option value="' . esc_attr( $choice ) . '" ' . selected( $selected, true, false ) . '>' . esc_html( $title ) . '</option>';
            }
            ?>
        </select>

        <?php
    }
}
?>
