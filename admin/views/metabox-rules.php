<?php
WC_Conditional_Content::nonce_field( 'admin' );

global $post;

$groups = get_post_meta( $post->ID, 'wccc_rule', true );

// At lease 1 location rule
if ( empty( $groups ) ) {
    $default_rule_id = 'rule' . uniqid();
    $groups          = [
        'group0' => [
            $default_rule_id => [
                'rule_type' => 'general_always',
                'operator'  => '==',
                'condition' => '',
            ],
        ],
    ];
}
?>

<div class="wccc-rules-builder woocommerce_options_panel_disabled">
    <p><?php _e( 'Define a set of rules and conditions to control when the blocks above should be displayed.', 'woocommerce-conditional-blocks' ); ?></p>

    <div id="wccc-rules-groups">
        <div class="wccc-rule-group-target">
            <?php
            if ( is_array( $groups ) ) {
                $group_counter = 0;

                foreach ( $groups as $group_id => $group ) {
                    if ( empty( $group_id ) ) {
                        $group_id = 'group' . $group_id;
                    }
                    ?>

                    <div class="wccc-rule-group-container" data-groupid="<?php echo $group_id; ?>">
                        <div class="wccc-rule-group-header">
                            <?php if ( (int) $group_counter === 0 ) { ?>
                                <h4><?php _e( 'Show these blocks when the conditions below are met:', 'woocommerce-conditional-blocks' ); ?></h4>
                            <?php } else { ?>
                                <h4><?php _e( 'or:', 'woocommerce-conditional-blocks' ); ?></h4>
                            <?php } ?>
                            <a href="#" class="wccc-remove-rule-group button"><?php _e( 'Remove', 'woocommerce-conditional-blocks' ); ?></a>
                        </div>

                        <?php if ( is_array( $group ) ) { ?>
                            <table class="wccc-rules" data-groupid="<?php echo $group_id; ?>">
                                <tbody>
                                <?php
                                foreach ( $group as $rule_id => $rule ) {
                                    if ( empty( $rule_id ) ) {
                                        $rule_id = 'rule' . $rule_id;
                                    }
                                    ?>
                                    <tr data-ruleid="<?php echo $rule_id; ?>" class="wccc-rule">
                                        <td class="rule-type">
                                        <?php
                                            // Allow custom location rules
                                            $types = apply_filters( 'wc_conditional_content_get_rule_types', [] );

                                            // Create field
                                            $args = [
                                                'input'   => 'select',
                                                'name'    => 'wccc_rule[' . $group_id . '][' . $rule_id . '][rule_type]',
                                                'class'   => 'rule_type',
                                                'choices' => $types,
                                            ];

                                            WC_Conditional_Content_Input_Builder::create_input_field( $args, $rule['rule_type'] ?? 'general_always' );
                                            ?>
                                        </td>

                                        <?php
                                        WC_Conditional_Content_Admin_Controller::instance()->ajax_render_rule_choice(
                                            [
                                                'group_id' => $group_id,
                                                'rule_id'  => $rule_id,
                                                'rule_type' => $rule['rule_type'] ?? 'general_always',
                                                'condition' => $rule['condition'] ?? false,
                                                'operator' => $rule['operator'] ?? false,
                                            ]
                                        );
                                        ?>
                                        <td class="loading" colspan="2" style="display:none;"><?php _e( 'Loading...', 'woocommerce-conditional-blocks' ); ?></td>
                                        <td class="add">
                                            <a href="#" class="wccc-add-rule button"><?php _e( 'and', 'woocommerce-conditional-blocks' ); ?></a>
                                        </td>
                                        <td class="remove">
                                            <div>
                                                <a href="#" class="wccc-remove-rule wccc-button-remove" title="<?php _e( 'Remove condition', 'woocommerce-conditional-blocks' ); ?>"><span class="dashicons dashicons-trash"></span></a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                            <?php
                        }
                        ?>
                    </div>

                    <?php
                    ++$group_counter;
                }
                ?>

                <p class="or" style="<?php echo( $group_counter > 1 ? 'display:block;' : 'display:none' ); ?>"><?php _e( '&mdash; or when the rules and conditions below are met &mdash;', 'woocommerce-conditional-blocks' ); ?></p>
                <button class="button button-primary wccc-add-rule-group" title="<?php _e( 'Add a set of conditions', 'woocommerce-conditional-blocks' ); ?>"><?php _e( 'Add Rule Set', 'woocommerce-conditional-blocks' ); ?></button>
                <?php
            }
            ?>
        </div>
    </div>
</div>

<script type="text/template" id="wccc-rule-template">
    <?php require 'metabox-rules-rule-template.php'; ?>
</script>
