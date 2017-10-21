<table class="widefat wccs-table additional-semi" style="display:none;" border="1" name="additional_table">
	<thead>

		<tr>
			<th style="width:3%;" class="wccs-order" title="<?php esc_attr_e( 'Change order' , 'woocommerce-checkout-manager' ); ?>">#</th>

			<?php require( WOOCCM_PATH.'includes/templates/htmlheadadd.php' ); ?>

			<th width="1%" scope="col" title="<?php esc_attr_e( 'Remove button', 'woocommerce-checkout-manager' ); ?>"><strong>X</strong><!-- remove --></th>
		</tr>

	</thead>
	<tbody>

<?php
	if ( isset ( $options['buttons'] ) ) {
		for ( $iz = 0; $iz < count( $options['buttons'] ); $iz++ ) {

			if ( ! isset( $options['buttons'][$iz] ) )
				break;
?>

		<tr valign="top" class="wccs-row">

			<td style="display:none;" class="wccs-order-hidden" >
				<input type="hidden" name="wccs_settings[buttons][<?php echo $iz; ?>][order]" value="<?php echo (empty( $options['buttons'][$iz]['order'])) ? $iz :  $options['buttons'][$iz]['order']; ?>" />
			</td>
			<td class="wccs-order" title="<?php esc_attr_e( 'Change order', 'woocommerce-checkout-manager' ); ?>"><?php echo $iz+1; ?></td>

			<?php require( WOOCCM_PATH.'includes/templates/htmlbodyadd.php' ); ?>

			<td class="wccs-remove"><a class="wccs-remove-button" href="javascript:;" title="<?php esc_attr_e( 'Remove Field' , 'woocommerce-checkout-manager' ); ?>">&times;</a></td>

		</tr>

<?php
		}
	}
?>
<!-- Empty -->

<?php
	$iz = 999;
?>

		<tr valign="top" class="wccs-clone" >

			<td style="display:none;" class="wccs-order-hidden">
				<input type="hidden" name="wccs_settings[buttons][<?php echo $iz; ?>][order]" value="<?php echo $iz; ?>" />
			</td>

			<td class="wccs-order" title="<?php esc_attr_e( 'Change order' , 'woocommerce-checkout-manager' ); ?>"><?php echo $iz; ?></td>

			<?php require( WOOCCM_PATH.'includes/templates/htmlbodycloneadd.php' ); ?>

			<td class="wccs-remove"><a class="wccs-remove-button" href="javascript:;" title="<?php esc_attr_e( 'Remove Field' , 'woocommerce-checkout-manager' ); ?>">&times;</a></td>

		</tr>
	</tbody>
</table>
<!-- .widefat -->

<div class="wccs-table-footer additional-semi" style="display:none;">
	<a href="javascript:;" id="wccs-add-button" class="button-secondary"><?php _e( '+ Add New Field' , 'woocommerce-checkout-manager' ); ?></a>
</div>
<!-- .wccs-table-footer -->