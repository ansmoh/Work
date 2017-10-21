<?php
/**
 * Plugin Name: WooCommerce Infusionsoft & Eventbrite
 * Description:
 * Version: 1.0
 * Author: Isaac Oyelowo
 * Author URI: http://codemypain.com
 * Plugin URI: http://isaacoyelowo.com
 */


if( !class_exists("iaSDK")) include('class/iasdk.php');

class ISAAC_WCinfusion
{
    public function __construct()
    {
        $this->_plugin_dir = dirname(__FILE__);
        $this->_plugin_url = get_site_url(null, 'wp-content/plugins/' . basename($this->_plugin_dir));
        $this->addActions();
        //register_activation_hook(__FILE__, array($this, 'OnActivate'));
        //register_deactivation_hook(__FILE__, array($this, 'deActivate'));
    }

    public function addActions()
    {
        add_action( 'init', array($this,'wci_localize') );
        add_filter( 'infusedwoo_contact_updateinfo', array( $this, 'send_to_infusion' ),10,2 );
        add_action( 'infuse_hourly_event', array($this,'do_eventbrite') );
        add_action( 'admin_menu', array($this,'plugin_menu'));
        // add_action( 'template_redirect', array($this,'webhtml'));
        // add_action( 'wp_footer', array($this,'place_js_footer'));
        //add_action( 'init', array($this,'do_eventbrite') );
    }

    public function wci_localize()
    {
        load_plugin_textdomain('wc_infusion', false, dirname(plugin_basename(__FILE__)). "/languages" );
    }

    public function OnActivate()
    {
        if (! wp_next_scheduled ( 'infuse_hourly_event' ))
        {
            wp_schedule_event(time(), 'hourly', 'infuse_hourly_event');
        }
    }

    public function deActivate()
    {
        wp_clear_scheduled_hook('infuse_hourly_event');
    }

    public function send_to_infusion($contactinfo, $order)
    {
        $order_id = $order->ID;

        $items = $order->get_items();

        foreach ($items as $key => $value)
        {
            $s_date .= wc_get_order_item_meta($key, 'Which date will you begin class?', 1);
            $loc .= wc_get_order_item_meta($key, 'Which location would you like to train?', 1);
            $p_id = $value['item_meta']['_product_id'][0];
        }

        $contactinfo['_OrderID'] = $order_id;
        $contactinfo['_BirthdayDate0']    = $order->myfield25;
        $contactinfo['_DriversLicense']    = $order->billing_myfield7;
        $contactinfo['_DriversLicenseState']    = $order->billing_myfield8;
        $contactinfo['_TrackingNumber']    = '';
        $contactinfo['_PaymentAmount']    = $order->order_total;
        $contactinfo['_ProgramCost']    = $order->order_total;
        $contactinfo['_PaymentPlanTotal']    = $order->order_total;
        $contactinfo['_BookType']    = 'Electronic Books';
        $contactinfo['_TrainingType0']    = 'Online Classes';
        $contactinfo['_TrainingLocation0']    = $loc;
        $contactinfo['_ProgramPurchase']    = $p_id;
        $contactinfo['_CourseStartDate']    = $s_date;
        $contactinfo['_EventbriteEventDate']    = '';
        $contactinfo['_StudentCourseGrades0']    = '';
        $contactinfo['_StudentLessonGrades']    = '';
        $contactinfo['_StudentCourseCompletionDate']    = '';
        return $contactinfo;

    }

    public function plugin_menu()
    {
        add_options_page( 'Eventbrite Settings', 'Eventbrite Settings', 'manage_options', 'eve_settings', array($this,'eve_plugin_options') );
    }

