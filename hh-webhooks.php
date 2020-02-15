<?php
/**
 * Plugin Name:         WooCommerce Order Forwarding
 * Plugin URI:          https://github.com/houthacker/hh-webhooks
 * Description:         Exposes endpoints for WooCommerce WebHooks to call
 * Version:             1.0.0
 * Requires at least:   5.3
 * Requires PHP:        7.2
 * Author:              houthacker
 * Author URI:          https://github.com/houthacker
 * License:             GPL v2
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         hh-webhooks
 * Domain Path:         /languages
 */

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/includes/Settings.php');

function hwh_log($messate) {
}

function hwh_process_webhook($request) {
    $processor = new \HWH\WebHookProcessor();
    return $processor->process($request);
}

function hwh_init_rest_api() {
    register_rest_route('hh-webhooks/v1', '/wc_payment_completed', array(
        'methods' => \WP_REST_Server::CREATABLE,
        'callback' => 'hwh_process_webhook'
    ));
}

function hwh_activate() {
    \hwh_log('hh_webhooks activated');  
}

function hwh_deactivate() {
    \hwh_log('hh_webhooks deactivated');
}

function hwh_init_settings() {
    $settings = new \HWH\Settings();
    $settings->init_settings();
}

function hwh_add_menu() {
    $settings = new \HWH\Settings();
    $settings->add_menu();
}

/**
 * This function adds extra fields which are required to enable
 * automatic order forwarding to EDC.
 * Additionally, the label address_1 in WooCommerce is updated to
 * reflect the added fields.
 */
function hwh_wc_checkout_fields($fields) {
    $fields['address_1']['label'] = 'Straatnaam';
    $fields['address_2']['label'] = 'Huisnummer en extras';
    $fields['address_2']['required'] = true;
    $fields['address_2']['priority'] = 60;

    return $fields;
}

function hwh_forward_order($order) {
    $forward_type = \get_option('hwh-edc-forward-type', 'local-file');
    $edc_email = \get_option('hwh-edc-email', null);
    $edc_api_key = \get_option('hwh-edc-api-key', null);
    $packing_slip_id = \get_option('hwh-edc-packing-slip-id', null);
    $endpoint = \get_option('hwh-edc-endpoint', null);

    $forwarder = new HWH\EDCOrderForwarder($order, $edc_email, $edc_api_key
        , $packing_slip_id, $endpoint);
    $forwarder->forward($forward_type);
}

add_action('rest_api_init', 'hwh_init_rest_api', 10, 0);
add_action('admin_init', 'hwh_init_settings');
add_action('admin_menu', 'hwh_add_menu');
add_action('hwh_ready_for_forwarding', 'hwh_forward_order', 10, 1);
add_filter('woocommerce_default_address_fields', 'hwh_wc_checkout_fields');
register_activation_hook(__FILE__, 'hwh_activate');
register_deactivation_hook(__FILE__, 'hwh_deactivate');

