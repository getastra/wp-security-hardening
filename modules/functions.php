<?php

if (!defined('ABSPATH')) exit ('Peekaboo!');

// Remove versions from scripts
function whp_remove_appended_version_script_style($target_url)
{
    $filename_arr = explode('?', basename($target_url));

    global $exclude_files_arr, $exclude_file_list;
    // first check the list of user defined excluded CSS/JS files

    /* Remove the "ver=" argument from the url */
    if (strpos($target_url, 'ver=')) {
        $target_url = remove_query_arg('ver', $target_url);
    }
    /* Remove the "version=" argument from the url */
    if (strpos($target_url, 'version=')) {
        $target_url = remove_query_arg('version', $target_url);
    }

    return $target_url;
}

function remove_revslider_meta_tag()
{
    return '';
}

//Remove WPML version number
function remove_wpml_generator()
{

    if (!empty ($GLOBALS['sitepress'])) {
        remove_action(
            current_filter(),
            array($GLOBALS['sitepress'], 'meta_generator_tag')
        );
    }
}

// Remove Yoast Debug Mark
function remove_yoast_seo_comments_fn()
{
    if (!class_exists('WPSEO_Frontend') || !method_exists('WPSEO_Frontend', 'get_instance')) {
        return;
    }
    $instance = WPSEO_Frontend::get_instance();

    if (!method_exists($instance, 'debug_mark')) {
        return;
    }
    remove_action('wpseo_head', array($instance, 'debug_mark'), 2);
}


class issuesScanClass
{

    protected $response_results;
    public static $is_astra;

    function __construct()
    {
        $this->response_results = array();
    }

    public function run_issues_check()
    {
        $this->check_wordpress_update();
        $this->get_outdated_plugins();
        $this->check_php_version();
        $this->check_db_password(DB_PASSWORD);
        $this->firewall_check();
        $this->check_permissions();

        update_option('whp_scan_results', $this->response_results);
        update_option('whp_scan_results_time', current_time('timestamp'));
    }





   public function wp_check_php_version() {
        $version = phpversion();
        $key     = md5( $version );

        $response = get_site_transient( 'php_check_' . $key );
        if ( false === $response ) {
            $url = 'http://api.wordpress.org/core/serve-happy/1.0/';
            if ( wp_http_supports( array( 'ssl' ) ) ) {
                $url = set_url_scheme( $url, 'https' );
            }

            $url = add_query_arg( 'php_version', $version, $url );

            $response = wp_remote_get( $url );

            if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
                return false;
            }


            $response = json_decode( wp_remote_retrieve_body( $response ), true );

            if ( ! is_array( $response ) ) {
                return false;
            }

            set_site_transient( 'php_check_' . $key, $response, WEEK_IN_SECONDS );
        }

        if ( isset( $response['is_acceptable'] ) && $response['is_acceptable'] ) {


            $response['is_acceptable'] = (bool) apply_filters( 'wp_is_php_version_acceptable', true, $version );
        }

