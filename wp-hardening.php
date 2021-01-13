<?php /** @noinspection PhpUnused */

if (!defined('ABSPATH')) exit ('Peekaboo!');

/*
Plugin Name: WP Hardening
Plugin URI: https://www.getastra.com/
Description: Harden your WordPress security by fixing 20 common security loopholes by just a click.
Version: 1.1.2
Author: Astra Security
Author URI: https://www.getastra.com/
Stable tag: 1.1.2
*/


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

 
          
            


            //register_activation_hook(__FILE__, array( $this, 'set_cron') );
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
                wp_schedule_event(time(), 'daily', 'whp_task_hook');
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
    $init_array = array(
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
    );

    update_option('whp_fixer_option', $init_array);

    update_site_option('whp_admin_page', 'login');
    update_option('whp_admin_page', 'login');

    // hide wp-in
    if (is_writable(ABSPATH . "wp-includes")) {
        $handle = fopen(ABSPATH . "wp-includes/index.php", "w");
        fclose($handle);
    }

}


?>