    public function eve_plugin_options()
    {
        $timestamp = wp_next_scheduled( 'infuse_hourly_event');
        $timestamp = gmdate("F j, Y, g:i a", $timestamp);
        if ( !current_user_can( 'manage_options' ) )
        {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        if(isset($_POST['save_ie_settings']))
        {
            echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
            <p><strong>Settings saved.</strong></p><button type="button" class="notice-dismiss">
            <span class="screen-reader-text">Dismiss this notice.</span></button></div>';
            if(isset($_POST['inf_app_n']))
            {
                update_option('inf_app_n',$_POST['inf_app_n']);
            }
            if(isset($_POST['inf_app_k']))
            {
                update_option('inf_app_k',$_POST['inf_app_k']);
            }
            if(isset($_POST['eve_toks_a']))
            {
                update_option('eve_toks_a',$_POST['eve_toks_a']);
            }
            if(isset($_POST['eve_toks_b']))
            {
                update_option('eve_toks_b',$_POST['eve_toks_b']);
            }
        }
        $inf_app_n = get_option('inf_app_n');
        $inf_app_k = get_option('inf_app_k');
        $eve_toks_a = get_option('eve_toks_a');
        $eve_toks_b = get_option('eve_toks_b');
        ?>
        <h2>Infusionsoft API Settings</h2>
        <p>Enter your Infusionsoft API credentials in the fields below.</p>
        <form method="post">
            <table>
              <tbody>
                <tr>
                  <td>
                    <strong>App Name</strong>
                  </td>
                  <td>
                    <input type="text" name="inf_app_n" value="<?php print $inf_app_n; ?>">
                  </td>
                </tr>
                <tr>
                  <td>
                    <strong>API Key</strong>
                  </td>
                  <td>
                    <input type="text" name="inf_app_k" value="<?php print $inf_app_k; ?>">
                  </td>
                </tr>
              </tbody>
            </table>

            <h2>Eventbrite API Settings</h2>
        <p>Enter your Eventbrite API credentials in the fields below.</p>
        <form method="post">
            <table>
              <tbody>
                <tr>
                  <td>
                    <strong>Token for first App</strong>
                  </td>
                  <td>
                    <input type="text" name="eve_toks_a" value="<?php print $eve_toks_a; ?>">
                  </td>
                </tr>
                <tr>
                  <td>
                    <strong>Token for Second App</strong>
                  </td>
                  <td>
                    <input type="text" name="eve_toks_b" value="<?php print $eve_toks_b; ?>">
                  </td>
                </tr>
              </tbody>
            </table>
            <p class="submit">
                <input name="save_ie_settings" id="submit" class="button button-primary" value="Save Changes" type="submit">
            </p>
        </form>
        <hr />
        <div>
            <span>The next import will take place </span><?php echo $timestamp; ?>
        <?php
    }


    public function do_eventbrite()
    {
        $eve_toks_a = get_option('eve_toks_a');
        $eve_toks_b = get_option('eve_toks_b');
        $request_url = 'https://www.eventbriteapi.com/v3/events/search/?token='.$eve_toks_a;

        $response = wp_remote_get( $request_url, array('timeout'=>120));
        if( is_wp_error( $response ) )
        {
            return $response->get_error_message();
        }

        $json = json_decode( $response['body'] );

        $i = 1;

        foreach ( $json->events as $event )
        {
            $event_url = 'https://www.eventbriteapi.com/v3/events/'.$event->id.'/attendees/?token=='.$eve_toks_a;

            $response = wp_remote_get( $request_url, array('timeout'=>120));
            if( is_wp_error( $response ) )
            {
                return $response->get_error_message();
            }

            $json = json_decode( $response['body'] );
            print_r($json->pagination);
            //
            //if(in_array($event->id,$arr))https://www.eventbriteapi.com/v3/events/
            echo $i.'. '.$event->id .'<br/>';
            $arr_event[] = $event->id;
            $i++;
        }
        update_option('arr_event_a',$arr_event);
        print_r($arr_event);
        die();

        /*
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($ch);
        curl_close($ch);
        */
    }

    public function evnt_to_infuse()
    {

    }

    public function webhtml()
    {
        if(is_checkout())
        {
            $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            //die($actual_link);
            if(!isset($_GET['tac']) && $actual_link == 'https://carealtyschool.com/checkout/')
            {
                wp_redirect('https://carealtyschool.com/terms-and-conditions/');
                exit();
            }
        }
    }

    public function place_js_footer()
    {
        ?>
        <script type="text/javascript">
jQuery(function(){
    jQuery(document).ready(function(){
        jQuery("#applicant_esign_content").scroll(function(){
            var totalScrollHeight = jQuery("#applicant_esign_content")[0].scrollHeight
            var scrollBarHeight = jQuery("#applicant_esign_content")[0].clientHeight
            var scrollBarTopPosition = jQuery("#applicant_esign_content")[0].scrollTop
            if (totalScrollHeight== scrollBarHeight + scrollBarTopPosition){
                jQuery("#applicant_read_the_content").val("true")
                jQuery("#accept_esign_disclosure_applicant").css("background-color","green")
                jQuery("#dissaprove_esign_disclosure_applicant").css("background-color","red")
            }
        })

        jQuery("#accept_esign_disclosure_applicant").click(function(){
            if (jQuery("#applicant_read_the_content").val() != "true"){
                alert("Please scroll through the disclosure text before clicking I Agree.")
                return false
            }
            else{
                jQuery("#esign_acceptance_form").submit()
            }
        })
        jQuery("#dissaprove_esign_disclosure_applicant").click(function(){
            if (jQuery("#applicant_read_the_content").val() != "true"){
                alert("Please scroll through the disclosure text before clicking back.")
                return false
            }
        })
    });
})
</script>
<?php
    }

}
$isaac_infuse = new ISAAC_WCinfusion();
