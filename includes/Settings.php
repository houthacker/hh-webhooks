<?php
namespace HWH;

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

class Settings {

    public function init_settings() {
        // Contais a newline-delimited list of accepted secrets in HTTP headers.
        // If a request does not contain a header with one of those secrets,
        // oir it's sent in an usupported header, access will be denied.
        \register_setting('hwh-order-forwarding', 'hwh-accepted-secrets');

        // Private key for communication with WooCommerce REST API
        \register_setting('hwh-order-forwarding', 'hwh-wc-private');

        // Private key for communication with WooCommerce REST API
        \register_setting('hwh-order-forwarding', 'hwh-wc-public');

        // Hostname for communication with WooCommerce REST API
        \register_setting('hwh-order-forwarding', 'hwh-wc-api-host');

        // Forwarding type: 'local-file' or 'http'
        \register_setting('hwh-order-forwarding', 'hwh-edc-forward-type');

        // Email address as registered at EDC
        \register_setting('hwh-order-forwarding', 'hwh-edc-email');

        // EDC API key
        \register_setting('hwh-order-forwarding', 'hwh-edc-api-key');

        // EDC Endpoint URL
        \register_setting('hwh-order-forwarding', 'hwh-edc-endpoint');

        // EDC Packing slip id 
        \register_setting('hwh-order-forwarding', 'hwh-edc-packing-slip-id');

        \add_settings_section(
            'hwh-accepted-secrets-section',
            __('Order Forwarding', 'default'),
            [ $this, 'render_section_header' ],
            'hwh-order-forwarding'
        );

        \add_settings_section(
            'hwh-wc-section',
            __('WooCommerce Settings', 'default'),
            [ $this, 'render_section_header' ],
            'hwh-order-forwarding'
        );

        \add_settings_section(
            'hwh-edc-section',
            __('EDC Settings', 'default'),
            [ $this, 'render_section_header' ],
            'hwh-order-forwarding'
        );

        \add_settings_field(
            'hwh-accepted-secrets-field',
            __('Accepted Secrets', 'default'),
            [ $this, 'render_accepted_secrets_field' ],
            'hwh-order-forwarding',
            'hwh-accepted-secrets-section',
            [
                'label_for' => 'hwh-accepted-secrets-field',
                'class' => 'hwh-order-forwarding_row'
            ]
        );

        \add_settings_field(
            'hwh-wc-private-field',
            __('WooCommerce REST API Private Key', 'default'),
            [ $this, 'render_wc_private_field' ],
            'hwh-order-forwarding',
            'hwh-wc-section',
            [
                'label_for' => 'hwh-wc-private-field',
                'class' => 'hwh-order-forwarding_row'
            ]
        );

        \add_settings_field(
            'hwh-wc-public-field',
            __('WooCommerce REST API Public Key', 'default'),
            [ $this, 'render_wc_public_field' ],
            'hwh-order-forwarding',
            'hwh-wc-section',
            [
                'label_for' => 'hwh-wc-public-field',
                'class' => 'hwh-order-forwarding_row'
            ]
        );

        \add_settings_field(
            'hwh-wc-api-host-field',
            __('WooCommerce API Host', 'default'),
            [ $this, 'render_wc_api_host_field' ],
            'hwh-order-forwarding',
            'hwh-wc-section',
            [
                'label_for' => 'hwh-wc-api-host-field',
                'class' => 'hwh-order-forwarding_row'
            ]
        );

        \add_settings_field(
            'hwh-edc-forward-type-field',
            __('EDC Forward Type', 'default'),
            [ $this, 'render_edc_forward_type_field' ],
            'hwh-order-forwarding',
            'hwh-edc-section',
            [
                'label_for' => 'hwh-edc-forward-type-field',
                'class' => 'hwh-order-forwarding_row'
            ]
        );

        \add_settings_field(
            'hwh-edc-email-field',
            __('EDC Email Address', 'default'),
            [ $this, 'render_edc_email_field' ],
            'hwh-order-forwarding',
            'hwh-edc-section',
            [
                'label_for' => 'hwh-edc-email-field',
                'class' => 'hwh-order-forwarding_row'
            ]
        );

        \add_settings_field(
            'hwh-edc-api-key-field',
            __('EDC API Key', 'default'),
            [ $this, 'render_edc_api_key_field' ],
            'hwh-order-forwarding',
            'hwh-edc-section',
            [
                'label_for' => 'hwh-edc-api-key-field',
                'class' => 'hwh-order-forwarding_row'
            ]
        );

        \add_settings_field(
            'hwh-edc-packing-slip-id-field',
            __('EDC Packing Slip ID', 'default'),
            [ $this, 'render_edc_packing_slip_id_field' ],
            'hwh-order-forwarding',
            'hwh-edc-section',
            [
                'label_for' => 'hwh-edc-packing-slip-id-field',
                'class' => 'hwh-order-forwarding_row'
            ]
        );

        \add_settings_field(
            'hwh-edc-endpoint-field',
            __('EDC Endpoint URL', 'default'),
            [ $this, 'render_edc_endpoint_field' ],
            'hwh-order-forwarding',
            'hwh-edc-section',
            [
                'label_for' => 'hwh-edc-endpoint-field',
                'class' => 'hwh-order-forwarding_row'
            ]
        );

        $this->init_orders_table();
    }