        return $response;
    }




    public function check_php_version()
    {

        $version = explode('.', PHP_VERSION);
        $msg = '';
        $status = 'bad';

        // Sets up PHP versions and dates.
        $php_versions = array(
            '5.0' => array(
                'release' => 'July 13, 2004',
                'eol' => 'September 5, 2005',
            ),
            '5.1' => array(
                'release' => 'November 24, 2005',
                'eol' => 'August 24, 2006',
            ),
            '5.2' => array(
                'release' => 'November 2, 2006',
                'eol' => 'January 6, 2011',
            ),
            '5.3' => array(
                'release' => 'June 30, 2009',
                'eol' => 'August 14, 2014',
            ),
            '5.4' => array(
                'release' => 'March 1, 2012',
                'eol' => 'September 3, 2015',
            ),
            '5.5' => array(
                'release' => 'June 20, 2013',
                'eol' => 'July 21, 2016',
            ),
            '5.6' => array(
                'release' => 'August 28, 2014',
                'eol' => 'December 31, 2018',
            ),
            '7.0' => array(
                'release' => 'December 3, 2015',
                'eol' => 'December 2, 2018',
            ),
            '7.1' => array(
                'release' => 'December 1, 2016',
                'eol' => 'December 1, 2019',
            ),
            '7.2' => array(
                'release' => 'November 30, 2017',
                'eol' => 'November 30, 2020',
            ),
            '7.3' => array(
                'release' => 'December 6, 2018',
                'eol' => 'December 6, 2021',
            ),
            '7.4' => array(
                'release' => 'December 6, 2018',
                'eol' => 'December 6, 2025',
			),
            '8.0' => array(
                'release' => 'November 26, 2020',
                'eol' => 'November 26, 2023'
            ),
			'8.1' => array(
                'release' => 'November 25, 2021',
                'eol' => 'November 25, 2024'
            ),
        );

        $error = __('Error checking PHP health.', 'whp');
        if (!is_array($version) || count($version) < 2) {
            return $this->prepare_array($error, $status, 'php_version', PHP_VERSION);
        }
        $site_version = $version[0] . '.' . $version[1];


        $eol_time = strtotime($php_versions[$site_version]['eol']);
        $today = time();
        $eol_suggest_time = $today + 60*60*24*180;

        $data = $this->wp_check_php_version();
        $string = $data['recommended_version'];
        $system =  phpversion();
        $ststemversionExp  = explode('.', $system);
        array_pop($ststemversionExp);
        $system= implode('.', $ststemversionExp);

        if ($eol_time < $today) {
            // If EOL is passed, show unsupported message.
            $unsupported_version_message = sprintf(__('Your server is running PHP version %1$s which has not been supported since %2$s.', 'whp'), $site_version, $php_versions[$site_version]['eol']);
            $unsupported_message = __('Using an unsupported version of PHP means that you are using a version that no longer receives important security updates and fixes. Also, newer versions are faster which makes your site load faster. You must update your PHP or contact your host immediately!', 'whp');

            $this->response_results['php_version'] = array(
                'status' => 'error',
                'message' => $unsupported_version_message,
                'details' => $unsupported_message,
            );

        } else if ($eol_time < $eol_suggest_time) {
            // If EOL is coming up within the next 180 days, show expiring soon message.
            $expiring_version_message = sprintf(__('Your server is running PHP version %1$s which is going to expire in %2$s.', 'whp'), $site_version, $php_versions[$site_version]['eol']);
            $security_ending_message = __('Be sure to check with your host to make sure they have a plan to update before the security support ends.', 'whp');
            $this->response_results['php_version'] = array(
                'status' => 'error',
                'message' => $expiring_version_message,
                'details' => $security_ending_message,
            );

        } else if ($system < $string) {
            // If the php version is lower than suggested version, show outdated message.
            $this->response_results['php_version'] = array(
                'status' => 'error',
                'message' => sprintf(__('Your current PHP version (%s) is outdated and can invite hackers.', 'whp'), $system),
                'details' => sprintf(__('Move to the latest and secured version (%s) with this <a href="https://www.getastra.com/blog/cms/wordpress-security/wordpress-security-guide/#3-Update-your-PHP-to-the-latest-version">guide</a> here.', 'whp'), $data['recommended_version']),
            );

        } elseif ($system >= $string) {
            // If the php version is higher or equal than suggested version, show success message.
            $this->response_results['php_version'] = array(
                'status' => 'success',
                'message' => __('Hurray! Your PHP version is up to date!', 'whp'),
                'details' => sprintf(__('PHP version %s is recognized as the most secured version as of now. ', 'whp'), $site_version),
            );
        }


    }

    public function get_outdated_plugins()
    {

        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        $plugins_list = get_plugins();

        $plugin_info = get_site_transient('update_plugins');

        $plugins = array();
        if (isset($plugin_info->response)) {
            foreach ($plugin_info->response as $plugin) {

                $plugin->title = $plugins_list[$plugin->plugin]['Name'];
                $plugin->upgrade = true;
                $plugins[$plugin->slug] = $plugin;
            }
        }


        if (count($plugins) === 0) {
            $this->response_results['inactive_plugins'] = array(
                'status' => 'success',
                'message' => 'Great! All your plugins are running on the latest versions.',
                'details' => 'All plugins are running the latest versions.'
            );
        } else {

            $list_of_plugins = array();

            foreach ($plugins as $k => $v) {
                $names[] = $v->title;
            }

            $this->response_results['inactive_plugins'] = array(
                'status' => 'error',
                'message' => __('Outdated plugins were detected on your website. Update them to the latest version to stay secure!', 'whp'),
                'details' => sprintf(__('Plugins (%s) require immediate update. Follow this <a href="%s">link</a>  to update now.', 'whp'), implode(', ', $names), admin_url('/plugins.php?plugin_status=upgrade'))
            );
        }

    }

    public function check_wordpress_update()
    {
        $local_version = get_bloginfo('version');

        $url = 'https://api.wordpress.org/core/version-check/1.7/';
        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            return false;
        }

        $json = $response['body'];
        $obj = json_decode($json);

        $upgrade = $obj->offers[0];
        $current_version = $upgrade->version;


        $res = version_compare($local_version, $current_version);

        //local < current
        if ($res === -1) {
            $this->response_results['wp_version'] = array(
                'status' => 'error',
                'message' => __('You need to update WordPress to latest version', 'whp'),
                'details' => sprintf(__('An older WordPress version was detected on your Website. Update it ASAP to keep yourself secure. You need to update WordPress to %s. Your current version is: %s', 'whp'), $current_version, $local_version),
            );
        } else {
            $this->response_results['wp_version'] = array(
                'status' => 'success',
                'message' => __('Bravo! Your WordPress Version is up to date.', 'whp'),
                'details' => sprintf(__('Your website is running the most secure version ( %s ) of WordPress.', 'whp'), $current_version),
            );
        }


    }

    public function check_db_password($pwd)
    {

        $errors = array();

        if (strlen($pwd) < 8) {
            $errors[] = __("Password too short!", 'whp');
        }

        if (!preg_match("#[0-9]+#", $pwd)) {
            $errors[] = __("Password must include at least one number!", 'whp');
        }

        if (!preg_match("#[a-zA-Z]+#", $pwd)) {
            $errors[] = __("Password must include at least one letter!", 'whp');
        }


        if (count($errors) == 0) {
            $this->response_results['db_strength'] = array(
                'status' => 'success',
                'message' => __('Good job using strong passwords for your database.', 'whp'),
                'details' => __('You are following good password practices for your website. We recommend that you change your passwords often.', 'whp'),
            );
        } else {
            $this->response_results['db_strength'] = array(
                'status' => 'error',
                'message' => __('Sorry! The current database password is not strong. Try something more secure?', 'whp'),
                'details' => __('Change to a stronger Password. Take help from this   <a href="https://www.getastra.com/blog/knowledge-base/create-safe-and-secure-passwords/" target="_blank">guide</a>  to create strong passwords for your website.', 'whp'),
            );
        }

    }

    public static function is_firewall_installed()
    {
        $firewalls_slugs = array(
            'getastra/astra-security.php',
            'astra_wp/astra_wp.php',
            'astra_tc/astra_tc.php',
            'wordfence/wordfence.php',
            'wp-cerber/wp-cerber.php',
            'better-wp-security/better-wp-security.php',
        );

        $active_plugins = get_option('active_plugins');

        $has_firewall = 0;
        foreach ($active_plugins as $s_plugin) {


            if (in_array($s_plugin, $firewalls_slugs)) {

                if ($s_plugin == 'getastra/astra-security.php') {
                    self::$is_astra = 1;
                }

                return true;
            }
        }

        return false;
    }


    public function firewall_check()
    {


        if (!$this->is_firewall_installed()) {
            $this->response_results['has_firewall'] = array(
                'status' => 'error',
                'message' => __('Oops! We were not able to detect any WordPress security plugin on your website. ', 'whp'),
                'details' => __('<a target="_blank" href="https://wordpress.org/plugins/getastra/">Astra Firewall</a> leverages continuous and comprehensive protection to your website. Astra firewall stops attacks like XSS, SQLi, LFI, RFI, Bad bots & 100+ type of security threats in real time.', 'whp'),
            );
            return false;
        } else {
            if (self::$is_astra == 1) {
                $this->response_results['has_firewall'] = array(
                    'status' => 'success',
                    'message' => __('Oh wow! You are well-protected by Astra!', 'whp'),
                    'details' => __('Firewalls are a great way to monitor & protect your website against hacks. But, of course you know that :-) ', 'whp'),
                );
            } else {
                $this->response_results['has_firewall'] = array(
                    'status' => 'success',
                    'message' => __('Nice! You have a firewall installed.', 'whp'),
                    'details' => __('Firewalls are a great way to monitor & protect your website against hacks. But, of course you know that :-) ', 'whp'),
                );
            }

            return true;
        }


    }

    public function check_permissions()
    {
        global $level;

        $level = 0;
        $result = $this->getDirContents(ABSPATH);

        if (count($result) == 0) {
            $this->response_results['file_permission'] = array(
                'status' => 'success',
                'message' => __('Correct file permissions are in place.', 'whp'),
                'details' => __('File permissions ensure privacy as well as security of your website. Glad you know that too :-)', 'whp'),
            );
        } else {
            $out_lines = array();
            foreach ($result as $s_row) {
                $out_lines[] = "<tr><td>" . $s_row['permissions'] . '</td><td>' . $s_row['path'] . '</td>';
            }
            $this->response_results['file_permission'] = array(
                'status' => 'error',
                'message' => __('Poor file & folder permissions detected.', 'whp'),
                'details' => __('Managing and securing file permission should not be overlooked. This <a href="https://www.getastra.com/blog/cms/wordpress-security/fixing-wordpress-file-permissions/" target="_blank" >guide</a> will help you secure the recommended file permissions on your WordPress. <br/><br/><table class="table table-responsive table-striped"><thead><tr><th>Current Permission</th><th>File Path</th></tr></thead><tbody>' . implode('', $out_lines) . '</tbody></table>', 'whp'),
            );
        }


    }

    private function getDirContents($dir, &$results = array())
    {
        global $level;


        $files = scandir($dir);
        $level = (int)$level + 1;
        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);

            if (!is_dir($path)) {


                $perms = (int)substr(sprintf('%o', fileperms($path)), -4);

                if ($perms > 664) {
                    $results[] = array('path' => $path, 'permissions' => $perms);
                }

            } else if ($value != "." && $value != "..") {

                $edited_path = rtrim(ABSPATH, '/');
                $edited_path = rtrim($edited_path, '\\');

                $res_path = str_replace($edited_path, '', $path);
                $res_path = ltrim($res_path, '\\');
                $res_path = ltrim($res_path, '/');

                $level = substr_count($res_path, '\\');

                if ($level >= 2) {
                    return true;
                }

                $this->getDirContents($path, $results);

                $perms = (int)substr(sprintf('%o', fileperms($path)), -4);
                if ($perms > 775) {
                    $results[] = array('path' => $path, 'permissions' => $perms);
                }
            }
        }

        return $results;
    }
}


