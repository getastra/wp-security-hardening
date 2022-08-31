<?php
if (!defined('ABSPATH')) exit ('Peekaboo!');

require_once(ABSPATH."/wp-includes/pluggable.php");
require_once(ABSPATH."/wp-load.php");

add_action( 'admin_footer', 'wh_admin_footer' );
function wh_admin_footer(){
	echo '<a href="#" id="fake_link"   target="_blank">&nbsp;</a>';
}
  
 
add_action('init', 'fake_cron_function');
function fake_cron_function(){
	$last_run = get_option('hard_cron');
	$schedule_audit = get_option('whp_custom_admin_schedule_audit');
 	
	if( $last_run == '' || !$last_run ){
		$last_run = time();
		update_option('hard_cron', $last_run  );
	}

	if( trim($schedule_audit) =='every day' && (time() - $last_run  > 60*60*24) ){
		
		whp_task_function();
		
		$last_run = time();
		update_option('hard_cron', $last_run  );
	}

	elseif( trim($schedule_audit) =='every week' && (time() - $last_run  > 60*60*24*7) ){
		
		whp_task_function();
		
		$last_run = time();
		update_option('hard_cron', $last_run  );
	}


	elseif( trim($schedule_audit) =='every month' && (time() - $last_run  > 60*60*24*30) ){
		
		whp_task_function();
		
		$last_run = time();
		update_option('hard_cron', $last_run  );
	}
	elseif(time() - $last_run  > 60*60*24*30){

		whp_task_function();
		
		$last_run = time();
		update_option('hard_cron', $last_run  );

	}

}

  //whp_task_function();
  function whp_task_function() {

		global $wpdb;

		$tnp = new issuesScanClass();
		$tnp->run_issues_check();
		$obj = new tableViewOutput();
		$obj->process_results();
		$resom_amount = $obj->get_recommendations_amount();
		$issues_list = $obj->generate_widget_list( 10 );

		$subject = sprintf( __('%d WordPress Hardening recommendations for %s',  'whp'), $resom_amount, get_option('home') );

		 // getting list of users
		 $report_mail_array = get_option('whp_custom_admin_report_email');
		 $report_mails = explode(',',$report_mail_array);
		if( count($report_mails) > 0 ){
			foreach( $report_mails as $report_mail ){
				$content = sprintf( __('
				<p>Howdy! %s</p>
			
				<p>Greetings!</p>
			
				<p>As a part of our routine WordPress Hardening check, we ran a scan on %s. We found that there are still some improvements that can be made to harden the site. I have listed them below for your convenience:</p>
						
				%s
								
				<p style="text-align:center;"><a style="margin:10px auto; font-size:18px;" href="%s">Check the details here</a></p>
				
				<p>To ensure the maximum hardening of your WordPress website, we recommend that you implement the fixes at the earliest. You’ll find details of the fixes, under the ‘Hardening Audit’ tab in your WordPress backend.</p>
			
				<p>Thanks,</p>
				<p>Ananda from Astra</p>

				',  'whp'), '', get_option('home'), $issues_list, admin_url('/admin.php?page=wphwp_harden#audit_bottom_block') ); 

				$headers[]= "MIME-Version: 1.0" . "\r\n";
				$headers[]= "Content-type:text/html;charset=UTF-8" . "\r\n";

				wp_mail( $report_mail, $subject, $content, $headers);
			
			}
		}
		 
	
		
  }


// add admin navbar
add_action('admin_bar_menu', 'whp_add_toolbar_items', 100);
function whp_add_toolbar_items($admin_bar){

	$obj = new tableViewOutput();
	$health = $obj->calculate_site_health();

	if( $health < 90 && $obj->last_results != '' ){
		$admin_bar->add_menu( array(
			'id'    => 'wh_checking',
			'title' =>  '<i class="fa fa-exclamation-triangle is_fa icon_orange" aria-hidden="true"></i> '.__('Improve Hardening', 'whp'),
			'href'  => admin_url('admin.php?page=wphwp_harden'),
			
		));
	}
    
   
}

 
// wp enum scann callback
function shapeSpace_check_enum($redirect, $request) {
	// permalink URL format
	if (preg_match('/\?author=([0-9]*)(\/*)/i', $request)) die();
	else return $redirect;
}

// login fix
$fixer_options = get_option('whp_fixer_option');
// custom login 
	 
if( $fixer_options['change_login_url'] == 'on' ){
	
	new WHP_Change_Login_URL;
}
 

// fixers options processing cont and hooks based
add_action('init', 'whp_fixers_processing');
function whp_fixers_processing(){
	$fixer_options = get_option('whp_fixer_option');


	
	// file edition
	if( $fixer_options['disable_file_editor'] == 'on' && !defined('DISALLOW_FILE_EDIT')){
		define('DISALLOW_FILE_EDIT', true);
	}

	// disable xml rpc
	if( $fixer_options['disable_xml_rpc'] == 'on' ){
 
		if(  substr_count( strtolower( $_SERVER['REQUEST_URI'] ), strtolower( 'xmlrpc' ) ) ){
			die();
		}

		// Disable use XML-RPC
		add_filter('xmlrpc_enabled', '__return_false');

		// Disable X-Pingback to header
		add_filter( 'wp_headers', 'disable_x_pingback' );
		function disable_x_pingback( $headers ) {
			unset( $headers['X-Pingback'] );

		return $headers;
		}
	}

	// disable api 
	if( $fixer_options['disable_json_api'] == 'on' ){
		add_filter( 'rest_authentication_errors', function( $result ) {
			if ( ! empty( $result ) ) {
				return $result;
			}
			if ( ! is_user_logged_in() ) {
				return new WP_Error( 'rest_not_logged_in', 'You are not currently logged in.', array( 'status' => 401 ) );
			}
			return $result;
		});
	}

	// file edition
	if( $fixer_options['disable_file_editor'] == 'on' && !defined('DISALLOW_FILE_EDIT')){
		define('DISALLOW_FILE_EDIT', true);
	}
	
	


	// user enumeration patch
	if( $fixer_options['stop_user_enumeration'] == 'on' ){
		if (!is_admin()) {
			// default URL format
			if (isset($_SERVER['QUERY_STRING']) && preg_match('/author=([0-9]*)/i', $_SERVER['QUERY_STRING'])){
			 	wp_redirect( get_option('home'), 302 ); exit;
			}
			add_filter('redirect_canonical', 'shapeSpace_check_enum', 10, 2);
		}
	}

    // hide wp meta & version
    if( $fixer_options['disable_app_passwords'] == 'on' ){
        add_filter( 'wp_is_application_passwords_available', '__return_false' );
    }

	// hide wp meta & version
	if( $fixer_options['hide_wp_version_number'] == 'on' ){
		add_filter( 'the_generator', '__return_null' );
		
		
 
		add_action('template_redirect', 'remove_yoast_seo_comments_fn', 9999);
		
		
	}

	// hide wpml
	if( $fixer_options['remove_wpml_meta_gen_tag'] == 'on' ){
		if( function_exists('remove_wpml_generator') ){
			add_action( 'wp_head', 'remove_wpml_generator', 0 );
		}
		
	}

	// hide revo
	if( $fixer_options['remove_revo_slider_meta_gen_tag'] == 'on' ){
		add_filter( 'revslider_meta_generator', 'remove_revslider_meta_tag' );
	}

	// hide vc
	if( $fixer_options['remove_vc_meta_gen_tag'] == 'on' ){
		if ( class_exists( 'Vc_Manager' ) || class_exists( 'Vc_Base' ) ) {
            remove_action('wp_head', array(visual_composer(), 'addMetaData'));
		}
	}

	// hide css
	if( $fixer_options['remove_css_meta_gen_tag'] == 'on' ){
		add_filter('style_loader_src', 'whp_remove_appended_version_script_style', 20000);
	}

	// hide js
	if( $fixer_options['remove_js_meta_gen_tag'] == 'on' ){
		add_filter('script_loader_src', 'whp_remove_appended_version_script_style', 20000);
	}
	
}

// menu redirects
/*
add_action('init', 'whp_init_redirect');
function whp_init_redirect(){
	

	if( $_GET['page'] == 'wphwp_harden_help' ){
		$url = 'https://www.getastra.com/kb/kb/wp-hardening/';
	}
	if( $_GET['page'] == 'wphwp_harden_upgrade' ){
		$url = 'https://www.getastra.com/?ref=wp-hardening';
	}
	if( $url ){
		wp_Redirect( $url, 302 );
		exit();
	}
	
}
*/

// dashboard widget
add_action('wp_dashboard_setup', 'whp_custom_dashboard_widgets');
  
function whp_custom_dashboard_widgets() {
global $wp_meta_boxes;
 
	wp_add_dashboard_widget('custom_help_widget', '<i class="fa fa-exclamation-circle icon_red" aria-hidden="true"></i> WordPress Hardening Overview', 'whp_custom_dashboard_help');
}
 
function whp_custom_dashboard_help() {
echo '
	<p>WP Harden checks for security fixes you can make to enhance your website\'s security against hackers and bots</p>';
	 
	$obj = new tableViewOutput();

	 
	$health = $obj->calculate_site_health();

	if( $health < 90 && $obj->last_results != '' ){

		if( $obj->check_exists()  ){

			if( $health  < 90 ){
				echo $obj->generate_widget_list();
				echo '
				<div class="row_marg_10">
					<a href="'.admin_url( '/admin.php?page=wphwp_harden#audit_bottom_block' ).'" class="button button-primary">'.__('View all recommendations').'</a>
				</div>';
				echo '
				<div class="subinfo">
				'.__('These recommendations are provided by WP Hardening, and are specific to your site. For more information, <a href="https://astra.sh/wp-knowledge-base">read the documentation</a> or  <a href="https://astra.sh/wp-malware-removal">contact us</a>').'
				</div>
				';
			}else{
			 
				echo '
				<div class="row_marg_10">
					'.__('Your site is well protected.', 'whp').'
				</div>';
				
			}

			
		}else{
		
			echo '
			<div class="row_marg_10">
				'.__('You didnt made scan yet. Scan system to check issues.','whp').'
			</div>
			<div class="row_marg_10">
				<a href="'.admin_url( '/admin.php?page=wphwp_harden' ).'" class="button button-primary">'.__('Make Audit').'</a>
			</div>';
			echo '
			<div class="subinfo">
			'.__('These recommendations are provided by WP Hardening, and are specific to your site. For more information, <a href="https://astra.sh/wp-knowledge-base">read the documentation</a> or  <a href="https://astra.sh/wp-malware-removal">contact us</a>').'
			</div>
			';
		}
	}
	
}
 

// show admin toification
function whp_general_admin_notice(){
	global $pagenow;
	global $current_user;
	//delete_user_meta( $current_user->ID, 'hide_secure_subs');
	//delete_user_meta( $current_user->ID, 'whp_subscribed_email');
	$is_subs = get_user_meta( $current_user->ID, 'hide_secure_subs', true);

	$is_subscribed = get_user_meta( $current_user->ID, 'whp_subscribed_email', true );
	if( $is_subs == '1' || ( $is_subscribed != '' && $is_subscribed ) ){
		return false;
	}

   // if ( $pagenow == 'options-general.php' ) {
		 echo '
			<style>
			.admin_notice_container{
				overflow:hidden;
				
			}
			.get_secure_notice{
				padding:0px !important; 
			}
			.admin_notice_container .img_block{
				float:left;
				width:10%;
				max-width: 96px;
			}
			.admin_notice_container .content_block{
				float: left;
				width: 90%;
				padding: 20px 20px 10px 20px;
				-webkit-box-sizing: border-box;
				-moz-box-sizing: border-box;
				box-sizing: border-box;
				text-align: center;
				font-size: 16px; 
			}
			.admin_notice_container .email_block{
				text-align: center;
				float: left;
				width: 90%;
				padding: 10px 20px;
				-webkit-box-sizing: border-box;
				-moz-box-sizing: border-box;
				box-sizing: border-box;
			}
			.admin_notice_container .email_block #no_subscription{
				margin:0px 20px;
			}
			</style>	 
		 <div class="notice notice-warning get_secure_notice">
			 <div class="admin_notice_container">
			 <img class="img_block" src="'.plugins_url( '/images/cta.png', __FILE__  ).'"   />
				 <div class="content_block">
					
				 	Security can be complicated & hacked websites are messy. Stay updated & don\'t get hacked.
				 </div>
				 <div class="email_block">
					 <input type="text" id="user_subscribe_email" value="'.$current_user->user_email.'">
					 <button type="button" class="button button-primary" id="subscribe_secure"  >'.__( 'Send Me Security Updates' ).'</button>
					 <a href="#" id="no_subscription">'.__( 'No, thanks' ).'</a>
				 </div>
				 
			</div>
         </div>';
   // }
}
add_action('admin_notices', 'whp_general_admin_notice');



?>