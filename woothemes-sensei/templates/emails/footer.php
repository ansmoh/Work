<?php
/**
 * Email Footer
 *
 * @author 		WooThemes
 * @package 	Sensei/Templates/Emails
 * @version     1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woothemes_sensei, $sensei_email_data;
extract( $sensei_email_data );

// Load colours
$base = '#557da1';
if( isset( $woothemes_sensei->settings->settings['email_base_color'] ) && '' != $woothemes_sensei->settings->settings['email_base_color'] ) {
    $base = $woothemes_sensei->settings->settings['email_base_color'];
}

$base_lighter_40 = sensei_hex_lighter( $base, 40 );

$footer_text = sprintf( __( '%1$s - Powered by Sensei', 'woothemes-sensei' ), get_bloginfo( 'name' ) );
if( isset( $woothemes_sensei->settings->settings['email_footer_text'] ) ) {
    $footer_text = $woothemes_sensei->settings->settings['email_footer_text'];
}

// For gmail compatibility, including CSS styles in head/body are stripped out therefore styles need to be inline. These variables contain rules which are added to the template inline.
$template_footer = "
	border-top:0;
	-webkit-border-radius:6px;
";

$credit = "
	border:0;
	color: $base_lighter_40;
	font-family: Arial;
	font-size:12px;
	line-height:125%;
	text-align:center;
";
?>
															</div>
														</td>
                                                    </tr>
                                                </table>
                                                <!-- End Content -->
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- End Body -->
                                </td>
                            </tr>
                            <tr>
<td align="center" valign="top">
                                    
                                	<table border="0" cellpadding="10" cellspacing="0" width="600" style="border-top:3px solid #000;    background: #c3c3c3;border-bottom:3px solid #000"><tbody><tr>
<td valign="top" style="padding:0">
                                                <table border="0" cellpadding="10" cellspacing="0" width="100%"><tbody><tr>
<td colspan="2" style="padding:0 48px 48px 48px;padding-bottom:0!important;border:0;color:#d9d9d9;font-family:Arial;font-size:12px;line-height:125%;text-align:center">
                                                        	<p style="font-size:18px;color:#000!important;text-decoration:none;line-height:25px">For More
Information Visit:<br><a href="http://carealtytraining.com/" style="color:#000!important;font-weight:normal;text-decoration:none;font-size:18px;line-height:25px" target="_blank">www.CARealtyTraining.Com</a><br><a href="mailto:info@carealtytraining.com" style="color:#000!important;font-weight:normal;text-decoration:none;font-size:18px;line-height:25px" target="_blank">info@CArealtyTraining.Com</a></p>
                                                        </td>
                                                    </tr></tbody></table>
</td>
                                        </tr></tbody></table>

</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>