class tableViewOutput
{
    public $last_results;
    protected $last_results_time;
    protected $issues_list;

    protected $out_success = array();
    protected $out_error = array();
    protected $table;

    function __construct()
    {

        $this->issues_list = array(
            'wp_version' => array(
                'test_name' => __('WordPress version check', 'whp'),
                'title' => __('Wrong WP version', 'whp'),
                'text' => __('some issue description', 'whp'),
                'level' => 'high',
                'weight' => 8
            ),
            'inactive_plugins' => array(
                'test_name' => __('WordPress plugin version check', 'whp'),
                'title' => __('Inactive plugins', 'whp'),
                'text' => __('some issue description', 'whp'),
                'level' => 'high',
                'weight' => 8
            ),
            'php_version' => array(
                'test_name' => __('Check for active PHP version', 'whp'),
                'title' => __('Old PHP Version', 'whp'),
                'text' => __('some issue description', 'whp'),
                'level' => 'medium',
                'weight' => 5
            ),
            'db_strength' => array(
                'test_name' => __('Database Password Strength', 'whp'),
                'title' => __('DB strength', 'whp'),
                'text' => __('some issue description', 'whp'),
                'level' => 'medium',
                'weight' => 5
            ),
            'has_firewall' => array(
                'test_name' => __('Firewall Status', 'whp'),
                'title' => __('Has Firewall', 'whp'),
                'text' => __('some issue description', 'whp'),
                'level' => 'recommendation',
                'weight' => 6
            ),
            'file_permission' => array(
                'test_name' => __('File Permission Checker', 'whp'),
                'title' => __('File Permission', 'whp'),
                'text' => __('some issue description', 'whp'),
                'level' => 'recommendation',
                'weight' => 4
            ),


        );

        $this->last_results = get_option('whp_scan_results', array());
        $this->last_results_time = get_option('whp_scan_results_time');

        add_action( 'admin_notices', array($this,'remove_localstorage') );

    }


public function remove_localstorage()
    {


        if(isset($_REQUEST['activate']) && $_REQUEST['activate']=='true')
        {
        ?>
       <script type="text/javascript">
        localStorage.setItem("wphShowAdminPrompt", '');
        location.reload();
    </script>

    <?php
}
    }


