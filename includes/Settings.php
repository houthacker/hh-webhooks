<?php
namespace HWH;

class Settings {

    public function init_settings() {
        // Contais space-delimited list of accepted secrets in HTTP headers.
        // If a request does not contain a header with one of those secrets,
        // oir it's sent in an usupported header, access will be denied.
        \register_setting('hwh-order-forwarding', 'hwh-accepted-secrets');

        // Private key for communication with WooCommerce REST API
        \register_setting('hwh-order-forwarding', 'hwh-wc-private');

        // Private key for communication with WooCommerce REST API
        \register_setting('hwh-order-forwarding', 'hwh-wc-public');

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

    public function render_section_header($args) {
        echo '<div class="wrap">';
        echo '<p>' . \apply_filters( 'the_title', $args['title']) . '</p>';
        echo '</div>';
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
}
