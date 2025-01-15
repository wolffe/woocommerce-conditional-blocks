<?php


class WC_Conditional_Content_Input_Order_Status extends WC_Conditional_Content_Input_Base {

    public function __construct() {
        // vars
        $this->type = 'Order_Status';

        $this->defaults = [
            'multiple'      => 0,
            'allow_null'    => 0,
            'choices'       => [],
            'default_value' => '',
            'class'         => '',
            'placeholder'   => '',
        ];
    }

    public function render( $field, $value = null ): void {
        $field = array_merge( $this->defaults, $field );
        if ( ! isset( $field['id'] ) ) {
            $field['id'] = sanitize_title( $field['id'] );
        }

        $value['status'] = $value['status'] ?? '';

        ?>

        <table style="width:100%;">
            <tr>
                <td><?php esc_html_e( 'Order Count', 'woocommerce-conditional-blocks' ); ?></td>
                <td style="width:162px;"><?php esc_html_e( 'Order Status', 'woocommerce-conditional-blocks' ); ?></td>
            </tr>
            <tr>
                <td style="width:32px">
                    <input
                            aria-label="<?php esc_attr_e( 'Quantity', 'woocommerce-conditional-blocks' ); ?>"
                            type="text" id="<?php echo esc_attr( $field['id'] ); ?>_qty"
                            name="<?php echo esc_attr( $field['name'] ); ?>[qty]"
                            value="<?php echo esc_attr( $value['qty'] ?? 1 ); ?>"/>
                </td>
                <td>
                    <?php echo '<select style="min-height:40px;" id="' . esc_attr( $field['id'] ) . '" class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['name'] ) . '[status]">'; ?>
                    <?php
                    $sts = wc_get_order_statuses();
                    foreach ( $sts as $key => $status ) {
                        echo '<option ' . selected( $value['status'], $key ) . ' value="' . esc_attr( $key ) . '">' . esc_html( $status ) . '</option>';
                    }
                    ?>

                    <?php echo '</select>'; ?>

                </td>
            </tr>
        </table>
        <?php
    }
}