    public function return_status($slug)
    {
        return $this->issues_list[$slug]['level'];
    }

    public function return_status_html($slug)
    {
        return ucfirst($this->issues_list[$slug]['level']);
    }


    function check_exists()
    {
        $res = get_option('whp_scan_results');
        if ($res && $res != '') {
            return true;
        } else {
            return false;
        }
    }

    function calculate_site_health()
    {

        $total_points = 0;
        foreach ($this->issues_list as $k => $v) {
            $total_points = $total_points + $v['weight'];
        }

        $current_points = 0;
        $this->last_results = get_option('whp_scan_results');
        if (count((array)$this->last_results) > 0)
            foreach ((array)$this->last_results as $key => $value) {
                if ($value['status'] == 'success') {
                    $current_points = $current_points + $this->issues_list[$key]['weight'];
                }
            }
        $pers = (int)($current_points * 100 / $total_points);
        return $pers;
    }

    function get_recommendations_amount()
    {
        return count($this->out_error);
    }

    function process_results()
    {

        foreach ((array)$this->last_results as $key => $value) {


            if ($value['status'] == 'error') {
                $this->out_error[] = '
				<div class="row single_status_block">

					<div class="issue_name">
						<div class="test_name">' . $this->issues_list[$key]['test_name'] . '</div>
						<div class="test_message">' . $value['message'] . '</div>
					</div>
					<div class="issue_status">
						<div class="' . $this->return_status($key) . '_issue">' . $this->return_status_html($key) . '</div>
					</div>
					<div class="row_control">
						<div class="hide_control">' . __('Hide', 'whp') . ' <i class="fa fa-chevron-up" aria-hidden="true"></i></div>
						<div class="show_control">' . __('Details', 'whp') . ' <i class="fa fa-chevron-down" aria-hidden="true"></i></div>
					</div>
					<div class="details_block"><strong class="steps_fix">Steps to Fix:</strong>
					' . $value['details'] . '
					</div>
				</div>
				';
            }
            if ($value['status'] == 'success') {
                $this->out_success[] = '
				<div class="row single_status_block">
					<div class="issue_name">
						<div class="test_name">' . $this->issues_list[$key]['test_name'] . '</div>
						<div class="test_message">' . $value['message'] . '</div>
					</div>
					<!--
					<div class="issue_status">
						<div class="critical_issue">' . __('Critical', 'whp') . '</div>
					</div>
					<div class="row_control">
						<div class="hide_control">' . __('Hide', 'whp') . ' <i class="fa fa-chevron-up" aria-hidden="true"></i></div>
						<div class="show_control">' . __('Details', 'whp') . ' <i class="fa fa-chevron-down" aria-hidden="true"></i></div>
					</div>
					-->
				</div>
				';
            }
        }


    }

