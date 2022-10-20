<?php /** @noinspection PhpUnused */

if (!defined('ABSPATH')) exit ('Peekaboo!');

/*
Plugin Name: WP Hardening
Plugin URI: https://www.getastra.com/
Description: Harden your WordPress security by fixing 25 common security loopholes by just a click.
Version: 1.2.6
Author: Astra Security
Author URI: https://www.getastra.com/
Stable tag: 1.2.6
*/



$whp_init_array = array(
    'hide_wp_version_number' => 'on',
    'remove_wp_meta_gen_tag' => 'on',
    'remove_wpml_meta_gen_tag' => 'on',
    'remove_revo_slider_meta_gen_tag' => 'on',
    'remove_vc_meta_gen_tag' => 'on',
    'remove_css_meta_gen_tag' => 'on',
    'remove_js_meta_gen_tag' => 'on',
    'stop_user_enumeration' => 'on',
    'change_login_url' => 'off',
    'disable_xml_rpc' => 'on',
    'disable_json_api' => 'on',
    'hide_includes_dir_listing' => 'on',
    'disable_file_editor' => 'on',
    'report_email' => 'off',
    'schedule_audit' => 'on',
    'xss_protection' => 'on',
    'content_sniffing_protection' => 'on',
    'http_secure_flag' => 'on',
    'disable_app_passwords' => 'on',
);

whpDatabaseUpgrade($whp_init_array);
function whpDatabaseUpgrade($defaults = array())
{
    $stored = get_option('whp_fixer_option', array());
    $changes = 0;

    if(!is_array($stored)){
        $stored = array();
    }

    foreach($defaults as $key => $value){
        if(!isset($stored[$key])){
            $stored[$key] = $value;
            $changes++;
        }
    }

    if($changes > 0){
        update_option('whp_fixer_option', $stored);
        update_option('whp_radio_clickjacking_protection', '3');
    }

}

// core initiation
if (!class_Exists('wphMainStart')) {
    class wphMainStart
    {
        public $locale;

        function __construct($locale, $includes, $path)
        {

            $this->locale = $locale;

            // include files
            foreach ($includes as $single_path) {
                include($path . $single_path);
            }
            // calling localization
            add_action('plugins_loaded', array($this, 'myplugin_init'));
            add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'wp_security_hardening_settings_link'));
            add_action('admin_notices', array($this, 'custom_notice_bar'));
            add_action('admin_notices', array($this, 'add_css_plugin'));
            add_action('init', array($this, 'security_header'));


        }

        function security_header()
        {
            if (get_option('whp_xss_protection') == 'on') {
                header("X-XSS-Protection: 1; mode=block");
            }

            if (get_option('whp_content_sniffing_protection') == 'on') {
                header('X-Content-Type-Options: nosniff');
            }

            if (get_option('whp_http_secure_flag') == 'on') {
                @ini_set('session.cookie_httponly', true);
                @ini_set('session.cookie_secure', true);
                @ini_set('session.use_only_cookies', true);
            }


            if (get_option('whp_radio_clickjacking_protection') == '2') {
                header("X-Frame-Options: deny");
            }

            if (get_option('whp_radio_clickjacking_protection') == '3' && !defined('DOING_CRON') ) {
                header("X-Frame-Options: sameorigin");
            }

        }


        function add_css_plugin()
        {
            ?>
            <style type="text/css">

                #activate_plugin_message {
                    justify-content: space-between;
                    align-items: center;
                }


                .notice-img {
                    max-width: 100px;
                    margin-right: 10px;
                }

                #activate_plugin_message a {
                    width: calc(100% - 100px);
                    display: inline-flex;
                    padding: 5px 0;
                    font-size: 120%;
                    line-height: 1.3;
                    font-weight: 600;
                    color: #23282d;
                    text-decoration: none;
                    align-items: center;
                }

                #activate_plugin_message img {
                    max-width: 100%;
                }

                #activate_plugin_message form {
                    text-align: right;
                    width: 100px;
                }

                #activate_plugin_message .button {
                    min-width: 100px;
                }

                @media screen and (max-width: 767px) {

                    #activate_plugin_message {
                        flex-wrap: wrap;
                    }

                    #activate_plugin_message a {
                        width: 100%;
                        font-size: 100%;
                    }

                    .notice-img {
                        max-width: 70px;
                    }

                    #activate_plugin_message form {
                        margin: 10px 0 0 0;
                    }
                }

            </style>
            <?php
        }


