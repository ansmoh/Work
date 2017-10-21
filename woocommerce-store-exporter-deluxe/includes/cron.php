<?php
function woo_ce_cron_activation( $force_reload = false ) {

	// Check if we need to reload the scheduled export
	if( woo_ce_get_option( 'enable_auto', 0 ) == 0 || $force_reload )
		wp_clear_scheduled_hook( 'woo_ce_auto_export_schedule' );

	if( !wp_next_scheduled( 'woo_ce_auto_export_schedule' ) ) {

		$commence = sanitize_text_field( woo_ce_get_option( 'auto_commence', 'now' ) );
		switch( $commence ) {

			// Start initial export immediately
			case 'now':
				$time = current_time( 'timestamp', 1 );
				break;

			// Pass on a timestamp from the future
			case 'future':
				$commence_date = sanitize_text_field( woo_ce_get_option( 'auto_commence_date', date( 'd/m/Y H:i', current_time( 'timestamp', 1 ) ) ) );
				// Check if date is set
				if( !empty( $commence_date ) ) {
					$commence_date = strtotime( woo_ce_format_order_date( $commence_date ), current_time( 'timestamp', 1 ) );
					// Check if given date is in the future
					if( $commence_date > current_time( 'timestamp', 1 ) )
						$time = $commence_date;
					else
						$time = current_time( 'timestamp', 1 );
				} else {
					$time = current_time( 'timestamp', 1 );
				}
				break;

		}
		wp_schedule_event( $time, 'woo_ce_auto_interval', 'woo_ce_auto_export_schedule' );
	}

}

function woo_ce_cron_schedules( $schedules = array() ) {

	$schedule = sanitize_text_field( woo_ce_get_option( 'auto_schedule', 'custom' ) );
	$interval = absint( woo_ce_get_option( 'auto_interval', 1440 ) );
	switch( $schedule ) {

		case 'daily':
			$schedules['woo_ce_auto_interval'] = array(
				'interval' => 86400,
				'display'  => __( 'Daily', 'woo_ce' )
			);
			break;

		case 'weekly':
			$schedules['woo_ce_auto_interval'] = array(
				'interval' => 604800,
				'display'  => __( 'Weekly', 'woo_ce' )
			);
			break;

		case 'monthly':
			$schedules['woo_ce_auto_interval'] = array(
				'interval' => ( date( 't' ) * 60 * 60 * 24 ),
				'display'  => __( 'Monthly', 'woo_ce' )
			);
			break;

		case 'custom':
			$schedules['woo_ce_auto_interval'] = array(
				'interval' => $interval * 60,
				'display'  => sprintf( __( 'Every %d minutes', 'woo_ce' ), $interval )
			);
			break;

	}
	return $schedules;

}

function woo_ce_auto_export() {

	// Do we have an existing hash to check and has the hash override been enabled
	$site_hash = woo_ce_get_option( 'site_hash', false );
	if( !empty( $site_hash ) && ( woo_ce_get_option( 'override_duplicate_site_prompt', 0 ) == false ) ) {
		// Check the existing hash against the current hash
		if( $site_hash != md5( get_site_url() ) ) {
			// Show a notice and disable scheduled exports
			woo_ce_update_option( 'duplicate_site_prompt', 1 );
			woo_ce_update_option( 'enable_auto', 0 );
			return;
		}
	}

	$export_method = woo_ce_get_option( 'auto_method', 'archive' );
	$export_type = woo_ce_get_option( 'auto_type', '' );
	if( !empty( $export_type ) ) {
		if( in_array( $export_method, array( 'archive', 'email', 'post', 'ftp' ) ) )
			woo_ce_cron_export( $export_method, $export_type, true );
		else
			woo_ce_cron_export( '', $export_type, true );
	}

}