    public function generate_widget_list($limit = 3)
    {


        $list_messages = array();
        $cnt = 0;
        foreach ($this->last_results as $key => $value) {
            if ($value['status'] == 'success') {
                continue;
            }

            if ($cnt >= $limit) {
                break;
            }
            $list_messages[] = '<li class="ov_hidden widget_row"> <span class="warning_sign pull-left margin_hor_10" ><i class="fa fa-exclamation-triangle icon_orange" aria-hidden="true"></i></span> <b>' . $this->issues_list[$key]['test_name'] . '</b> ' . $value['message'] . '<span class="warning_sign pull-right margin_hor_10" ><a href="' . admin_url('/admin.php?page=wphwp_harden#audit_bottom_block') . '"><i class="fa fa-chevron-right" aria-hidden="true"></i></a></span></li>';
            $cnt++;
        }

        if (count($list_messages) == 0) {
            $list_messages = array('<li>' . __('Your site is well protected', 'whp') . '</li>');
        }

        return '<ul class="tw-bs4">' . implode('', $list_messages) . '</ul>';
    }


    public function generate_table_view()
    {


        $total = count((array)$this->out_error) + count((array)$this->out_success);

        if ($total === 0) {
            $completed = 0;
        } else {
            $completed = (int)(count($this->out_success) * 100 / $total);
        }


        if ($this->last_results == '' || !$this->last_results) {
            $this->table = '

			';
        } else {
            $extra_class = '';
            if (count($this->out_error) == 0) {
                $extra_class = 'is_zero';
            }
            $this->table = '
			<div class="div_white_cont" >
				<div class="row_block">
					<div class="single_line_block gray_block">
						<img src="' . plugins_url('/images/icons8-charging-battery.svg', __FILE__) . '" /> ' . sprintf(__('%s Site Health', 'whp'), '<span class="font_20"><b>' . $this->calculate_site_health() . '<small>%</small></b></span>') . '
					</div>
					<div class="single_line_block orange_block ' . $extra_class . '">
						<img src="' . plugins_url('/images/shape.svg', __FILE__) . '" /><span class="font_20"><b>' . count($this->out_error) . '</b></span>' . __(' recommendations', 'whp') . ' <a href="#audit_bottom_block" id="view_res_link">' . __('View Results', 'whp') . ' <i class="fa fa-chevron-right" aria-hidden="true"></i></a>
					</div>
					<div class="single_line_block gray_block">
						<img src="' . plugins_url('/images/success.svg', __FILE__) . '" /> <span class="font_20"><b>' . $completed . '<small>%</small></b></span> ' . __('Passed Tests', 'whp') . '
					</div>
					<div class="text_row">
						' . __('Last Audit on ', 'whp') . date('M d, h:i A', $this->last_results_time) . '
					</div>
				</div>
			</div>
			';
        }


        return $this->table;

    }