// Custom Message when plugin active
        function custom_notice_bar()
        {


            $homeurl = home_url($_SERVER['REQUEST_URI']);
            $getvalue = strpos($homeurl, 'plugins.php');

            if ($getvalue === false) {
            } else {

                ?>
                <div class="notice notice-success" id="activate_plugin_message">

                    <a href="<?php echo admin_url('admin.php?page=wphwp_harden_fixers'); ?>">

                        <div class="notice-img"><img
                                    src="<?php echo esc_url(plugins_url('/modules/images/cta.png', __FILE__)) ?>"></div>
                        <?php _e('We have enabled 18 security fixes to protect your site. Please review them here.', 'wp-security-hardening'); ?>
                    </a>

                    <form>
                        <input class="button button-primary" onclick="localStorage.setItem('wphShowAdminPrompt', '1')" type="submit" name="submit" value="Got It."/>
                    </form>

                </div>
                <script type="text/javascript">
                    var wphShowAdminPrompt = localStorage.getItem("wphShowAdminPrompt");

                    if (wphShowAdminPrompt == '1') {
                        document.getElementById('activate_plugin_message').style.display = 'none';
                    } else {
                        document.getElementById('activate_plugin_message').style.display = 'flex';
                    }
                </script>
                <?php
            }


        }


// Settings links in plugin section
        function wp_security_hardening_settings_link($links)
        {
            $links[] = '<a href="' . admin_url('admin.php?page=wphwp_harden_fixers') . '">' . __('Settings') . '</a>';
            return $links;
        }


        function myplugin_init()
        {
            $plugin_dir = dirname(plugin_basename(__FILE__)) . '/languages';

            load_plugin_textdomain($this->locale, false, $plugin_dir);
        }

        function set_cron()
        {
            // cron to check issues
            wp_clear_scheduled_hook('whp_task_hook');
            if (!wp_next_scheduled('whp_task_hook')) {
                $scheduledoption = get_option('whp_custom_admin_schedule_audit');

                if ($scheduledoption == 'every day') {
                    $scheduleTime = 'daily';
                } elseif ($scheduledoption == 'every week') {
                    $scheduleTime = 'weekly';
                } elseif ($scheduledoption == 'every month') {
                    $scheduleTime = 'monthly';
                } else {
                    $scheduleTime = 'daily';
                }
                wp_schedule_event(time(), $scheduleTime, 'whp_task_hook');
                //wp_schedule_event(time(), 'weekly', 'whp_task_hook');
                //wp_schedule_event(time(), 'monthly', 'whp_task_hook');
            }

        }
    }


}


// initiate main class
new wphMainStart('whp', array(
    'modules/formElementsClass.php',

    'modules/functions.php',
    'modules/scripts.php',
    'modules/hooks.php',
    'modules/ajax.php',
    'modules/settings.php',

), dirname(__FILE__) . '/');

register_activation_hook(__FILE__, 'whp_plugin_activation');
function whp_plugin_activation()
{
    // init fixers
    global $whp_init_array;
    update_option('whp_fixer_option', $whp_init_array);

    update_site_option('whp_admin_page', 'login');
    update_option('whp_admin_page', 'login');

    // hide wp-in
    if (is_writable(ABSPATH . "wp-includes")) {
        $handle = fopen(ABSPATH . "wp-includes/index.php", "w");
        fclose($handle);
    }

    /* Run the first audit */

    $tnp = new issuesScanClass();
    $tnp->run_issues_check();

}



?>
