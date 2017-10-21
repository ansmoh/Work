<table class="widefat billing-wccs-table billing-semi" style="display:none;" border="1" name="billing_table">
	<thead>

		<tr>
			<th style="width:3%;" class="billing-wccs-order" title="<?php esc_attr_e( 'Change order' , 'woocommerce-checkout-manager' ); ?>">#</th>

			<?php require( WOOCCM_PATH.'includes/templates/htmlheadbill.php' ); ?>

			<th width="1%" scope="col" title="<?php esc_attr_e( 'Remove button', 'woocommerce-checkout-manager' ); ?>"><strong>X</strong><!-- remove --></th>
		</tr>

	</thead>
	<tbody>

<?php
	if ( isset ( $options3['billing_buttons'] ) ) {
		$billing = array(
			'country', 
			'first_name', 
			'last_name', 
			'company', 
			'address_1', 
			'address_2', 
			'city', 
			'state', 
			'postcode', 
			'email', 
			'phone'
		);
		for ( $i = 0; $i < count( $options3['billing_buttons'] ); $i++ ) {

			if ( ! isset( $options3['billing_buttons'][$i] ) )
				break;
?>

		<tr valign="top" class="billing-wccs-row">

			<td style="display:none;" class="billing-wccs-order-hidden" >
				<input type="hidden" name="wccs_settings3[billing_buttons][<?php echo $i; ?>][order]" value="<?php echo (empty( $options3['billing_buttons'][$i]['order'])) ? $i :  $options3['billing_buttons'][$i]['order']; ?>" />
			</td>
			<td class="billing-wccs-order" title="<?php esc_attr_e( 'Change order', 'woocommerce-checkout-manager' ); ?>"><?php echo $i+1; ?></td>

			<?php require( WOOCCM_PATH.'includes/templates/htmlbodybill.php' ); ?>

<?php if( in_array($options3['billing_buttons'][$i]['cow'], $billing) ) { ?>
			<td style="text-align:center;">
				<input name="wccs_settings3[billing_buttons][<?php echo $i; ?>][disabled]" type="checkbox" value="true" <?php if (  !empty ($options3['billing_buttons'][$i]['disabled'])) echo "checked='checked'"; ?> />
			</td>
<?php } else { ?>
			<td class="billing-wccs-remove"><a class="billing-wccs-remove-button" href="javascript:;">&times;</a></td>
<?php } ?>

		</tr>

<?php
		}
	}
?>
<!-- Empty -->

<?php
	$i = 999;
?>

		<tr valign="top" class="billing-wccs-clone" >

			<td style="display:none;" class="billing-wccs-order-hidden">
				<input type="hidden" name="wccs_settings3[billing_buttons][<?php echo $i; ?>][order]" value="<?php echo $i; ?>" />
			</td>

			<td class="billing-wccs-order" title="<?php esc_attr_e( 'Change order' , 'woocommerce-checkout-manager' ); ?>"><?php echo $i; ?></td>

			<?php require( WOOCCM_PATH.'includes/templates/htmlbodyclonebill.php' ); ?>

			<td class="billing-wccs-remove"><a class="billing-wccs-remove-button" href="javascript:;" >&times;</a></td>

		</tr>
	</tbody>
</table>
<!-- .widefat -->

<div class="billing-wccs-table-footer billing-semi" style="display:none;">
	<a href="javascript:;" id="billing-wccs-add-button" class="button-secondary"><?php _e( '+ Add New Field' , 'woocommerce-checkout-manager' ); ?></a>
</div>
<!-- .billing-wccs-table-footer -->