    public static function get_processed_fixers()
    {
        $settings = get_option('whp_fixer_option');

        $is_on = 0;
        $is_off = 0;

        foreach ((array)$settings as $k => $v) {
            if ($v == 'on') {
                $is_on++;
            }
            if ($v == 'off') {
                $is_off++;
            }
        }
        return array('on' => $is_on, 'off' => $is_off);
    }

    public static function get_hard_percent()
    {
        $settings = get_option('whp_fixer_option');

        $is_on = 0;
        $is_off = 0;

        foreach ($settings as $k => $v) {
            if ($v == 'on') {
                $is_on++;
            }
            if ($v == 'off') {
                $is_off++;
            }
        }


        return array('on' => $is_on, 'off' => $is_off);
    }

    function return_data()
    {

        return array('success' => implode('', (array)$this->out_success), 'error' => implode('', (array)$this->out_error), 'table' => $this->table);
    }
}


class WHP_Change_Login_URL
{
    private $wp_login_php;

    private function basename()
    {
        return plugin_basename(__FILE__);
    }

    private function path()
    {
        return trailingslashit(dirname(__FILE__));
    }

    private function use_trailing_slashes()
    {
        return '/' === substr(get_option('permalink_structure'), -1, 1);
    }

    private function user_trailingslashit($string)
    {
        return $this->use_trailing_slashes() ? trailingslashit($string) : untrailingslashit($string);
    }

    private function wp_template_loader()
    {
        global $pagenow;

        $pagenow = 'index.php';

        if (!defined('WP_USE_THEMES')) {
            define('WP_USE_THEMES', true);
        }

        wp();

        if ($_SERVER['REQUEST_URI'] === $this->user_trailingslashit(str_repeat('-/', 10))) {
            $_SERVER['REQUEST_URI'] = $this->user_trailingslashit('/wp-login-php/');
        }

        require_once ABSPATH . WPINC . '/template-loader.php';

        die;
    }

    private function new_login_slug()
    {
        if (
            ($slug = get_option('whp_admin_page')) || (
                is_multisite() &&
                is_plugin_active_for_network($this->basename()) &&
                ($slug = get_site_option('whp_admin_page', 'login'))
            ) ||
            ($slug = 'login')
        ) {
            return $slug;
        }
    }

    public function new_login_url($scheme = null)
    {
        if (get_option('permalink_structure')) {
            return $this->user_trailingslashit(home_url('/', $scheme) . $this->new_login_slug());
        } else {
            return home_url('/', $scheme) . '?' . $this->new_login_slug();
        }
    }

    public function __construct()
    {
        register_activation_hook($this->basename(), array($this, 'activate'));
        register_uninstall_hook($this->basename(), array('WHP_Change_Login_URL', 'uninstall'));

        //add_action('admin_init', array($this, 'admin_init'));
        //add_action('admin_notices', array($this, 'admin_notices'));
        //add_action('network_admin_notices', array($this, 'admin_notices'));

        if (is_multisite() && !function_exists('is_plugin_active_for_network')) {
            require_once ABSPATH . '/wp-admin/includes/plugin.php';
        }

        add_filter('plugin_action_links_' . $this->basename(), array($this, 'plugin_action_links'));

        if (is_multisite() && is_plugin_active_for_network($this->basename())) {
            add_filter('network_admin_plugin_action_links_' . $this->basename(), array($this, 'plugin_action_links'));

            add_action('wpmu_options', array($this, 'wpmu_options'));
            add_action('update_wpmu_options', array($this, 'update_wpmu_options'));
        }

        add_action('plugins_loaded', array($this, 'plugins_loaded'), 1);
        add_action('wp_loaded', array($this, 'wp_loaded'));

        add_filter('site_url', array($this, 'site_url'), 10, 4);
        add_filter('network_site_url', array($this, 'network_site_url'), 10, 3);
        add_filter('wp_redirect', array($this, 'wp_redirect'), 10, 2);

        add_filter('site_option_welcome_email', array($this, 'welcome_email'));

        remove_action('template_redirect', 'wp_redirect_admin_locations', 1000);
    }

