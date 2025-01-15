<td class="rule-type">
    <?php
    // Allow custom location rules
    $types = apply_filters( 'wc_conditional_content_get_rule_types', [] );

    // Create field
    $args = [
        'input'   => 'select',
        'name'    => 'wccc_rule[<%= groupId %>][<%= ruleId %>][rule_type]',
        'class'   => 'rule_type',
        'choices' => $types,
    ];

    WC_Conditional_Content_Input_Builder::create_input_field( $args, 'general_always' );
    ?>
</td>

<?php
WC_Conditional_Content_Admin_Controller::instance()->render_rule_choice_template(
    [
        'group_id'  => 0,
        'rule_id'   => 0,
        'rule_type' => 'general_always',
        'condition' => false,
        'operator'  => false,
    ]
);
?>
<td class="loading" colspan="2" style="display:none;"><?php _e( 'Loading...', 'woocommerce-conditional-blocks' ); ?></td>
<td class="add"><a href="#" class="wccc-add-rule button"><?php _e( 'and', 'woocommerce-conditional-blocks' ); ?></a></td>
<td class="remove"><a href="#" class="wccc-remove-rule wccc-button-remove" <?php _e( 'Remove condition', 'woocommerce-conditional-blocks' ); ?>><span class="dashicons dashicons-trash"></span></a></td>
