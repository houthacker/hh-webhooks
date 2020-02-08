<?php
/**
 * Plugin Name:         WooCommerce WebHook Processors
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

function hwh_process_webhook($request) {
    $processor = new \WWP\WebHookProcessor();
    return $processor->process($request);
}

function hwh_init_rest_api() {
    register_rest_route('hh-webhooks/v1', '/wc_payment_completed/', array(
        'methods' => \WP_REST_Server::CREATABLE,
        'callback' => 'process_webhook'
    ));
}

function hwh_activate() {
    \hh_log('hh_webhooks activated');  
}

function hwh_deactivate() {
    \hh_log('hh_webhooks deactivated');
}

add_action('rest_api_init', 'hwh_init_rest_api', 10, 0);
register_activation_hook(__FILE__, 'hwh_activate');
register_deactivation_hook(__FILE__, 'hwh_deactivate');