function woo_ce_cron_export( $gui = '', $type = '', $is_scheduled = false ) {

	global $export;

	$export = new stdClass;
	$export->cron = ( $is_scheduled ? 0 : 1 );
	$export->scheduled_export = ( $is_scheduled ? 1 : 0 );
	$export->start_time = time();
	$export->idle_memory_start = woo_ce_current_memory_usage();
	$export->error = '';

	$bits = '';
	$type = ( isset( $_GET['type'] ) ? $_GET['type'] : $type );
	if( empty( $type ) ) {
		if( $gui == 'gui' ) {
			$output = sprintf( '<p>%s</p>', __( 'No export type was provided.', 'woo_ce' ) );
		} else {
			error_log( sprintf( '[store-exporter-deluxe] -: Error: %s', __( 'No export type was provided', 'woo_ce' ) ) );
			return false;
		}
	} else {
		$types = array_keys( woo_ce_return_export_types() );
		$export->type = $type;
		// Check that export is in the list of available exports
		if( !in_array( $export->type, $types ) ) {
			if( $gui == 'gui' ) {
				$output = '<p>' . __( 'An invalid export type was provided.', 'woo_ce' ) . '</p>';
			} else {
				error_log( sprintf( '[store-exporter-deluxe] -: Error: %s', __( 'An invalid export type was provided', 'woo_ce' ) ) );
				return false;
			}
		} else {
			$export->export_format = ( isset( $_GET['format'] ) ? sanitize_text_field( $_GET['format'] ) : woo_ce_get_option( 'export_format', 'csv' ) );

			// Override the export format if outputting to screen in friendly design
			if( $gui == 'gui' && in_array( $export->export_format, array( 'csv', 'xls', 'xlsx' ) ) )
				$export->export_format = 'csv';

			// Override the export format if this is a scheduled export
			if( $export->scheduled_export )
				$export->export_format = woo_ce_get_option( 'auto_format', 'csv' );

			// Override the export format if the single order Transient is set
			$single_export_format = get_transient( WOO_CD_PREFIX . '_single_export_format' );
			if( $single_export_format !== false )
				$export->export_format = $single_export_format;
			unset( $single_export_format );

			$export->delimiter = ( isset( $_GET['delimiter'] ) ? sanitize_text_field( $_GET['delimiter'] ) : woo_ce_get_option( 'delimiter', ',' ) );
			if( $export->delimiter == '' || $export->delimiter == false ) {
				error_log( '[store-exporter-deluxe] Warning: Delimiter export option was corrupted, defaulted to ,' );
				$export->delimiter = ',';
				woo_ce_update_option( 'delimiter', ',' );
			} else if( $export->delimiter == 'TAB' ) {
				$export->delimiter = "\t";
			}
			$export->category_separator = ( isset( $_GET['category_separator'] ) ? sanitize_text_field( $_GET['category_separator'] ) : woo_ce_get_option( 'category_separator', '|' ) );
			// Override for line break (LF) support in Category Separator
			if( $export->category_separator == 'LF' )
				$export->category_separator = "\n";
			$export->bom = ( isset( $_GET['bom'] ) ? absint( $_GET['bom'] ) : woo_ce_get_option( 'bom', 1 ) );
			$export->encoding = ( isset( $_GET['encoding'] ) ? sanitize_text_field( $_GET['encoding'] ) : woo_ce_get_option( 'encoding', 'UTF-8' ) );
			$export->timeout = woo_ce_get_option( 'timeout', 600 );
			$export->escape_formatting = ( isset( $_GET['escape_formatting'] ) ? sanitize_text_field( $_GET['escape_formatting'] ) : woo_ce_get_option( 'escape_formatting', 'all' ) );
			$export->header_formatting = ( isset( $_GET['header_formatting'] ) ? absint( $_GET['header_formatting'] ) : woo_ce_get_option( 'header_formatting', 1 ) );
			$export->date_format = woo_ce_get_option( 'date_format', 'd/m/Y' );
			$export->order_items = ( isset( $_GET['order_items'] ) ? sanitize_text_field( $_GET['order_items'] ) : woo_ce_get_option( 'order_items_formatting', 'unique' ) );
			$export->order_items_types = ( isset( $_GET['order_items_types'] ) ? sanitize_text_field( $_GET['order_items_types'] ) : woo_ce_get_option( 'order_items_types', false ) );
			$export->upsell_formatting = woo_ce_get_option( 'upsell_formatting', 1 );
			$export->crosssell_formatting = woo_ce_get_option( 'crosssell_formatting', 1 );
			$export->gallery_formatting = woo_ce_get_option( 'gallery_formatting', 0 );
			$export->gallery_unique = woo_ce_get_option( 'gallery_unique', 0 );
			$export->max_product_gallery = woo_ce_get_option( 'max_product_gallery', 3 );
			$export->filename = woo_ce_generate_filename( $export->type );
			switch( $export->export_format ) {

				case 'csv':
					$php_excel_format = 'SED_CSV';
					$file_extension = 'csv';
					$post_mime_type = 'text/csv';
					break;

				case 'xls':
					$php_excel_format = 'Excel5';
					$file_extension = 'xls';
					$post_mime_type = 'application/vnd.ms-excel';
					break;

				case 'xlsx':
					$php_excel_format = 'Excel2007';
					$file_extension = 'xlsx';
					$post_mime_type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
					break;

				case 'xml':
					$file_extension = 'xml';
					$post_mime_type = 'application/xml';
					break;

				case 'rss':
					$file_extension = 'xml';
					$post_mime_type = 'application/rss+xml';
					break;

				default:
					error_log( sprintf( '[store-exporter-deluxe] -: Warning: %s', __( 'An invalid export format was provided', 'woo_ce' ) ) );
					return;
					break;

			}
			$export->filename = $export->filename . '.' . $file_extension;
			$export->limit_volume = ( isset( $_GET['limit'] ) ? absint( $_GET['limit'] ) : -1 );
			$export->offset = ( isset( $_GET['offset'] ) ? absint( $_GET['offset'] ) : 0 );
			// Select all export fields for CRON export
			$export->fields = woo_ce_cron_export_fields( $export->type, $export->scheduled_export );
			// Grab to value if response is e-mail or remote POST
			if( in_array( $gui, array( 'email', 'post' ) ) ) {
				if( $gui == 'email' )
					$export->to = ( isset( $_GET['to'] ) ? sanitize_email( $_GET['to'] ) : woo_ce_get_option( 'email_to', '' ) );
				else if( $gui == 'post' )
					$export->to = ( isset( $_GET['to'] ) ? esc_url_raw( $_GET['to'] ) : woo_ce_get_option( 'post_to', '' ) );
			}
			$export = woo_ce_check_cron_export_arguments( $export );

			$export->args = array(
				'limit_volume' => $export->limit_volume,
				'offset' => $export->offset,
				'encoding' => $export->encoding,
				'date_format' => $export->date_format,
				'order_items' => $export->order_items,
				'order_items_types' => $export->order_items_types
			);

			$orderby = ( isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : null );
			$order = ( isset( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : null );
			switch( $export->type ) {

				case 'product':
					$export->args['product_orderby'] = $orderby;
					$export->args['product_order'] = $order;
					if( $export->scheduled_export ) {
						$product_filter_type = woo_ce_get_option( 'auto_product_type', false );
						$product_filter_status = woo_ce_get_option( 'auto_product_status', false );
						$product_filter_stock = woo_ce_get_option( 'auto_product_stock', false );
						$product_filter_category = woo_ce_get_option( 'auto_product_category', false );
						$product_filter_tag = woo_ce_get_option( 'auto_product_tag', false );
						$export->args['product_type'] = ( !empty( $product_filter_type ) ? $product_filter_type : false );
						$export->args['product_status'] = ( !empty( $product_filter_status ) ? $product_filter_status : false );
						$export->args['product_stock'] = ( !empty( $product_filter_stock ) ? $product_filter_stock : false );
						$export->args['product_categories'] = ( !empty( $product_filter_category ) ? $product_filter_category : false );
						$export->args['product_tags'] = ( !empty( $product_filter_tag ) ? $product_filter_tag : false );
					} else {
						$export->args['product_status'] = ( isset( $_GET['product_status'] ) ? sanitize_text_field( $_GET['product_status'] ) : null );
						$export->args['product_stock'] = ( isset( $_GET['stock_status'] ) ? sanitize_text_field( $_GET['stock_status'] ) : null );
					}
					break;

				case 'category':
					$export->args['category_orderby'] = $orderby;
					$export->args['category_order'] = $order;
					break;

				case 'tag':
					$export->args['tag_orderby'] = $orderby;
					$export->args['tag_order'] = $order;
					break;

				case 'order':
					$export->args['order_orderby'] = $orderby;
					$export->args['order_order'] = $order;
					$export->args['order_ids'] = ( isset( $_GET['order_ids'] ) ? sanitize_text_field( $_GET['order_ids'] ) : null );

					// Override Filter Orders by Order ID if a single order transient is set
					$single_export_order_ids = get_transient( WOO_CD_PREFIX . '_single_export_order_ids' );
					if( $single_export_order_ids !== false )
						$export->args['order_ids'] = sanitize_text_field( $single_export_order_ids );
					unset( $single_export_order_ids );

					if( $export->scheduled_export ) {

						// Scheduled export engine

						// Order Status
						$order_filter_status = woo_ce_get_option( 'auto_order_status', '' );
						$export->args['order_status'] = ( !empty( $order_filter_status ) ? (array)$order_filter_status : array() );
						// Order Date
						$order_dates_filter = woo_ce_get_option( 'auto_order_date', false );
						if( $order_dates_filter ) {
							$export->args['order_dates_filter'] = $order_dates_filter;
							switch( $order_dates_filter ) {

								case 'manual':
									$order_filter_dates_from = woo_ce_get_option( 'auto_order_dates_from', false );
									$order_filter_dates_to = woo_ce_get_option( 'auto_order_dates_to', false );
									$export->args['order_dates_from'] = ( !empty( $order_filter_dates_from ) ? sanitize_text_field( $order_filter_dates_from ) : false );
									$export->args['order_dates_to'] = ( !empty( $order_filter_dates_to ) ? sanitize_text_field( $order_filter_dates_to ) : false );
									break;

								case 'variable':
									$order_filter_date_variable = woo_ce_get_option( 'auto_order_date_variable', false );
									$order_filter_date_variable_length = woo_ce_get_option( 'auto_order_date_variable_length', false );
									$export->args['order_dates_filter_variable'] = ( !empty( $order_filter_date_variable ) ? absint( $order_filter_date_variable ) : false );
									$export->args['order_dates_filter_variable_length'] = ( !empty( $order_filter_date_variable_length ) ? sanitize_text_field( $order_filter_date_variable_length ) : false );
									break;

							}
						}
						// Product
						$order_filter_product = woo_ce_get_option( 'auto_order_product', '' );
						$export->args['order_product'] = ( !empty( $order_filter_product ) ? (array)$order_filter_product : array() );
						// Billing Country
						$order_filter_billing_country = woo_ce_get_option( 'auto_order_billing_country', false );
						$export->args['order_billing_country'] = ( !empty( $order_filter_billing_country ) ? array_map( 'sanitize_text_field', $order_filter_billing_country ) : false );
						// Shipping Country
						$order_filter_shipping_country = woo_ce_get_option( 'auto_order_shipping_country', false );
						$export->args['order_shipping_country'] = ( !empty( $order_filter_shipping_country ) ? array_map( 'sanitize_text_field', $order_filter_shipping_country ) : false );
						// Payment Gateway
						$order_filter_payment = woo_ce_get_option( 'auto_order_payment', array() );
						$export->args['order_payment'] = ( !empty( $order_filter_payment ) ? array_map( 'sanitize_text_field', $order_filter_payment ) : false );
						// Shipping Method
						$order_filter_shipping = woo_ce_get_option( 'auto_order_shipping', array() );
						$export->args['order_shipping'] = ( !empty( $order_filter_shipping ) ? array_map( 'sanitize_text_field', $order_filter_shipping ) : false );
					} else {

						// CRON export engine

						// Order Status
						if( isset( $_GET['order_status'] ) ) {
							$order_filter_status = sanitize_text_field( $_GET['order_status'] );
							$order_filter_status = explode( ',', $order_filter_status );
							$export->args['order_status'] = $order_filter_status;
						}
						// Product
						if( isset( $_GET['order_product'] ) ) {
							$order_filter_product = sanitize_text_field( $_GET['order_product'] );
							$order_filter_product = explode( ',', $order_filter_product );
							$export->args['order_product'] = $order_filter_product;
						}
						// Order Date
						if( isset( $_GET['order_date_from'] ) && isset( $_GET['order_date_to'] ) ) {
							$order_filter_dates_from = $_GET['order_date_from'];
							$order_filter_dates_to = $_GET['order_date_to'];
							$export->args['order_dates_filter'] = 'manual';
							$export->args['order_dates_from'] = ( !empty( $order_filter_dates_from ) ? sanitize_text_field( $order_filter_dates_from ) : false );
							$export->args['order_dates_to'] = ( !empty( $order_filter_dates_to ) ? sanitize_text_field( $order_filter_dates_to ) : false );
						}
						// Billing Country
						if( isset( $_GET['billing_country'] ) ) {
							$order_filter_billing_country = sanitize_text_field( $_GET['billing_country'] );
							$order_filter_billing_country = explode( ',', $order_filter_billing_country );
							$export->args['order_billing_country'] = ( !empty( $order_filter_billing_country ) ? $order_filter_billing_country : false );
						}
						// Shipping Country
						if( isset( $_GET['shipping_country'] ) ) {
							$order_filter_shipping_country = sanitize_text_field( $_GET['shipping_country'] );
							$order_filter_shipping_country = explode( ',', $order_filter_shipping_country );
							$export->args['order_shipping_country'] = ( !empty( $order_filter_shipping_country ) ? $order_filter_shipping_country : false );
						}
						// Payment Gateway
						if( isset( $_GET['payment_gateway'] ) ) {
							$order_filter_payment = sanitize_text_field( $_GET['order_payment'] );
							$order_filter_payment = explode( ',', $order_filter_payment );
							$export->args['order_payment'] = ( !empty( $order_filter_payment ) ? $order_filter_payment : false );
						}
						// Shipping Method
						if( isset( $_GET['shipping_method'] ) ) {
							$order_filter_shipping = sanitize_text_field( $_GET['shipping_method'] );
							$order_filter_shipping = explode( ',', $order_filter_shipping );
							$export->args['order_shipping'] = ( !empty( $order_filter_shipping ) ? $order_filter_shipping : false );
						}
					}
					break;

				case 'subscription':
					$export->args['subscription_orderby'] = $orderby;
					$export->args['subscription_order'] = $order;
					break;

				case 'product_vendor':
					$export->args['product_vendor_orderby'] = $orderby;
					$export->args['product_vendor_order'] = $order;
					break;

				case 'commission':
					// Commission Date
					$commission_dates_filter = woo_ce_get_option( 'auto_commission_date', false );
					if( $commission_dates_filter ) {
						$export->args['commission_dates_filter'] = $commission_dates_filter;
						switch( $commission_dates_filter ) {

							case 'manual':
								$commission_filter_dates_from = woo_ce_get_option( 'auto_commission_dates_from', false );
								$commission_filter_dates_to = woo_ce_get_option( 'auto_commission_date_to', false );
								$export->args['commission_dates_from'] = ( !empty( $commission_filter_dates_from ) ? sanitize_text_field( $commission_filter_dates_from ) : false );
								$export->args['commission_dates_to'] = ( !empty( $commission_filter_dates_to ) ? sanitize_text_field( $commission_filter_dates_to ) : false );
								break;

							case 'variable':
								$commission_filter_date_variable = woo_ce_get_option( 'auto_commission_date_variable', false );
								$commission_filter_date_variable_length = woo_ce_get_option( 'auto_commission_date_variable_length', false );
								$export->args['commission_dates_filter_variable'] = ( !empty( $commission_filter_date_variable ) ? absint( $commission_filter_date_variable ) : false );
								$export->args['commission_dates_filter_variable_length'] = ( !empty( $commission_filter_date_variable_length ) ? sanitize_text_field( $commission_filter_date_variable_length ) : false );
								break;

						}
					}
					break;

				case 'shipping_class':
					$export->args['shipping_class_orderby'] = $orderby;
					$export->args['shipping_class_order'] = $order;
					break;

			}
			$export->filename = woo_ce_generate_filename( $export->type ) . '.' . $file_extension;
			// Let's spin up PHPExcel for supported Export Types and Export Formats
			if( in_array( $export->export_format, array( 'csv', 'xls', 'xlsx' ) ) ) {
				// Check if we are using PHPExcel or not for supported Export Types
				$dataset = woo_ce_export_dataset( $export->type );
				if( !empty( $dataset ) ) {
					// Check that PHPExcel is where we think it is
					if( file_exists( WOO_CD_PATH . 'classes/PHPExcel.php' ) ) {
						// Check if PHPExcel has already been loaded
						if( !class_exists( 'PHPExcel' ) ) {
							include_once( WOO_CD_PATH . 'classes/PHPExcel.php' );
						} else {
							error_log( sprintf( '[store-exporter-deluxe] %s: Warning: %s', $export->filename, __( 'The PHPExcel library was already loaded by another WordPress Plugin, if there\'s issues with your export file you know where to look.', 'woo_ce' ) ) );
						}
						$excel = new PHPExcel();
						$excel->setActiveSheetIndex( 0 );
						$excel->getActiveSheet()->setTitle( ucfirst( $export->type ) );
						$row = 1;
						// Skip headers if Heading Formatting is turned off
						if( $export->header_formatting ) {
							$col = 0;
							foreach( $export->columns as $column ) {
								$excel->getActiveSheet()->setCellValueByColumnAndRow( $col, $row, $column );
								$excel->getActiveSheet()->getCellByColumnAndRow( $col, $row )->getStyle()->getFont()->setBold( true );
								$excel->getActiveSheet()->getColumnDimensionByColumn( $col )->setAutoSize( true );
								$col++;
							}
							$row = 2;
						}
						foreach( $dataset as $data ) {
							$col = 0;
							foreach( array_keys( $export->fields ) as $field ) {
								$excel->getActiveSheet()->getCellByColumnAndRow( $col, $row )->getStyle()->getFont()->setBold( false );
								if( $export->encoding == 'UTF-8' ) {
									if( woo_ce_detect_value_string( ( isset( $data->$field ) ? $data->$field : null ) ) ) {
										// Treat this cell as a string
										$excel->getActiveSheet()->getCellByColumnAndRow( $col, $row )->setValueExplicit( ( isset( $data->$field ) ? wp_specialchars_decode( $data->$field ) : '' ), PHPExcel_Cell_DataType::TYPE_STRING );
									} else {
										$excel->getActiveSheet()->getCellByColumnAndRow( $col, $row )->setValue( ( isset( $data->$field ) ? wp_specialchars_decode( $data->$field ) : '' ) );
									}
								} else {
									// PHPExcel only deals with UTF-8 regardless of encoding type
									if( woo_ce_detect_value_string( ( isset( $data->$field ) ? $data->$field : null ) ) ) {
										// Treat this cell as a string
										$excel->getActiveSheet()->getCellByColumnAndRow( $col, $row )->setValueExplicit( ( isset( $data->$field ) ? utf8_encode( wp_specialchars_decode( $data->$field ) ) : '' ), PHPExcel_Cell_DataType::TYPE_STRING );
									} else {
										$excel->getActiveSheet()->getCellByColumnAndRow( $col, $row )->setValue( ( isset( $data->$field ) ? utf8_encode( wp_specialchars_decode( $data->$field ) ) : '' ) );
									}
								}
								$col++;
							}
							$row++;
						}
						switch( $export->export_format ) {

							case 'csv':
								// We need to load this after the PHPExcel Class has been created
								woo_cd_load_phpexcel_sed_csv_writer();
								break;

						}
						$objWriter = PHPExcel_IOFactory::createWriter( $excel, $php_excel_format );
						switch( $export->export_format ) {

							case 'csv':
								$objWriter->setUseBOM( true );
								// Check if we're using a non-standard delimiter
								if( $export->delimiter != ',' )
									$objWriter->setDelimiter( $export->delimiter );
								break;

							case 'xlsx':
								$objWriter->setPreCalculateFormulas( false );
								break;

						}
						if( in_array( $gui, array( 'raw' ) ) ) {
							$objWriter->save( 'php://output' );
						} else {
							// Save to file and insert to WordPress Media
							$temp_filename = tempnam( apply_filters( 'woo_ce_sys_get_temp_dir', sys_get_temp_dir() ), 'tmp' );
							// Check if we were given a temporary filename
							if( $temp_filename == false ) {
								$message = sprintf( __( 'We could not create a temporary export file in %s, ensure that WordPress can read and write files here and try again.', 'woo_ce' ), apply_filters( 'woo_ce_sys_get_temp_dir', sys_get_temp_dir() ) );
								error_log( sprintf( '[store-exporter-deluxe] %s: Error: %s', $export->filename, $message ) );
							} else {
								$objWriter->save( $temp_filename );
								$bits = file_get_contents( $temp_filename );
							}
							unlink( $temp_filename );
						}

						// Clean up PHPExcel
						$excel->disconnectWorksheets();
						unset( $objWriter, $excel );

					} else {
						error_log( sprintf( '[store-exporter-deluxe] %s: Error: %s', $export->filename, __( 'We couldn\'t load the PHPExcel library, this file should be present.' ) ) );
					}
				}
			// Run the default engine for the XML and RSS export formats
			} else if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
				// Check if SimpleXMLElement is present
				if( class_exists( 'SED_SimpleXMLElement' ) ) {
					if( $export->export_format == 'xml' ) {
						$xml = new SED_SimpleXMLElement( sprintf( apply_filters( 'woo_ce_export_xml_first_line', '<?xml version="1.0" encoding="%s"?><%s/>' ), esc_attr( $export->encoding ), esc_attr( apply_filters( 'woo_ce_export_xml_store_node', 'store' ) ) ) );
						if( woo_ce_get_option( 'xml_attribute_url', 1 ) )
							$xml->addAttribute( 'url', get_site_url() );
						if( woo_ce_get_option( 'xml_attribute_date', 1 ) )
							$xml->addAttribute( 'date', date( 'Y-m-d' ) );
						if( woo_ce_get_option( 'xml_attribute_time', 0 ) )
							$xml->addAttribute( 'time', date( 'H:i:s' ) );
						if( woo_ce_get_option( 'xml_attribute_title', 1 ) )
							$xml->addAttribute( 'name', htmlspecialchars( get_bloginfo( 'name' ) ) );
						if( woo_ce_get_option( 'xml_attribute_export', 1 ) )
							$xml->addAttribute( 'export', htmlspecialchars( $export->type ) );
						if( woo_ce_get_option( 'xml_attribute_orderby', 1 ) )
							$xml->addAttribute( 'orderby', $orderby );
						if( woo_ce_get_option( 'xml_attribute_order', 1 ) )
							$xml->addAttribute( 'order', $order );
						if( woo_ce_get_option( 'xml_attribute_limit', 1 ) )
							$xml->addAttribute( 'limit', $export->limit_volume );
						if( woo_ce_get_option( 'xml_attribute_offset', 1 ) )
							$xml->addAttribute( 'offset', $export->offset );
						$xml = woo_ce_export_dataset( $export->type, $xml );
					} else if( $export->export_format == 'rss' ) {
						$xml = new SED_SimpleXMLElement( sprintf( apply_filters( 'woo_ce_export_rss_first_line', '<?xml version="1.0" encoding="%s"?><rss version="2.0"%s/>' ), esc_attr( $export->encoding ), ' xmlns:g="http://base.google.com/ns/1.0"' ) );
						$child = $xml->addChild( apply_filters( 'woo_ce_export_rss_channel_node', 'channel' ) );
						$child->addChild( 'title', woo_ce_get_option( 'rss_title', '' ) );
						$child->addChild( 'link', woo_ce_get_option( 'rss_link', '' ) );
						$child->addChild( 'description', woo_ce_get_option( 'rss_description', '' ) );
						$bits = woo_ce_export_dataset( $export->type, $child );
					}
					$bits = woo_ce_format_xml( $xml );
				} else {
					$bits = false;
					error_log( '[store-exporter-deluxe] Error: The SimpleXMLElement class does not exist for XML file generation' );
				}
			}
			if( !empty( $bits ) ) {
				$output = '<p>' . __( 'Export completed successfully.', 'woo_ce' ) . '</p>';
				if( $gui == 'gui' )
					$output .= '<textarea readonly="readonly">' . esc_textarea( str_replace( '<br />', "\n", $bits ) ) . '</textarea>';
			} else {
				if( $gui == 'gui' ) {
					$output = sprintf( '<p>%s</p>', __( 'No export entries were found.', 'woo_ce' ) );
				} else {
					error_log( sprintf( '[store-exporter-deluxe] %s: Warning: %s', $export->filename, __( 'No export entries were found', 'woo_ce' ) ) );
					return false;
				}
			}
		}
	}

	// Return raw export to browser without file headers
	if( $gui == 'raw' && !empty( $bits ) ) {
		return $bits;
	// Return export as file download to browser
	} else if( $gui == 'download' && !empty( $bits ) ) {
		woo_ce_generate_file_headers( $post_mime_type );
		if( defined( 'DOING_AJAX' ) || get_transient( WOO_CD_PREFIX . '_single_export_format' ) !== false )
			echo $bits;
		else
			return $bits;
	// HTTP Post export contents to remote URL
	} else if( $gui == 'post' && !empty( $bits ) ) {
		$args = apply_filters( 'woo_ce_cron_export_post_args', array(
			'method'      => 'POST',
			'timeout'     => 60,
			'redirection' => 0,
			'httpversion' => '1.0',
			'sslverify'   => false,
			'blocking'    => true,
			'headers'     => array(
				'accept'       => $post_mime_type,
				'content-type' => $post_mime_type
			),
			'body'        => $bits,
			'cookies'     => array(),
			'user-agent'  => sprintf( 'WordPress/%s', $GLOBALS['wp_version'] ),
		) );
		$response = wp_remote_post( $export->to, $args );
		if( is_wp_error( $response ) ) {
			error_log( sprintf( '[store-exporter-deluxe] %s: Error: %s', $export->filename, $response->get_error_message() ) );
			return false;
		} else {
			error_log( sprintf( '[store-exporter-deluxe] %s: Success: %s', $export->filename, sprintf( __( 'Remote POST sent to %s', 'woo_ce' ), $export->to ) ) );
		}
	// Output to screen in friendly design with on-screen error responses
	} else if( $gui == 'gui' ) {
		if( file_exists( WOO_CD_PATH . 'templates/admin/cron.php' ) ) {
			include_once( WOO_CD_PATH . 'templates/admin/cron.php' );
		} else {
			error_log( sprintf( '[store-exporter-deluxe] %s: Error: %s', $export->filename, __( 'Could not load template file within /templates/admin/cron.php', 'woo_ce' ) ) );
		}
		if( isset( $output ) )
			echo $output;
		echo '
	</body>
</html>';
	// Save export file to WordPress Media before sending/saving/etc. action
	} else if( in_array( $gui, array( 'gui', 'archive', 'url', 'file', 'email', 'ftp' ) ) ) {
		$upload = false;
		if( $export->filename && !empty( $bits ) ) {
			$post_ID = woo_ce_save_file_attachment( $export->filename, $post_mime_type );
			$upload = wp_upload_bits( $export->filename, null, $bits );
			if( ( $post_ID == false ) || $upload['error'] ) {
				wp_delete_attachment( $post_ID, true );
				error_log( sprintf( '[store-exporter-deluxe] %s: Error: %s', $export->filename, $upload['error'] ) );
				return false;
			}
			if( file_exists( ABSPATH . 'wp-admin/includes/image.php' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/image.php' );
				$attach_data = wp_generate_attachment_metadata( $post_ID, $upload['file'] );
				wp_update_attachment_metadata( $post_ID, $attach_data );
				update_attached_file( $post_ID, $upload['file'] );
				if( !empty( $post_ID ) ) {
					woo_ce_save_file_guid( $post_ID, $export->type, $upload['url'] );
					woo_ce_save_file_details( $post_ID );
				}
			} else {
				error_log( sprintf( '[store-exporter-deluxe] %s: Error: %s', $export->filename, __( 'Could not load image.php within /wp-admin/includes/image.php', 'woo_ce' ) ) );
			}
		}
		// Return URL to export file
		if( $gui == 'url' )
			return $upload['url'];
		// Return system path to export file
		if( $gui == 'file' )
			return $upload['file'];
		// E-mail export file to preferred address or WordPress site owner address
		if( $gui == 'email' ) {

			global $woocommerce;

			$mailer = $woocommerce->mailer();
			$subject = woo_ce_cron_email_subject( $export->type, $export->filename );
			$attachment = $upload['file'];
			$email_heading = sprintf( __( 'Export: %s', 'woo_ce' ), ucwords( $export->type ) );
			$recipient_name = apply_filters( 'woo_ce_email_recipient_name', __( 'there', 'woo_ce' ) );
			$email_contents = apply_filters( 'woo_ce_email_contents', wpautop( __( 'Please find attached your export ready to review.', 'woo_ce' ) ) );

			// Buffer
			ob_start();

			// Get mail template
			if( file_exists( WOO_CD_PATH . 'templates/emails/scheduled_export.php' ) ) {
				include_once( WOO_CD_PATH . 'templates/emails/scheduled_export.php' );
			} else {
				echo wpautop( sprintf( __( 'Hi %s', 'woo_ce' ), $recipient_name ) );
				echo $email_contents;
				error_log( sprintf( '[store-exporter-deluxe] %s: Error: %s', $export->filename, sprintf( __( 'Could not load template file %s within %s, defaulted to hard coded template.', 'woo_ce' ), 'scheduled_export.php', '/templates/emails/...' ) ) );
			}

			// Get contents
			$message = ob_get_clean();

			// Send the mail using WooCommerce mailer
			if( function_exists( 'woocommerce_mail' ) ) {
				woocommerce_mail( $export->to, $subject, $message, null, $attachment );
			} else {
				// Default to wp_mail()
				add_filter( 'wp_mail_content_type', 'woo_ce_set_html_content_type' );
				wp_mail( $export->to, $subject, $message, null, $attachment );
				remove_filter( 'wp_mail_content_type', 'woo_ce_set_html_content_type' );
			}
			// Delete the export file regardless of whether e-mail was successful or not
			wp_delete_attachment( $post_ID, true );
			error_log( sprintf( '[store-exporter-deluxe] %s: Success: %s', $export->filename, sprintf( __( 'Scheduled export e-mail of %s sent to %s', 'woo_ce' ), $export->filename, $export->to ) ) );

		}
		if( $gui == 'ftp' ) {

			// Load up our FTP/SFTP resources
			$host = woo_ce_get_option( 'auto_ftp_method_host', '' );
			if( !empty( $host ) )
				$host = woo_ce_format_ftp_host( $host );
			$port = woo_ce_get_option( 'auto_ftp_method_port', '' );
			$port = ( !empty( $port ) ? absint( $port ) : false );
			$user = woo_ce_get_option( 'auto_ftp_method_user', '' );
			$pass = woo_ce_get_option( 'auto_ftp_method_pass', '' );
			$path = woo_ce_get_option( 'auto_ftp_method_path', '' );
			$filename = woo_ce_get_option( 'auto_ftp_method_filename', '' );
			// Switch to fixed export filename if provided
			if( !empty( $filename ) )
				$export->filename = woo_ce_generate_filename( $export->type, $filename ) . '.' . $file_extension;

			// Check what protocol are we using; FTP or SFTP?
			$protocol = woo_ce_get_option( 'auto_ftp_method_protocol', 'ftp' );
			switch( $protocol ) {

				case 'ftp':
				default:
					// Check if ftp_connect() is available
					if( function_exists( 'ftp_connect' ) ) {
						$passive = woo_ce_get_option( 'auto_ftp_method_passive', '' );
						$timeout = woo_ce_get_option( 'auto_ftp_method_timeout', '' );
						if( $connection = @ftp_connect( $host, $port ) ) {
							// Update the FTP timeout if available and if a timeout was provided at export
							$remote_timeout = ftp_get_option( $connection, FTP_TIMEOUT_SEC );
							$timeout = absint( $timeout );
							if( $remote_timeout !== false && !empty( $timeout ) ) {
								// Compare the server timeout and the timeout provided at export
								if( $remote_timeout <> $timeout ) {
									if( ftp_set_option( $connection, FTP_TIMEOUT_SEC, $timeout ) == false )
										error_log( sprintf( '[store-exporter-deluxe] %s: Warning: %s', $export->filename, sprintf( __( 'Could not change the FTP server timeout on %s', 'woo_ce' ), $host ) ) );
								}
							}
							unset( $remote_timeout );
							if( ftp_login( $connection, $user, $pass ) ) {
								// Check if Transfer Mode is set to Auto/Pasive and if passive mode is available
								if( in_array( $passive, array( 'auto', 'passive' ) ) ) {
									$features = ftp_raw( $connection, 'FEAT' );
									if( !empty( $features ) ) {
										if( in_array( 'PASV', $features ) ) {
											if( ftp_pasv( $connection, true ) == false )
												error_log( sprintf( '[store-exporter-deluxe] %s: Warning: %s', 'woo_ce', $export->filename, sprintf( __( 'Could not switch to FTP passive mode on %s', 'woo_ce' ), $host ) ) );
										}
									}
								}
								// Change directory if neccesary
								if( !empty( $directory ) ) {
									$current_directory  = ftp_pwd( $connection );
									if( $current_directory !== false && @ftp_chdir( $connection, $path ) )
										ftp_chdir( $connection, $path );
								}
								if( ftp_put( $connection, sprintf( '%s/%s', $path, $export->filename ), $upload['file'], FTP_ASCII ) ) {
									error_log( sprintf( '[store-exporter-deluxe] %s: Success: %s', $export->filename, sprintf( __( 'Scheduled export of %s to %s via FTP uploaded', 'woo_ce' ), $export->filename, $path ) ) );
								} else {
									$export->error = sprintf( __( 'There was a problem uploading %s to %s via FTP, response: %s', 'woo_ce' ), $export->filename, $path, woo_ce_error_get_last_message() );
									error_log( sprintf( '[store-exporter-deluxe] %s: Error: %s', $export->filename, $export->error ) );
								}
							} else {
								$export->error = sprintf( __( 'Login incorrect for user %s on FTP server at %s, response: %s', 'woo_ce' ), $user, $host, woo_ce_error_get_last_message() );
								error_log( sprintf( '[store-exporter-deluxe] %s: Error: %s', $export->filename, $export->error ) );
							}
						} else {
							$export->error = sprintf( __( 'There was a problem connecting to %s via FTP', 'woo_ce' ), $host );
							error_log( sprintf( '[store-exporter-deluxe] %s: Error: %s', $export->filename, $export->error ) );
						}
					} else {
						$export->error = __( 'The function ftp_connect() is disabled within your WordPress site, cannot upload to FTP server', 'woo_ce' );
						error_log( __( '[store-exporter-deluxe] %s: Error: %s', 'woo_ce' ), $export->filename, $export->error );
					}
					break;

				case 'sftp':
					// Check if ssh2_connect() is available
					if( function_exists( 'ssh2_connect' ) ) {
						if( $connection = @ssh2_connect( $host, $port ) ) {
							if( ssh2_auth_password( $connection, $user, $pass ) ) {
								// Initialize SFTP subsystem
								if( $session = ssh2_sftp( $connection ) ) {
									if( $handle = fopen( sprintf( 'ssh2.sftp://%s/%s/%s', $session, $path, $export->filename ), 'w+' ) ) {
										error_log( sprintf( '[store-exporter-deluxe] %s: Success: %s', $export->filename, sprintf( __( 'Scheduled export of %s to %s via SFTP uploaded', 'woo_ce' ), $export->filename, $path ) ) );
									} else {
										$export->error = sprintf( __( 'There was a problem uploading %s to %s via SFTP', 'woo_ce' ), $export->filename, $path );
										error_log( sprintf( '[store-exporter-deluxe] %s: Error: %s', $export->filename, $export->error ) );
									}
								} else {
									$export->error = sprintf( __( 'Could not initialize SFTP subsystem on SFTP server at %s', 'woo_ce' ), $host );
									error_log( sprintf( '[store-exporter-deluxe] %s: Error: %s', $export->filename, $export->error ) );
								}
							} else {
								$export->error = sprintf( __( 'Login incorrect for user %s on SFTP server at %s', 'woo_ce' ), $user, $host );
								error_log( sprintf( '[store-exporter-deluxe] %s: Error: %s', $export->filename, $export->error ) );
							}
						} else {
							$export->error = sprintf( __( 'There was a problem connecting to %s via SFTP', 'woo_ce' ), $host );
							error_log( sprintf( '[store-exporter-deluxe] %s: Error: %s', $export->filename, $export->error ) );
						}
					} else {
						$export->error = __( 'The function ssh2_connect() is disabled within your WordPress site, cannot upload to SFTP server', 'woo_ce' );
						error_log( sprintf( __( '[store-exporter-deluxe] %s: Error: %s', 'woo_ce' ), $export->filename, $export->error ) );
					}
					break;

			}
			// Delete the export file regardless of whether upload was successful or not
			wp_delete_attachment( $post_ID, true );
		}
	}

	// Only include scheduled exports to the Recent Scheduled Exports list
	if( $export->scheduled_export ) {
		$recent_exports = woo_ce_get_option( 'recent_scheduled_exports', array() );
		if( empty( $recent_exports ) )
			$recent_exports = array();
		$size = count( $recent_exports );
		// Get the limit from the WordPress Dashboard widget
		if( !$widget_options = get_option( 'woo_ce_recent_scheduled_export_widget_options', array() ) ) {
			$widget_options = array(
				'number' => 5
			);
		}

		// Check if we have maxed out our recent scheduled exports
		if( $size >= $widget_options['number'] )
			array_shift( $recent_exports );
		$post_ID = ( isset( $post_ID ) ? $post_ID : 0 );
		$recent_exports[] = array(
			'post_id' => ( empty( $export->error ) ? $post_ID : 0 ),
			'name' => $export->filename,
			'date' => time(),
			'method' => $gui,
			'error' => $export->error
		);
		woo_ce_update_option( 'recent_scheduled_exports', $recent_exports );
	}

	delete_option( WOO_CD_PREFIX . '_exported' );

	// If the CRON process gets this far, pass on the good news!
	return true;

}

function woo_ce_set_html_content_type() {

	return 'text/html';

}

function woo_ce_check_cron_export_arguments( $args ) {

	$args->export_format = ( $args->export_format != '' ? $args->export_format : 'csv' );
	$args->limit_volume = ( $args->limit_volume != '' ? $args->limit_volume : -1 );
	$args->offset = ( $args->offset != '' ? $args->offset : 0 );
	$args->date_format = ( $args->date_format != '' ? $args->date_format : 'd/m/Y' );
	// Override for corrupt WordPress option 'date_format' from older releases
	if( $args->date_format == '1' || $args->date_format == '' || $args->date_format == false ) {
		error_log( '[store-exporter-deluxe] Warning: Date Format export option was corrupted, defaulted to d/m/Y' );
		$args->date_format = 'd/m/Y';
	}
	// Override for Order Item Types passed via CRON
	if( !empty( $args->order_items_types ) && !is_array( $args->order_items_types ) ) {
		$args->order_items_types = explode( ',', $args->order_items_types );
	} else if( empty( $args->order_items_types ) ) {
		// Override for empty Order Item Types
		$args->order_items_types = array( 'line_item' );
	}
	return $args;

}

function woo_ce_cron_export_fields( $export_type = '', $is_scheduled = 0 ) {

	global $export;

	$fields = array();

	$export_fields = 'all';
	// Override the export fields if the single order Transient is set
	$single_export_fields = get_transient( WOO_CD_PREFIX . '_single_export_fields' );
	if( $single_export_fields !== false ) {
		$export_fields = $single_export_fields;
	} else {
		if( $is_scheduled == '0' )
			$export_fields = woo_ce_get_option( 'cron_fields', 'all' );
		else if( $is_scheduled == '1' )
			$export_fields = woo_ce_get_option( 'scheduled_fields', 'all' );
	}
	unset( $single_export_fields );

	if( $export_fields == 'all' ) {
		// Check that the fields list exists for that export type
		if( function_exists( sprintf( 'woo_ce_get_%s_fields', $export_type ) ) )
			$fields = call_user_func_array( 'woo_ce_get_' . $export_type . '_fields', array( 'summary' ) );
	} else if( $export_fields == 'saved' ) {
		// Fall back to default of stored export fields for that export type
		$fields = woo_ce_get_option( $export_type . '_fields', array() );
	}
	return $fields;

}

function woo_ce_cron_email_subject( $type = '', $filename = '' ) {

	$subject = woo_ce_get_option( 'email_subject', '' );
	// Default subject
	if( empty( $subject ) )
		$subject = __( '[%store_name%] Export: %export_type% (%export_filename%)', 'woo_ce' );
	$subject = str_replace( '%store_name%', sanitize_title( get_bloginfo( 'name' ) ), $subject );
	$subject = str_replace( '%export_type%', ucwords( $type ), $subject );
	$subject = str_replace( '%export_filename%', $filename, $subject );
	return $subject;

}

function woo_ce_trigger_new_order_export( $order_id = 0 ) {

	global $export;

	if( !empty( $order_id ) ) {
		$export_format = woo_ce_get_option( 'trigger_new_order_format', 'csv' );
		$export_fields = woo_ce_get_option( 'trigger_new_order_fields', 'all' );
		$gui = woo_ce_get_option( 'trigger_new_order_method', 'archive' );
		set_transient( WOO_CD_PREFIX . '_single_export_format', sanitize_text_field( $export_format ), MINUTE_IN_SECONDS );
		set_transient( WOO_CD_PREFIX . '_single_export_order_ids', absint( $order_id ), MINUTE_IN_SECONDS );
		set_transient( WOO_CD_PREFIX . '_single_export_fields', sanitize_text_field( $export_fields ), MINUTE_IN_SECONDS );
		$export_type = 'order';
		if( woo_ce_cron_export( $gui, $export_type ) ) {
			error_log( sprintf( '[store-exporter-deluxe] %s: Success: %s', $export->filename, sprintf( __( 'New Order #%d export saved to WordPress Media', 'woo_ce' ), $order_id ) ) );
		}
		delete_transient( WOO_CD_PREFIX . '_single_export_format' );
		delete_transient( WOO_CD_PREFIX . '_single_export_order_ids' );
		delete_transient( WOO_CD_PREFIX . '_single_export_fields' );
		unset( $gui, $export_type );
	}

}
?>