    public function activate()
    {
        add_option('whp_redirect', '1');
    }

    public static function uninstall()
    {
        global $wpdb;

        if (is_multisite()) {
            $blogs = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");

            if ($blogs) {
                foreach ($blogs as $blog) {
                    switch_to_blog($blog);
                    delete_option('whp_admin_page');
                }

                restore_current_blog();
            }

            delete_site_option('whp_admin_page');
        } else {
            delete_option('whp_admin_page');
        }
    }

    public function wpmu_options()
    {
        echo(
            '<h3>' .
            _x('Rename wp-login.php', 'Text string for settings page', 'whp') .
            '</h3>' .
            '<p>' .
            __('This option allows you to set a network-wide default, which can be overridden by individual sites. Simply go to to the site’s permalink settings to change the url.', 'whp') .
            '</p>' .
            '<table class="form-table">' .
            '<tr valign="top">' .
            '<th scope="row">' .
            __('Networkwide default', 'whp') .
            '</th>' .
            '<td>' .
            '<input id="whp-page-input" type="text" name="whp_admin_page" value="' . get_site_option('whp_admin_page', 'login') . '">' .
            '</td>' .
            '</tr>' .
            '</table>'
        );
    }

    public function update_wpmu_options()
    {
        if (
            ($whp_admin_page = sanitize_title_with_dashes($_POST['whp_admin_page'])) &&
            strpos($whp_admin_page, 'wp-login') === false &&
            !in_array($whp_admin_page, $this->forbidden_slugs())
        ) {
            update_site_option('whp_admin_page', $whp_admin_page);
        }
    }

    public function admin_init()
    {
        global $pagenow;

        add_settings_section(
            'rename-wp-login-section',
            _x('Change Admin Login URL', 'Text string for settings page', 'whp'),
            array($this, 'whp_section_desc'),
            'permalink'
        );

        add_settings_field(
            'whp-page',
            '<label for="whp-page">' . __('Login url', 'whp') . '</label>',
            array($this, 'whp_admin_page_input'),
            'permalink',
            'change-wp-login-section'
        );

        if (isset($_POST['whp_admin_page']) && $pagenow === 'options-permalink.php') {
            if (
                ($whp_admin_page = sanitize_title_with_dashes($_POST['whp_admin_page'])) &&
                strpos($whp_admin_page, 'wp-login') === false &&
                !in_array($whp_admin_page, $this->forbidden_slugs())
            ) {
                if (is_multisite() && $whp_admin_page === get_site_option('whp_admin_page', 'login')) {
                    delete_option('whp_admin_page');
                } else {
                    update_option('whp_admin_page', $whp_admin_page);
                }
            }
        }

        if (get_option('whp_redirect')) {
            delete_option('whp_redirect');

            if (is_multisite() && is_super_admin() && is_plugin_active_for_network($this->basename())) {
                $redirect = network_admin_url('settings.php#whp-page-input');
            } else {
                $redirect = admin_url('options-permalink.php#whp-page-input');
            }

            wp_safe_redirect($redirect);

            die;
        }
    }

    public function whp_section_desc()
    {
        if (is_multisite() && is_super_admin() && is_plugin_active_for_network($this->basename())) {
            echo(
                '<p>' .
                sprintf(
                    __('To set a networkwide default, go to %s.', 'whp'),
                    '<a href="' . esc_url(network_admin_url('settings.php#whp-page-input')) . '">' .
                    __('Network Settings', 'whp') .
                    '</a>'
                ) .
                '</p>'
            );
        }
    }

    public function whp_admin_page_input()
    {
        if (get_option('permalink_structure')) {
            echo '<code>' . trailingslashit(home_url()) . '</code> <input id="whp-page-input" type="text" name="whp_admin_page" value="' . $this->new_login_slug() . '">' . ($this->use_trailing_slashes() ? ' <code>/</code>' : '');
        } else {
            echo '<code>' . trailingslashit(home_url()) . '?</code> <input id="whp-page-input" type="text" name="whp_admin_page" value="' . $this->new_login_slug() . '">';
        }
    }

    public function admin_notices()
    {
        global $pagenow;

        if (!is_network_admin() && $pagenow === 'options-permalink.php' && isset($_GET['settings-updated'])) {
            echo '<div class="notice notice-success is-dismissible"><p>' . sprintf(__('Your login page is now here: %s. Bookmark this page!', 'whp'), '<strong><a href="' . $this->new_login_url() . '">' . $this->new_login_url() . '</a></strong>') . '</p></div>';
        }
    }

