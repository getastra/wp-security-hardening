<?php

if (!defined('ABSPATH')) exit ('Peekaboo!');

add_action('wp_ajax_secure_subscribe', 'whp_secure_subscribe');


function whp_secure_subscribe()
{
    global $current_user, $wpdb;
    if (check_ajax_referer('ajax_call_nonce', 'security')) {

        $email = sanitize_email($_POST['email']);

        $is_subscribed = whp_subscribe_security_alerts($email);

        update_user_meta($current_user->ID, 'whp_subscribed_email', $email);

        echo json_encode(array('result' => 'success'));

    }
    die();
}

function whp_subscribe_security_alerts($email = '')
{

    $formId = '31';
    $api_path = 'https://go.getastra.com/form/submit?formId=' . $formId;

    include_once('gaIP.php');

    $gaIP = new Astra_ip();
    $current_ip = $gaIP->get_ip_address();

    $user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

    $data = array(
        'formId' => $formId,
        'email' => $email,
        'website_url' => get_site_url(),
    );

    $current_user = wp_get_current_user();
    if ($current_user->exists() && isset($current_user->user_firstname)) {
        $data['f_name'] = $current_user->user_firstname;
    }

    $data = array('mauticform' => $data);


    $response = wp_remote_post($api_path, array(
            'method' => 'POST',
            'timeout' => 20,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(
                'X-Forwarded-For' => $current_ip,
                'User-Agent' => $user_agent,
            ),
            'body' => $data,
        )
    );

    if (is_wp_error($response)) {
        return $response->get_error_message();
    }

    return $response;
}

add_action('wp_ajax_no_subscribe', 'whp_no_subscribe');
function whp_no_subscribe()
{
    global $current_user, $wpdb;
    if (check_ajax_referer('ajax_call_nonce', 'security')) {

        update_user_meta($current_user->ID, 'hide_secure_subs', 1);

        echo json_encode(array('result' => 'success'));

    }
    die();
}

add_action('wp_ajax_start_audit', 'whp_start_audit');
function whp_start_audit()
{
    global $current_user, $wpdb;
    if (check_ajax_referer('ajax_call_nonce', 'security') && current_user_can('administrator')) {

        // make scan
        $tnp = new issuesScanClass();
        $tnp->run_issues_check();

        // return results
        $tmp = new tableViewOutput();
        $tmp->process_results();
        $tmp->generate_table_view();
        $res_array = $tmp->return_data();

        echo json_encode(array(
            'result' => 'success',
            'error' => $res_array['error'],
            'success' => $res_array['success'],
            'table' => $res_array['table']
        ));

    }
    die();
}

// process fixers
add_action('wp_ajax_process_fixer', 'whp_process_fixer');
function whp_process_fixer()
{
    global $current_user, $wpdb;

    if (check_ajax_referer('ajax_call_nonce', 'security') && current_user_can('administrator')) {

        // make scan
        $id = sanitize_text_field($_POST['id']);
        $value = sanitize_text_field($_POST['value']);

        $fixer_options = get_option('whp_fixer_option');
        $fixer_options[$id] = $value;

        // echo '<pre>';
        // print_r($fixer_options);

        update_option('whp_fixer_option', $fixer_options);

        switch ($id) {
            case "hide_includes_dir_listing":
                if ($value == 'on') {
                    if (is_writable(ABSPATH . "wp-includes")) {
                        $handle = fopen(ABSPATH . "wp-includes/index.php", "w");
                        fclose($handle);
                    }
                } else {

                    if (is_writable(ABSPATH . "wp-includes/index.php")) {
                        $res = unlink(ABSPATH . "wp-includes/index.php");
                    }

                }
                break;

            // save admin url slug
            case "change_login_url":

                if ($value == 'on') {
                    
                    $admin_url = sanitize_title($_POST['custom_admin_slug']);
                    update_site_option('whp_admin_page', $admin_url);
                    update_option('whp_admin_page', $admin_url);
                } else {

                    $admin_url = sanitize_title($_POST['custom_admin_slug']);
                    delete_site_option('whp_admin_page');
                    delete_option('whp_admin_page');
                }


             


                break;

        }
        $results = tableViewOutput::get_processed_fixers();

        echo json_encode(array(
            'result' => 'success',
            'error' => !empty($results['error']) ? $results['error'] : null,
            'success' => !empty($results['success']) ? $results['success'] : null,
            'table' => !empty($results['table']) ? $results['table'] : null,
            'is_on' => $results['on'],
            'is_off' => $results['off']
        ));

    }
    die();
}