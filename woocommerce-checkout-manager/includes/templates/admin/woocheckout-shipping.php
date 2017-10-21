<table class="widefat shipping-wccs-table shipping-semi" style="display:none;" border="1" name="shipping_table">
	<thead>

		<tr>
			<th style="width:3%;" class="shipping-wccs-order" title="<?php esc_attr_e( 'Change order' , 'woocommerce-checkout-manager' ); ?>">#</th>

			<?php require( WOOCCM_PATH.'includes/templates/htmlheadship.php' ); ?>

			<th width="1%" scope="col" title="<?php esc_attr_e( 'Remove button', 'woocommerce-checkout-manager' ); ?>"><strong>X</strong><!-- remove --></th>
		</tr>

	</thead>
	<tbody>

<?php
	if ( isset ( $options2['shipping_buttons'] ) ) {
		$shipping = array(
			'country', 
			'first_name', 
			'last_name', 
			'company', 
			'address_1', 
			'address_2', 
			'city', 
			'state', 
			'postcode'
		);
		for ( $ix = 0; $ix < count( $options2['shipping_buttons'] ); $ix++ ) {

			if ( ! isset( $options2['shipping_buttons'][$ix] ) )
				break;
?>

		<tr valign="top" class="shipping-wccs-row">

			<td style="display:none;" class="shipping-wccs-order-hidden">
				<input type="hidden" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][order]" value="<?php echo (empty( $options2['shipping_buttons'][$ix]['order'])) ? $ix :  $options2['shipping_buttons'][$ix]['order']; ?>" />
			</td>
			<td class="shipping-wccs-order" title="<?php esc_attr_e( 'Change order', 'woocommerce-checkout-manager' ); ?>"><?php echo $ix+1; ?></td>

				<?php require(WOOCCM_PATH.'includes/templates/htmlbodyship.php'); ?>

<?php if( in_array( $options2['shipping_buttons'][$ix]['cow'],$shipping) ) { ?>
			<td style="text-align:center;">
				<input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][disabled]" type="checkbox" value="true" <?php if (  !empty ($options2['shipping_buttons'][$ix]['disabled'])) echo "checked='checked'"; ?> />
			</td>
<?php } else { ?>
			<td class="shipping-wccs-remove"><a class="shipping-wccs-remove-button" href="javascript:;" >&times;</a></td>
<?php } ?>

		</tr>

<?php
		}
	}
?>
<!-- Empty -->

<?php
	$ix = 999;
?>

		<tr valign="top" class="shipping-wccs-clone" >

			<td style="display:none;" class="shipping-wccs-order-hidden" >
				<input type="hidden" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][order]" value="<?php echo $ix; ?>" />
			</td>

			<td class="shipping-wccs-order" title="<?php esc_attr_e( 'Change order', 'woocommerce-checkout-manager' ); ?>"><?php echo $ix; ?></td>

			<?php require( WOOCCM_PATH.'includes/templates/htmlbodycloneship.php' ); ?>

			<td class="shipping-wccs-remove"><a class="shipping-wccs-remove-button" href="javascript:;">&times;</a></td>

		</tr>
	</tbody>
</table>
<!-- .widefat -->

<div class="shipping-wccs-table-footer shipping-semi" style="display:none;">
	<a href="javascript:;" id="shipping-wccs-add-button" class="button-secondary"><?php _e( '+ Add New Field' , 'woocommerce-checkout-manager' ); ?></a>
</div>
<!-- .shipping-wccs-table-footer -->