    public function plugin_action_links($links)
    {
        if (is_network_admin() && is_plugin_active_for_network($this->basename())) {
            array_unshift($links,
                '<a href="' . esc_url(network_admin_url('settings.php#whp-page-input')) . '">' .
                __('Settings', 'whp') .
                '</a>'
            );
        } elseif (!is_network_admin()) {
            array_unshift($links,
                '<a href="' . esc_url(admin_url('options-permalink.php#whp-page-input')) . '">' .
                __('Settings', 'whp') .
                '</a>'
            );
        }

        return $links;
    }

    public function plugins_loaded()
    {
        global $pagenow;

        load_plugin_textdomain('whp');

        if (
            !is_multisite() && (
                strpos($_SERVER['REQUEST_URI'], 'wp-signup') !== false ||
                strpos($_SERVER['REQUEST_URI'], 'wp-activate') !== false
            )
        ) {
            wp_die(__('This feature is not enabled.', 'whp'), '', array('response' => 403));
        }

        $request = parse_url($_SERVER['REQUEST_URI']);

        if ((
                strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false ||
                untrailingslashit($request['path']) === site_url('wp-login', 'relative')
            ) &&
            !is_admin()
        ) {
            $this->wp_login_php = true;
            $_SERVER['REQUEST_URI'] = $this->user_trailingslashit('/' . str_repeat('-/', 10));
            $pagenow = 'index.php';
        } elseif (
            untrailingslashit($request['path']) === home_url($this->new_login_slug(), 'relative') || (
                !get_option('permalink_structure') &&
                isset($_GET[$this->new_login_slug()]) &&
                empty($_GET[$this->new_login_slug()])
            )) {
            $pagenow = 'wp-login.php';
        }
    }

    public function wp_loaded()
    {
        global $pagenow;

        if (is_admin() && !is_user_logged_in() && !defined('DOING_AJAX')) {
            //wp_die(__('You must log in to access the admin area.', 'whp'), '', array('response' => 403));
        }

        $request = parse_url($_SERVER['REQUEST_URI']);

        if (
            $pagenow === 'wp-login.php' &&
            $request['path'] !== $this->user_trailingslashit($request['path']) &&
            get_option('permalink_structure')
        ) {
            wp_safe_redirect($this->user_trailingslashit($this->new_login_url()) . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
            die;
        } elseif ($this->wp_login_php) {
            if (
                ($referer = wp_get_referer()) &&
                strpos($referer, 'wp-activate.php') !== false &&
                ($referer = parse_url($referer)) &&
                !empty($referer['query'])
            ) {
                parse_str($referer['query'], $referer);

                if (
                    !empty($referer['key']) &&
                    ($result = wpmu_activate_signup($referer['key'])) &&
                    is_wp_error($result) && (
                        $result->get_error_code() === 'already_active' ||
                        $result->get_error_code() === 'blog_taken'
                    )) {
                    wp_safe_redirect($this->new_login_url() . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
                    die;
                }
            }

            $this->wp_template_loader();
        } elseif ($pagenow === 'wp-login.php') {
            global $error, $interim_login, $action, $user_login;

            @require_once ABSPATH . 'wp-login.php';

            die;
        }
    }

    public function site_url($url, $path, $scheme, $blog_id)
    {
        return $this->filter_wp_login_php($url, $scheme);
    }

    public function network_site_url($url, $path, $scheme)
    {
        return $this->filter_wp_login_php($url, $scheme);
    }

    public function wp_redirect($location, $status)
    {
        return $this->filter_wp_login_php($location);
    }

    public function filter_wp_login_php($url, $scheme = null)
    {
        if (strpos($url, 'wp-login.php') !== false) {
            if (is_ssl()) {
                $scheme = 'https';
            }

            $args = explode('?', $url);

            if (isset($args[1])) {
                parse_str($args[1], $args);
                $url = add_query_arg($args, $this->new_login_url($scheme));
            } else {
                $url = $this->new_login_url($scheme);
            }
        }

        return $url;
    }

    public function welcome_email($value)
    {
        return str_replace('wp-login.php', trailingslashit(get_site_option('whp_admin_page', 'login')), $value);
    }

    public function forbidden_slugs()
    {
        $wp = new WP;
        return array_merge($wp->public_query_vars, $wp->private_query_vars);
    }
}
