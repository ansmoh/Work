<?php
/**
 * Email Addresses
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-addresses.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><table id="addresses" cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top;" border="0">
	<tr><?php if($order->get_formatted_billing_address()){ ?>
		<td class="td" style="text-align:left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" valign="top" width="50%">
			<h3><?php _e( 'Shipping address', 'woocommerce' ); ?></h3>

			<p class="text"><?php echo $order->get_formatted_billing_address(); ?></p>
		</td>
		<?php }if($order->billing_myfield16){?>
			<td class="td" style="text-align:left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" valign="top" width="50%">
				<h3>Billing Address</h3>

				<p class="text"><?php  if($order->billing_myfield17){
								echo $order->billing_myfield17.'<br />';
								}
							if($order->billing_myfield18c){
								echo $order->billing_myfield18c.'<br />';
								}
							if($order->billing_myfield19c){
								echo $order->billing_myfield19c.'<br />';
								}
							if($order->billing_myfield20){
								echo $order->billing_myfield20.'<br />';
								}
							if($order->billing_myfield21){
								echo $order->billing_myfield21.'<br />';
								}
							if($order->billing_myfield22){
								echo $order->billing_myfield22.'<br />';
								}	
							if($order->billing_myfield23){
								echo $order->billing_myfield23.'<br />';
								}					
							if($order->billing_myfield24){
								echo $order->billing_myfield24;
								}	 ?></p>
			</td>
		<?php } ?>
	</tr>
</table>

		