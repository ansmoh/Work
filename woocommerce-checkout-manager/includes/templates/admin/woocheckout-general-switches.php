<div class="widefat general-semi switches" border="1" style="display:none;">

	<div class="section">
		<h3 class="heading"><?php _e('General Switches', 'woocommerce-checkout-manager'); ?></h3>
	</div>
	<!-- .section -->

	<div class="section">
		<h3 class="heading checkbox">  

			<div class="option">
				<label>
					<input type="checkbox" name="wccs_settings[checkness][additional_info]" value="true"<?php checked( !empty( $options['checkness']['additional_info'] ), true ); ?> />
					<div class="info-of"><?php printf( __('Hide %s heading', 'woocommerce-checkout-manager'), __( 'Ship to a different address?', 'woocommerce' ) );  ?></div>
				</label>
			</div>
			<!-- .option -->

		</h3>
	</div>
	<!-- section -->

	<div class="section">
		<h3 class="heading checkbox">  

			<div class="option">
				<label>
					<input type="checkbox" name="wccs_settings[checkness][show_shipping_fields]" value="true"<?php checked( !empty( $options['checkness']['show_shipping_fields'] ), true ); ?> />
					<div class="info-of"><?php printf( __('Force show Billing fields (to be used in conjunction with %s)', 'woocommerce-checkout-manager'), sprintf( __('Hide %s heading', 'woocommerce-checkout-manager'), __( 'Ship to a different address?', 'woocommerce' ) ) );  ?></div>
				</label>
			</div>
			<!-- .option -->

		</h3>
	</div>
	<!-- section -->

	<div class="section">
		<h3 class="heading checkbox">

			<div class="option">
				<label>
					<input type="checkbox" name="wccs_settings[checkness][auto_create_wccm_account]" value="true"<?php checked( !empty( $options['checkness']['auto_create_wccm_account'] ), true ); ?> />
					<div class="info-of"><?php printf( __( 'Hide %s checkbox on Checkout page for guests', 'woocommerce-checkout-manager' ), __( 'Create an account?', 'woocommerce' ) );  ?></div>
				</label>
			</div>
			<!-- .option -->

		</h3>
	</div>
	<!-- section -->

	<div class="section">
		<h3 class="heading checkbox">

			<div class="option">
				<label>
					<input type="checkbox" name="wccs_settings[checkness][retainval]" value="true"<?php checked( !empty( $options['checkness']['retainval'] ), true ); ?> />
					<div class="info-of"><?php _e('Retain Fields Information', 'woocommerce-checkout-manager');  ?></div>
				</label>
			</div>
			<!-- .option -->

		</h3>
	</div>
	<!-- section -->

	<div class="section">
		<h3 class="heading checkbox">

			<div class="option">
				<label>
					<input type="checkbox" name="wccs_settings[checkness][abbreviation]" value="true"<?php checked( !empty( $options['checkness']['abbreviation'] ), true ); ?> />
					<div class="info-of"><?php _e('Editing Of Abbreviation Fields', 'woocommerce-checkout-manager');  ?></div>
				</label>
			</div>
			<!-- .option -->

		</h3>
	</div>
	<!-- section -->

	<div class="section">
		<h3 class="heading checkbox">

			<div class="option">
				<label>
					<input type="checkbox" name="wccs_settings[checkness][admin_translation]" value="true"<?php checked( !empty( $options['checkness']['admin_translation'] ), true ); ?> />
					<div class="info-of"><?php _e('Translate WooCommerce Checkout Manager Options Panel', 'woocommerce-checkout-manager');  ?></div>
				</label>
			</div>
			<!-- .option -->

		</h3>
	</div>
	<!-- section -->

	<div class="section">
		<h3 class="heading"><?php _e('Additional Fields Position on Checkout', 'woocommerce-checkout-manager'); ?></h3>
	</div>
	<!-- .section -->

	<div class="section">
		<h3 class="heading checkbox radio">

			<div class="option">
				<label>
					<input type="radio" name="wccs_settings[checkness][position]" value="before_shipping_form"<?php checked( sanitize_text_field( $options['checkness']['position'] ), 'before_shipping_form' ); ?> />
					<div class="info-of"><?php _e('Before Shipping fields', 'woocommerce-checkout-manager');  ?></div>
				</label>
			</div>
			<!-- .option -->

		</h3>
	</div>
	<!-- section -->

	<div class="section">
		<h3 class="heading checkbox radio">

			<div class="option">
				<label>
					<input type="radio" name="wccs_settings[checkness][position]" value="after_shipping_form"<?php checked( sanitize_text_field( $options['checkness']['position'] ), 'after_shipping_form' ); ?> />
					<div class="info-of"><?php _e('After Shipping fields', 'woocommerce-checkout-manager');  ?></div>
				</label>
			</div>
			<!-- .option -->

		</h3>
	</div>
	<!-- section -->

	<div class="section">
		<h3 class="heading checkbox radio">

			<div class="option">
				<label>
					<input type="radio" name="wccs_settings[checkness][position]" value="before_billing_form"<?php checked( sanitize_text_field( $options['checkness']['position'] ), 'before_billing_form' ); ?> />
					<div class="info-of"><?php _e('Before Billing fields', 'woocommerce-checkout-manager');  ?></div>
				</label>
			</div>
			<!-- .option -->

		</h3>
	</div>
	<!-- section -->

	<div class="section">
		<h3 class="heading checkbox radio">

			<div class="option">
				<label>
					<input type="radio" name="wccs_settings[checkness][position]" value="after_billing_form"<?php checked( sanitize_text_field( $options['checkness']['position'] ), 'after_billing_form' ); ?> />
					<div class="info-of"><?php _e('After Billing fields', 'woocommerce-checkout-manager');  ?></div>
				</label>
			</div>
			<!-- .option -->

		</h3>
	</div>
	<!-- section -->

	<div class="section">
		<h3 class="heading checkbox radio">

			<div class="option">
				<label>
					<input type="radio" name="wccs_settings[checkness][position]" value="after_order_notes"<?php checked( ( $options['checkness']['position'] == false ? 'after_order_notes' : sanitize_text_field( $options['checkness']['position'] ) ), 'after_order_notes' ); ?> />
					<div class="info-of"><?php _e('After Order Notes', 'woocommerce-checkout-manager');  ?> (<?php _e( 'Default', 'woocommerce-checkout-manager' ); ?>)</div>
				</label>
			</div>
			<!-- .option -->

		</h3>
	</div>
	<!-- section -->

</div>
<!-- .switches -->