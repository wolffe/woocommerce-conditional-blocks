<?php
global $post;

$settings = get_post_meta( $post->ID, '_wccc_settings', true );
if ( ! $settings ) {
    $settings = [
        'location' => 'single-product',
        'hook'     => 'woocommerce_template_single_excerpt',
    ];
}

$locations = apply_filters( 'wc_conditional_content_get_locations', [] );

if ( ! isset( $settings['type'] ) ) {
    $settings['type'] = 'single';
}

if ( ! isset( $settings['accepted_args'] ) ) {
    $settings['accepted_args'] = 1;
}
?>

<h4><?php _e( 'Show Block(s)', 'woocommerce-conditional-blocks' ); ?></h4>

<label for="wccc_settings_type"><?php _e( 'How?', 'woocommerce-conditional-blocks' ); ?></label><br>
<select name="wccc_settings_type" id="wccc_settings_type">
    <option value="single" <?php selected( $settings['type'], 'single' ); ?>><?php _e( 'Once', 'woocommerce-conditional-blocks' ); ?></option>
    <option value="loop" <?php selected( $settings['type'], 'loop' ); ?>><?php _e( 'In Loop', 'woocommerce-conditional-blocks' ); ?></option>
</select>
<p class="description"><?php _e( 'Are the blocks displayed in a loop?.', 'woocommerce-conditional-blocks' ); ?></p>

<label for="wccc_settings_location"><?php _e( 'Where?', 'woocommerce-conditional-blocks' ); ?></label><br />
<select name="wccc_settings_location" id="wccc_settings_location">

    <?php foreach ( $locations as $location => $data ) : ?>
        <optgroup label="<?php echo $data['title']; ?>">
            <?php foreach ( $data['hooks'] as $hook_id => $hook ) : ?>
                <option <?php selected( $location . ':' . $hook_id, $settings['location'] . ':' . $settings['hook'] ); ?> value="<?php echo $location . ':' . $hook_id; ?>"><?php echo $hook['title']; ?></option>
            <?php endforeach; ?>
        </optgroup>
    <?php endforeach; ?>

    <optgroup label="<?php _e( 'Custom', 'woocommerce-conditional-blocks' ); ?>">
        <option <?php selected( 'custom', $settings['hook'] ); ?> value="custom:custom"><?php _e( 'Custom Action', 'woocommerce-conditional-blocks' ); ?></option>
    </optgroup>
</select>

<p class="description"><?php _e( 'Where would you like these blocks to be displayed?', 'woocommerce-conditional-blocks' ); ?></p>

<div class="wccc-settings-custom">
    <label for="wccc_settings_location_custom_hook"><?php _e( 'Action Hook Name', 'woocommerce-conditional-blocks' ); ?></label>
    <br><input type="text" name="wccc_settings_location_custom_hook" value="<?php echo esc_attr( $settings['hook'] == 'custom' ? $settings['custom_hook'] : '' ); ?>">
    <br><label for="wccc_settings_location_custom_priority"><?php _e( 'Priority', 'woocommerce-conditional-blocks' ); ?></label>
    <br><input type="text" name="wccc_settings_location_custom_priority" value="<?php echo esc_attr( $settings['hook'] == 'custom' ? $settings['custom_priority'] : '' ); ?>">
    <p class="description"><?php printf( __( 'Enter the name and priority of an action where this content should be displayed. See the <a href="%s">WooCommerce hooks and filter reference</a> for a full list of all template actions and filters.', 'woocommerce-conditional-blocks' ), 'https://woocommerce.com/document/introduction-to-hooks-actions-and-filters/' ); ?></p>
    <br><label for="wccc_settings_location_custom_accepted_args"><?php _e( 'Accepted Function Args', 'woocommerce-conditional-blocks' ); ?></label>
    <br><input type="number" name="wccc_settings_location_custom_accepted_args" value="<?php echo esc_attr( $settings['hook'] == 'custom' ? ( empty( $settings['custom_accepted_args'] ) ? '1' : $settings['custom_accepted_args'] ) : '0' ); ?>">
    <p class="description"><?php _e( 'Optional. The number of arguments the function accepts. Default 0.', 'woocommerce-conditional-blocks' ); ?></p>
</div>