    public function add_menu() {
        $page_title = 'Order Forwarding';
        $menu_title = 'Order Forwarding';
        $capability = 'manage_options';
        $menu_slug = 'hwh-order-forwarding';
        $callback = [ $this, 'render_settings_page' ];

        \add_menu_page(
            $page_title,
            $menu_title,
            $capability,
            $menu_slug,
            $callback
        );
    }

    public function render_settings_page() {
        if (!\current_user_can('manage_options')) {
            return;
        }

        if (isset($_GET['settings-updated'])) {
            \add_settings_error('hwh-order-forwarding_messages', 'hwh-order-forwarding_message'
                , __('Settings Saved', 'default'), 'updated');
        }

        \settings_errors('hwh-order-forwarding');

        echo '<div class="wrap">';
        echo '<h1>' . \esc_html(\get_admin_page_title()) . '</h1>';
        echo '<form action="options.php" method="POST">';

        \settings_fields('hwh-order-forwarding');
        \do_settings_sections('hwh-order-forwarding');
        \submit_button('Save Settings');

        echo '</form>';
        echo '</div>';
    }

    public function render_accepted_secrets_field($args) {
        $this->render_text_area($args, 'hwh-accepted-secrets', 5, 50);
    }

    public function render_wc_private_field($args) {
        $this->render_input_text($args, 'hwh-wc-private', 50);
    }

    public function render_wc_public_field($args) {
        $this->render_input_text($args, 'hwh-wc-public', 50);
    }

    public function render_wc_api_host_field($args) {
        $this->render_input_text($args, 'hwh-wc-api-host', 50);
    }

    public function render_edc_forward_type_field($args) {
        $options = array(
            '' => '--Select an option--',
            'local-file' => 'Local file',
            'http' => 'HTTP'
        );
        $this->render_select($args, 'hwh-edc-forward-type', $options);
    }

    public function render_edc_email_field($args) {
        $this->render_input_text($args, 'hwh-edc-email', 50);
    }

    public function render_edc_api_key_field($args) {
        $this->render_input_text($args, 'hwh-edc-api-key', 50);
    }

    public function render_edc_packing_slip_id_field($args) {
        $this->render_input_text($args, 'hwh-edc-packing-slip-id', 50);
    }

    public function render_edc_endpoint_field($args) {
        $this->render_input_text($args, 'hwh-edc-endpoint', 50);
    }

    public function render_section_header($args) {
        echo '<div class="wrap">';
        echo '<p>' . \apply_filters( 'the_title', $args['title']) . '</p>';
        echo '</div>';
    }

    private function render_select($args, $name, $options) {
        $setting = \get_option($name);

        echo '<select id="' . $name . '" name="' . $name . '">';
        foreach ($options as $key => $value) {
            $selected = "";
            if (isset($setting) && $setting !== null) {
                if (\strcmp($key, $setting) === 0) {
                    $selected = "selected";
                }
            }

            echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }
        echo '</select>';
    }

    private function render_input_text($args, $name, $size) {
        $setting = \get_option($name);

        if (isset($setting) && $setting !== null) {
            echo '<input name="' . $name . '" type="text" value="' . \esc_html($setting) . '" size="' . $size . '">';
        } else {
            echo '<input name="' . $name . '" type="text" value="" size="' . $size . '">';
        }
    }

    private function render_text_area($args, $name, $rows, $cols) {
        $setting = \get_option($name);

        echo '<textarea name="' . $name . '" rows="' . $rows . '" cols="' . $cols . '">';
        if (isset($setting) && $setting !== null) {
            echo \esc_html($setting);
        }
        echo '</textarea>';
    }

    /**
     * Create the orders table if it doesn't already exist.
     */
    private function init_orders_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'hwh_orders';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name(
            order_id BIGINT(20) NOT NULL PRIMARY KEY,
            order_status ENUM('stored', 'forwarded', 'error'),
            order_status_message TEXT,
            order_xml LONGTEXT
        ) $charset_collate;";

        \dbDelta($sql);        
    }
}
