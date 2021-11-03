<?php

if (!defined('ABSPATH')) exit ('Peekaboo!');

if( !class_exists('whpFormElementsClass') ){
	class whpFormElementsClass{
		
		protected  $type;
		protected  $settings;
		protected  $content;
		protected  $fixerOptions;

		function __construct( $type, $parameters, $value ){
	 
			$this->type = $type;
			$this->parameters = $parameters;
			$this->value = $value;
			$this->fixerOptions = get_option('whp_fixer_option');


			$this->generate_result_block();
 
		}

		function getFixerOption($key, $default = ''){
            if(isset($this->fixerOptions[$key])){
                return $this->fixerOptions[$key];
            }else{
                return $default;
            }
        }

		function generate_result_block(){
			$out = '';
			switch( $this->type ){
				
				case "audit_processing":
					$out .= '
					<div class="audit_process_container" style="display:none;">
						<div class="audit_top_block">
							<div class="left_col">
								
								<div class="big_font">'.__('WordPress Hardening Audit', 'whp').'</div>
								<div class="small_font">'.__('WP Harden performs a quick audit to assist you in Hardening your WordPress website', 'whp').'</div>
								<div class="button_row">
									<button type="button" class="big_white_button" id="start_new_audit" >'.__('Start a New Audit', 'whp').' <i class="fa fa-play" aria-hidden="true"></i></button>
									<button type="button" class="big_blue_button button_link_local" data-url="'.admin_url('/admin.php?page=wphwp_harden_fixers').'" >'.__('Security Fixers', 'whp').' <i class="fa fa-wrench" aria-hidden="true"></i></button>
								</div>

								<!--
								<div class="text_row">
									<a href="">'.__('Modify Settings', 'whp').' <i class="fa fa-play" aria-hidden="true"></i></a>
								</div>
								-->
								<div class="button_row">
									<button type="button"  class="small_blue_button button_link" data-url="https://astra.sh/wp-malware-removal" >'.__('Get Malware Cleanup', 'whp').'</button>
									<button type="button"  class="small_blue_button button_link" data-url="https://astra.sh/wp-knowledge-base" >'.__('View Help Docs', 'whp').'</button>
								</div>
							</div>
					 
							<div class="right_col" id="site_health_table">
								';
									$table = new tableViewOutput();
									$out .= $table->process_results();
									$out .= $table->generate_table_view();
					 
									$out .= '
								 
							</div>
							<div class="clearfix"></div>
						</div>

 
						<div class="audit_bottom_block" id="audit_bottom_block">
							<div class="content_block">
								<div class="header_row">'.__('Audit Recommendations', 'whp').'</div>
							</div>
							<div class="row_block">
								<span class="text_block">'.__('We found the following improvements for your website.<br/>
								Please review them below!', 'whp').'</span>
								<button type="button" class="button_light_blue button_link" data-url="https://astra.sh/wp-malware-removal">'.__('Request a malware cleanup', 'whp').' <i class="fa fa-play" aria-hidden="true"></i></button>
							</div>

							<div class="tabs_head_block">
								<div class="head_tab active"  >
									<a class="tab_link" href="#recommend_tab">'.__('Recommendations', 'whp').'</a>
								</div>
								<div class="head_tab">
									<a class="tab_link" href="#passed_tab">'.__('Passed Test', 'whp').'</a>
								</div>
								<div class="head_tab">
									<a class="tab_link" href="#settings">&#9881; '.__('Settings', 'whp').'</a>
								</div>
							</div>';

							$tmp = new tableViewOutput();
							$tmp->process_results();							
							$res_array = $tmp->return_data();

							$switch_options = $this->fixerOptions;


					 		$out .= '
							<div class="tabs_content_block">
								<div class="single_tab recommendations_tab" id="recommend_tab">
									'.$res_array['error'].'
								</div>
								<div class="single_tab passed_tab" id="passed_tab">
									'.$res_array['success'].'
								</div>
								<div class="single_tab settings_tab" id="settings">
												<div class="row switcher_line">
													<div class="switcher">
														<div class="switch_cont">
															<label class="whp-switch-wrap">
																<input type="checkbox" '.( $this->getFixerOption('schedule_audit', 'off') == 'on' ? ' checked ' : '' ).'  value="on" class="trace_switch" id="schedule_audit" name="schedule_audit" />
																<div class="whp-switch"></div>
															</label>
														</div>
													</div>
													<div class="description" style="min-width:156px;" data-balloon-length="large" aria-label="Configure how often you would like the Hardening Audit to be run" data-balloon-pos="up">Schedule the Audit</div>
													<div class="slug_container">
													<select id="custom_admin_schedule_audit" '.( $this->getFixerOption('schedule_audit', 'off') == 'on' ? 'disabled' : '' ).' style="background-color: #fafafa;border: solid 1px #ebebeb; margin-left:10px;">
														<option value="every day" '. ( get_option( 'whp_custom_admin_schedule_audit', 'every week') == 'every day' ? 'selected' : '' ) . '>every day</option>
														<option value="every week" ' . ( get_option( 'whp_custom_admin_schedule_audit', 'every week') == 'every week' ? 'selected' : '' ) . '>every week</option>
														<option value="every month" ' . ( get_option( 'whp_custom_admin_schedule_audit', 'every week') == 'every month' ? 'selected' : '' ) . '>every month</option>
													</select>
														  
													</div>
												</div>
								
							

                                            <div class="row switcher_line">
												<div class="switcher">
													<div class="switch_cont">
														<label class="whp-switch-wrap">
															<input type="checkbox" '.( $this->getFixerOption('report_email', 'off') == 'on' ? ' checked ' : '' ).'  value="on" class="trace_switch" id="report_email" name="report_email" />
															<div class="whp-switch"></div>
														</label>
													</div>
												</div>
												<div class="description" data-balloon-length="large" aria-label="If you would like multiple people to receive email updates, enter up to 15 email ids separated by a comma." data-balloon-pos="up">Send Email Report to</div>
												<div class="slug_container">
													 <textarea data-balloon-pos="up" style="height:100px; background-color: #fafafa; border: solid 1px #ebebeb;width:400px; margin-left:10px;"  id="custom_admin_report_email" '.( $this->getFixerOption('report_email', 'off') == 'on' ? 'readonly' : '' ).'  placeholder="'.__('Enter your email address. If you would like multiple people to receive email updates, enter up to 15 email ids separated by a comma.','whp').'">'.get_option( 'custom_admin_report_email').'</textarea>
												</div>
											</div>
											</div>
</div>
						</div>
					</div>						';
				break;
				case "security_fixers":
					$out .= '
					<div class="audit_process_container fixers_block" style="display:none;">
						<div class="audit_top_block">
							<div class="left_col">
								
								<div class="big_font">'.__('WordPress Security Fixers', 'whp').'</div>
								<div class="small_font">'.__('Further strengthen your WordPress site and make it difficult for hackers, enable security fixers', 'whp').'</div>
						 
							</div>
						 
							<div class="right_col ">
								<div class="div_trans_cont">
									<div class="col_block">
										 <div class="col_3">';


										$results = tableViewOutput::get_processed_fixers();

										$out .= '
										 	<div class="inner_container">
											 	<div class="big_number " id="active_fixers">'.$results['on'].'</div>
											 	<div class="small_text" >'.__('Activated', 'whp').'</div>
											 </div>
										 </div>
										 <div class="col_3">
										 	<div class="inner_container">
												<div class="big_number" id="unactive_fixers">'.$results['off'].'</div>
												 <div class="small_text">'.__('Disabled', 'whp').'</div>
											</div>
										 </div>
										 <div class="col_3">
										 	<div class="inner_container">
												 ';
												 if( issuesScanClass::is_firewall_installed() ){
													$out .= '
													<div class="big_number">
<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="39px" viewBox="0 0 27 39">
    <path fill="#24BC94" fill-rule="nonzero" d="M13.067 0C7.6 0 1.914 1.21 1.914 1.21l-.005.005C.8 1.433.001 2.389 0 3.5v7c0 12.131 11.904 17.201 11.904 17.201a2.409 2.409 0 0 0 2.32 0h.005c.001 0 11.904-5.07 11.904-17.201v-7c0-1.114-.8-2.073-1.914-2.29 0 0-5.686-1.21-11.152-1.21zm7.127 7c.304 0 .608.114.84.342a1.15 1.15 0 0 1 0 1.65l-8.981 8.82c-.224.22-.525.342-.84.342-.315 0-.618-.122-.84-.342l-4.102-4.028a1.15 1.15 0 0 1 0-1.65 1.202 1.202 0 0 1 1.68 0l3.262 3.204 8.141-7.996c.232-.228.536-.342.84-.342z"/>
</svg>
</div>
												 <div class="small_text whp-fixers-fw-status">'.__('Firewall Detected', 'whp').'</div>';
												 }else{
													$out .= '
													<div class="big_number"><i class="fa fa-exclamation-triangle icon_orange_color" aria-hidden="true"></i></div>
													<div class="small_text whp-fixers-fw-status"><a href="https://www.getastra.com/" target="_blank">'.__('Enable Firewall', 'whp').'</a></div>';
												 }
												 
												 $out .= '
											</div>
										 </div>
									</div>
								</div>
							</div>
							<div class="clearfix"></div>
						</div>

 
						<div class="audit_bottom_block">
							<div class="content_block">
								<div class="header_row">'.__('Security Fixers', 'whp').'</div>
								<input type="hidden" id="is_fixer" value="1" />
							</div>
							<div class="row_block">
								<span class="text_block">'.__('You can enable/disable fixers as per your requirement. We’ve already enabled important ones for you.', 'whp').'</span>
								<button type="button" class="button_light_blue" id="expand_all">'.__('Expand All', 'whp').'</button>
								<button type="button" class="button_light_blue" id="collapse_all">'.__('Collapse All', 'whp').'</button>
							</div>
                            
                            <div class="alert alert-success alert-dismissible fade" role="alert" id="whp-login-change-success" style="display: none;">
                                <span class="msg"></span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>                                
                            </div>

							<div class="tabs_content_block">
								<div class="single_tab recommendations_tab" id="recommend_tab">
									
								';

								$data_structure = array(
									array(
										'title' => __( 'Admin & API Security', 'whp'),
										'variants' => array(
                                            array(
                                                'title' => __( 'Disable WordPress Application Passwords', 'whp'),
                                                'info' => __('Hackers & bad bots can easily find usernames in WordPress by visiting URLs like yourwebsite.com/?author=1. This can significantly help them in performing larger attacks like Bruteforce & SQL injection.', 'whp'),
                                                'slug' => 'disable_app_passwords',
                                            ),
                                            array(
                                                'title' => __('Stop User Enumeration', 'whp'),
                                                'info' => __('Hackers & bad bots can easily find usernames in WordPress by visiting URLs like yourwebsite.com/?author=1. This can significantly help them in performing larger attacks like Bruteforce & SQL injection.', 'whp'),
                                                'slug' => 'stop_user_enumeration',
                                            ),
											 
												array(
													'title' => __( 'Change Login URL', 'whp'),
													'info' => __('Prevent admin password brute-forcing by changing the URL for the wp-admin login area. You can change the url only when this fixer is disabled.', 'whp'),
													'slug' => 'change_login_url',
													'type' => 'text'
												),
												array(
													'title' => __( 'Disable XMLRPC', 'whp'),
                                                    'info' => __("XMLRPC is often targeted by bots to perform brute force & DDoS attacks (via pingback) causing considerable stress on your server. However, there are some services which rely on xmlrpc. Be sure you definitely do not need xmlrpc before disabling it. If you are using Astra firewall, then you’re safe against xmlrpc attacks automatically.", 'whp'),
													'slug' => 'disable_xml_rpc'
												),
												array(
													'title' => __( 'Disable WP API JSON', 'whp'),
                                                    'info' => __("Since 4.4 version, WordPress added JSON REST API which largely benefits developers. However, it’s often targeted for bruteforce attacks just like in the case of xmlrpc. If you are not using it, best is to disable it.", 'whp'),
													'slug' => 'disable_json_api'
												),
												array(
													'title' => __( 'Disable File Editor', 'whp'),
                                                    'info' => __("If a hacker is able to get access to your WordPress admin, with the file editor enabled it becomes quite easy for them to add malicious code to your theme or plugins. If you are not using this, it’s best to keep the file editor disabled.", 'whp'),
													'slug' => 'disable_file_editor'
												),
						 
										),
									),
									array(
										'title' => __( 'Disable Information Disclosure & Remove Meta information', 'whp'),
										'sub_title' => __( 'Please clear your WordPress cache for these changes to reflect', 'whp'),
										
										'variants' => array(
											array(
											'title' => __( 'Hide WordPress Version Number', 'whp'),
                                            'info' => __('This gives away your WordPress version number making life of a hacker simple as they’ll be able to find targeted exploits for your WordPress version. It’s best to keep this hidden, enabling the button shall do that.', 'whp'),
											'slug' => 'hide_wp_version_number'
											),
											array(
												'title' => __( 'Remove WordPress Meta Generator Tag', 'whp'),
                                                'info' => __("The WordPress Meta tag contains your WordPress version number which is best kept hidden", 'whp'),
												'slug' => 'remove_wp_meta_gen_tag'
											),
											array(
												'title' => __( 'Remove WPML (WordPress Multilingual Plugin) Meta Generator Tag', 'whp'),
                                                'info' => __("This discloses the WordPress version number which is best kept hidden.", 'whp'),
												'slug' => 'remove_wpml_meta_gen_tag'
											),
											array(
												'title' => __( 'Remove Slider Revolution Meta Generator Tag', 'whp'),
                                                'info' => __("Slider revolution stays on the radar of hackers due to its popularity. An overnight hack in the version you’re using could lead your website vulnerable too. Make it difficult for hackers to exploit the vulnerabilities by disabling version number disclosure here.", 'whp'),
												'slug' => 'remove_revo_slider_meta_gen_tag'
											),
											array(
												'title' => __( 'Remove WPBakery Page Builder Meta Generator Tag', 'whp'),
                                                'info' => __("Common page builders often are diagnosed with a vulnerability putting your website’s security at risk. With this toggle enabled, the version of these page builders will be hidden making it difficult for hackers to find if you’re using a vulnerable version.", 'whp'),
												'slug' => 'remove_vc_meta_gen_tag'
											),
											array(
												'title' => __( 'Remove Version from Stylesheet', 'whp'),
                                                'info' => __("Many CSS files have the WordPress version number appended to their source, for cache purposes. Knowing the version number allows hackers to exploit known vulnerabilities.", 'whp'),
												'slug' => 'remove_css_meta_gen_tag'
											),
											array(
												'title' => __( 'Remove Version from Script', 'whp'),
                                                'info' => __("Many JS files have the WordPress version number appended to their source, for cache purposes. Knowing the version number allows hackers to exploit known vulnerabilities.", 'whp'),
												'slug' => 'remove_js_meta_gen_tag'
											),
										 
										),
									),
									array(
										'title' => __( 'Server Hardening', 'whp'),
										'variants' => array(
								 
											array(
												'title' => __( 'Hide Directory Listing of WP includes', 'whp'),
                                                'info' => __("WP-includes directory gives away a lot of information about your WordPress to hackers. Disable it by simply toggling the option to ensure you make reconnaissance of hackers difficult.", 'whp'),
												'slug' => 'hide_includes_dir_listing'
											),
						 
										),
									),
 



									array(
										'title' => __( 'Security Headers', 'whp'),
										'variants' => array(

											array(
												'title' => __( 'Clickjacking Protection', 'whp'),
                                                'info' => __("Protect your site from clickjacking. Use deny mode to block all iframes, and Same mode to only allow iframes from your own domain. Clickjacking is an attack that tricks a user into clicking a webpage element which is invisible or disguised as another element.", 'whp'),
												'slug' => 'clickjacking_protection'
											),
											array(
												'title' => __( 'XSS Protection', 'whp'),
                                                'info' => __("Add the HTTP X-XSS-Protection response header so that browsers such as Chrome, Safari, Microsoft Edge stops pages from loading when they detect reflected cross-site scripting (XSS) attacks.", 'whp'),
												'slug' => 'xss_protection'
											),
											array(
												'title' => __( 'Content Sniffing protection', 'whp'),
                                                'info' => __("Add the X-Content-Type-Options response header to protect against MIME sniffing vulnerabilities. Such vulnerabilities can occur when a website allows users to upload content to a website, however the user disguises a particular file type as something else. This can give them the opportunity to perform cross-site scripting and compromise the website.", 'whp'),
												'slug' => 'content_sniffing_protection'
											),

											array(
												'title' => __( 'HTTP only & Secure flag', 'whp'),
                                                'info' => __("Enable the HttpOnly and secure flags to make the cookies more secure. This instructs the browser to trust the cookie only by the server, which adds a layer of protection against XSS attacks.", 'whp'),
												'slug' => 'http_secure_flag'
											),
						 
										),
									),
 									


								);

								foreach( $data_structure as $single_top ){
									$out .= '
									<div class="row single_status_block">
										<div class="fixer_name">'.$single_top['title'].'
										</div>
										

										<div class="row_control">
											<div class="hide_control">'.__('Hide', 'whp').' <i class="fa fa-chevron-up" aria-hidden="true"></i></div>
											<div class="show_control">'.__('Details', 'whp').' <i class="fa fa-chevron-down" aria-hidden="true"></i></div>
										</div>
										 
										<div class="col-md-12 details_block fixers_block">
										<div class="fixer_sub">'.( isset($single_top['sub_title']) ? $single_top['sub_title'] : '' ).'</div>';



										$switch_options = get_option('whp_fixer_option');

										foreach( $single_top['variants'] as $single_line ){


											if( $single_line['slug'] == 'change_login_url' ){
												$out .= '
											<div class="row switcher_line">
												<div class="switcher">
													<div class="switch_cont">
														<label class="whp-switch-wrap">
															<input type="checkbox" '.( $switch_options[$single_line['slug']] == 'on' ? ' checked ' : '' ).'  value="on" class="trace_switch" id="'.$single_line['slug'].'" name="'.$single_line['slug'].'" />
															<div class="whp-switch"></div>
														</label>
													</div>
												</div>
												
												<div class="description" data-balloon-length="large" aria-label="' . $single_line['info'] . '" data-balloon-pos="up">'.$single_line['title'].'</div>

												<div class="slug_container">
													 <input type="text" data-balloon-pos="up" id="custom_admin_slug" '.( $switch_options[$single_line['slug']] == 'on' ? ' readonly ' : '' ).' value="'.get_option( 'whp_admin_page').'" placeholder="'.__('Enter new slug','whp').'">
												</div>
											</div>';
											}elseif( $single_line['slug'] == 'clickjacking_protection' ){

                                                $out .= '
											<div class="row switcher_line">
												<div class="switcher">
													<div class="switch_cont switch-accodin">
																<form action="" id="searchTypeToggle">
																  <div></div>
																  <label class="'. ( get_option( 'whp_radio_clickjacking_protection') == '1' ? 'selected' : '' ) . '">
																    <input type="radio" class="trace_switch" name="radio_clickjacking_protection" id="radio_clickjacking_protection" data-location="0" value="1" >
																    <div>Off</div>
																  </label>
																  <label class="'. ( get_option( 'whp_radio_clickjacking_protection') == '2' ? 'selected' : '' ) . '">
																    <input type="radio" class="trace_switch" name="radio_clickjacking_protection" id="radio_clickjacking_protection" data-location="calc(100% - 8px)" value="2" >
																    <div>Deny</div>
																  </label>
																  <label class="'. ( get_option( 'whp_radio_clickjacking_protection') == '3' ? 'selected' : '' ) . '">
																    <input type="radio" class="trace_switch" name="radio_clickjacking_protection" id="radio_clickjacking_protection" data-location="calc(200% - 12px)" value="3" >
																    <div>Same</div>
																  </label>
																</form>
													</div>
												</div>
												<div class="description" data-balloon-length="large" aria-label="' . $single_line['info'] . '" data-balloon-pos="up">'.$single_line['title'].'</div>
											</div>';
                                            }else{
												$out .= '
											<div class="row switcher_line">
												<div class="switcher">
													<div class="switch_cont">
														<label class="whp-switch-wrap">
															<input type="checkbox" '.( $switch_options[$single_line['slug']] == 'on' ? ' checked ' : '' ).'  value="on" class="trace_switch" id="'.$single_line['slug'].'" name="'.$single_line['slug'].'" />
															<div class="whp-switch"></div>
														</label>
													</div>
												</div>
												<div class="description" data-balloon-length="large" aria-label="' . $single_line['info'] . '" data-balloon-pos="up">'.$single_line['title'] . '</div>
											</div>';
											}
											
										 
											
										}
											
											 $out .= '
										</div>
									
									</div>';
								}
								
								$out .= '
								</div>
								 
							</div>


						</div>
					</div>
						';
				break;
				case "sheet_picker":
					$out .= '
					<div class="row">
						<div class="col-6">
							<div class="form-group">  
					 
							  <input type="text"  class="form-control '.$this->parameters['class'].'"  name="'.$this->parameters['name'].'" id="'.$this->parameters['id'].'" placeholder="'.$this->parameters['placeholder'].'" value="'.( $this->value && $this->value != '' ? esc_html( stripslashes( $this->value ) ) : $this->parameters['default'] ).'">  
							  <p class="help-block">'.$this->parameters['sub_text'].'</p>  
							
							</div> 
							
							<div class="form-group">  
					 
							  <input type="text"  class="form-control '.$this->parameters['class'].'"  name="'.$this->parameters['name'].'" id="'.$this->parameters['id'].'" placeholder="Recipients Email">  
							  <p class="help-block">'.$this->parameters['sub_text'].'</p>  
							
							</div>
						</div> 
					</div>
						';
				break;
				case "separator":
					$out .= '
					<div class="'.( $this->parameters['width'] ? $this->parameters['width'] : 'col-12' ).'">
						<div class="lead">'.$this->parameters['title'].'</div> 
					</div>
						';
				break;
				
				case "text":
						$out .= '
					<div class="'.( $this->parameters['width'] ? $this->parameters['width'] : 'col-12' ).'">
						<div class="form-group">  
							<label class="control-label" for="'.$this->parameters['id'].'">'.$this->parameters['title'].'</label>  
							
							  <input type="text"  class="form-control '.$this->parameters['class'].'"  name="'.$this->parameters['name'].'" id="'.$this->parameters['id'].'" placeholder="'.$this->parameters['placeholder'].'" value="'.( $this->value && $this->value != '' ? esc_html( stripslashes( $this->value ) ) : $this->parameters['default'] ).'">  
							  <p class="help-block">'.$this->parameters['sub_text'].'</p>  
							
						  </div> 
					</div>
						';
				break;
				case "button":
						$out .= '
					<div class="'.( $this->parameters['width'] ? $this->parameters['width'] : 'col-12' ).'">
						<div class="form-group">  
							<label class="control-label" for="">&nbsp;</label>  
							
							  <a class="'.( $this->parameters['class'] ? $this->parameters['class'] : 'btn btn-success' ).'" href="'.$this->parameters['href'].'"   >'.$this->parameters['title'].'</a>  
							  
							
						</div> 
					</div>
						';
				break;
				case "select":
						$out .= '
					<div class="'.( $this->parameters['width'] ? $this->parameters['width'] : 'col-12' ).'">
						<div class="form-group">  
							<label class="control-label" for="'.$this->parameters['id'].'">'.$this->parameters['title'].'</label>  
							 
							  <select  style="'.$this->parameters['style'].'" class="form-control '.$this->parameters['class'].'" name="'.$this->parameters['name'].'" id="'.$this->parameters['id'].'">' ; 
							  if( count( $this->parameters['value'] ) > 0 )
							  foreach( $this->parameters['value'] as $k => $v ){
								  $out .= '<option value="'.$k.'" '.( $this->value  == $k ? ' selected ' : ' ' ).' >'.$v.'</option> ';
							  }
						$out .= '		
							  </select>  
							  <p class="help-block">'.$this->parameters['sub_text'].'</p> 
							</div>  
					</div>	 
						';
				break;
				case "checkbox":
						$out .= '
					<div class="'.( $this->parameters['width'] ? $this->parameters['width'] : 'col-12' ).'">
						<div class="form-group">  
							<label class="control-label" for="'.$this->parameters['id'].'">'.$this->parameters['title'].'</label>  
						
							  <label class="checkbox">  
								<input  class="'.$this->parameters['class'].'" type="checkbox" name="'.$this->parameters['name'].'" id="'.$this->parameters['id'].'" value="on" '.( $this->value == 'on' ? ' checked ' : '' ).' > &nbsp; 
								'.$this->parameters['text'].'  
								<p class="help-block">'.$this->parameters['sub_text'].'</p> 
							  </label>  
							 
						  </div>  
					</div>
						';
				break;
				case "radio":
						$out .= '
					<div class="'.( $this->parameters['width'] ? $this->parameters['width'] : 'col-12' ).'">
						<div class="form-group">  
							<label class="control-label" for="'.$this->parameters['id'].'">'.$this->parameters['title'].'</label>';
								foreach( $this->parameters['value'] as $k => $v ){
									$out .= '
									<label class="radio">  
										<input  class="'.$this->parameters['class'].'" type="radio" name="'.$this->parameters['name'].'" id="'.$this->parameters['id'].'" value="'.$k.'" '.( $this->value == $k ? ' checked ' : '' ).' >&nbsp;  
										'.$v.'  
										<p class="help-block">'.$this->parameters['sub_text'].'</p> 
									  </label> ';
								}
							$out .= '
							
						  </div>  
					</div>
						';
				break;
				case "textarea":
						$out .= '
					<div class="'.( $this->parameters['width'] ? $this->parameters['width'] : 'col-12' ).'">
						<div class="form-group">  
							<label class="control-label" for="'.$this->parameters['id'].'">'.$this->parameters['title'].'</label>  
						
							  <textarea style="'.$this->parameters['style'].'" class="form-control '.$this->parameters['class'].'" name="'.$this->parameters['name'].'" id="'.$this->parameters['id'].'" rows="'.$this->parameters['rows'].'">'.( $this->parameters['name'] && $this->parameters['name'] != '' ?  esc_html( stripslashes( $this->value ) ) : $this->parameters['default'] ).'</textarea>  
							  <p class="help-block">'.$this->parameters['sub_text'].'</p> 
						 
						  </div> 
					</div>
						';
				break;
				case "multiselect":
						$out .= '
					<div class="'.( $this->parameters['width'] ? $this->parameters['width'] : 'col-12' ).'">
						<div class="form-group">  
							<label class="control-label" for="'.$this->parameters['id'].'">'.$this->parameters['title'].'</label>  
							 
							  <select  multiple="multiple" style="'.$this->parameters['style'].'" class="form-control '.$this->parameters['class'].'" name="'.$this->parameters['name'].'[]" id="'.$this->parameters['id'].'">' ; 
							  foreach( $this->parameters['value'] as $k => $v ){
								  $out .= '<option value="'.$k.'" '.( @in_array( $k, $this->value )   ? ' selected ' : ' ' ).' >'.$v.'</option> ';
							  }
						$out .= '		
							  </select>  
							  <p class="help-block">'.$this->parameters['sub_text'].'</p> 
							 
						  </div>  
					</div>
						';
				break;
				case "wide_editor":
					$out .= '
					<div class="'.( $this->parameters['width'] ? $this->parameters['width'] : 'col-12' ).'">
						<div class="form-group">  
							<label class="control-label" for="input01">'.$this->parameters['title'].'</label>
							<div class="form-control1">
							';  
							
							ob_start();
							wp_editor( $this->value, $this->parameters['name'] );
							$editor_contents = ob_get_clean();	
						 
							$out .= $editor_contents;  
						$out .= '
							</div>
						  </div> 
					</div>';	 
					 
				break;
				case "file":
						$out .= '
					<div class="'.( $this->parameters['width'] ? $this->parameters['width'] : 'col-12' ).'">
						<div class="form-group">  
							<label class="control-label" for="'.$this->parameters['id'].'">'.$this->parameters['title'].'</label>  
				 
							<input type="file" class="form-control-file '.$this->parameters['class'].'" name="'.$this->parameters['name'].''.( $this->parameters['multi'] ? '[]' : '' ).'" id="'.$this->parameters['id'].'" '.( $this->parameters['multi'] ? ' multiple ' : '' ).' >
							  
							  <p class="help-block">'.$this->parameters['sub_text'].'</p> 
						 
						  </div>
					</div>
						';
				break;
				case "mediafile_single":
					$attach_url = wp_get_attachment_url( $this->value );
					
					$out .= '
					<div class="'.( $this->parameters['width'] ? $this->parameters['width'] : 'col-12' ).'">
						<div class="form-group media_upload_block">  
						<label class="control-label" for="input01">'.$this->parameters['title'].'</label>  
						 
						  <input type="hidden" class="form-control input-xlarge mediafile_single item_id" name="'.$this->parameters['name'].'" id="'.$this->parameters['name'].'" value="'.$this->value.'"> 
						  
						
						  <input type="button" class="btn btn-success upload_file" data-single="1" value="'.$this->parameters['upload_text'].'" />
						  <div class="image_preview">'.( $attach_url ?  $attach_url  : '' ).'</div>
						</div> 
					</div>';	
					break;
					
					case "save":
				 
					$out .= '
					<div class="'.( $this->parameters['width'] ? $this->parameters['width'] : 'col-12' ).'">
						<div class="form-actions">  
							<button type="submit" class="btn btn-primary">'.$this->parameters['title'].'</button>  
						</div> 
					</div>
					';	
					break;
					case "link":
				 
					$out .= '
					<div class="'.( $this->parameters['width'] ? $this->parameters['width'] : 'col-12' ).'">
						<div class="form-actions">  
							<a href="'.$this->parameters['href'].'" class="'.$this->parameters['class'].'">'.$this->parameters['title'].'</a>  
						</div> 
					</div>
					';	
					break;
					
					case "text_out":
				 
					$out .= '
					<div class="'.( $this->parameters['title'] ? $this->parameters['title'] : 'col-12' ).'">
						'.$this->parameters['class'].'
					</div>
					';	
					break;
			}
			$this->content = $out;
		 
		}
		public function  get_code(){
			return $this->content;
		}
	}
}